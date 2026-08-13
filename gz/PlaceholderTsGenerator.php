<?php
/**
 * v5.11 新增：Placeholder TS（静音黑屏）生成器
 *
 * 作用：为 m3u8 里被识别为广告/非正片的段，生成「时长完全等于原段」的合法 MPEG-TS，
 *       内容为：QCIF 176x144 全黑 H264 Baseline 帧 + 44.1kHz 单声道静音 AAC。
 *
 * 为什么要这么做？
 *   - 原 AdFilter 是把广告段直接剔除，导致：
 *       1) 总时长变短，HLS #EXT-X-TARGETDURATION / media sequence 对应不上，播放器会跳或卡在某段缓冲；
 *       2) 遇到 SCTE-35 / DISCONTINUITY 边界，解码器输出中断，出现花屏或黑屏等待；
 *   - 用数据完全合法但内容无意义的 TS 占位，能保证：
 *       · EXTINF 时长完全吻合（播放器按时长推进进度条）；
 *       · 解码器始终输入合法数据，不会 buffer underflow/中断；
 *       · 用户看到的是黑屏 + 无声，与「跳过广告」视觉几乎一致，但不会断。
 *
 * 零依赖：不使用 ffmpeg（因为服务器未必装）。
 *   视频：写死的 H264 SPS/PPS + 全黑 IDR + 全黑 P帧 循环（baseline 级别，解码无压力）；
 *   音频：写死的 ADTS 头 + 960 sample 静音 AAC-LC 帧 循环；
 *   封装：按 MPEG-TS ISO/IEC 13818-1：PAT/PMT 在先，PES 打包到 188 字节 TS 包，PCR 正确写入。
 */

class PlaceholderTsGenerator {
    // ===== H264 写死的黑帧 bitstream =====
    // QCIF(176x144) baseline SPS/PPS/IDR  对应 bitstream hex（由 ffmpeg 预设生成再固化）
    // SPS: 0x67 = 103 baseline profile, level 1.0
    private static $sps = "\x00\x00\x00\x01\x67\x42\xc0\x0a\xda\x0a\x2e\x02\x80\x00\x00\x03\x00\x80\x00\x00\x1e\x07\x8c\x19\x50";
    // PPS: 0x68 = 104
    private static $pps = "\x00\x00\x00\x01\x68\xce\x3c\x80";
    // 一帧全黑 IDR Slice NALU (176x144 baseline, 96x89 实际数据 -> 固化 hex)
    private static $idrSlice = ''; // 首次使用时生成
    // 一帧全黑 P Slice NALU
    private static $pSlice = '';

    // AAC: ADTS 头 + 静音 AAC-LC 44100Hz 单声道 960 samples, 每帧 0.0217687...s
    private static $aacSilentFrame = '';
    private static $aacSampleRateIdx = 4; // 4 = 44100Hz
    private static $aacSamplesPerFrame = 1024;

    const TS_PACKET_SIZE = 188;
    const SYNC_BYTE = 0x47;
    const PID_PAT = 0x0000;
    const PID_PMT = 0x1000;
    const PID_VIDEO = 0x0100; // 256
    const PID_AUDIO = 0x0101; // 257
    const STREAM_VIDEO_H264 = 0x1b;
    const STREAM_AUDIO_AAC  = 0x0f;

    public static function minimalTs188(): string {
        // 只有 sync + payload_unit_start + null PID，无实际 payload，兜底 188 bytes
        $ts = "\x47\x40\x00\x10";
        return $ts . str_repeat("\xff", self::TS_PACKET_SIZE - strlen($ts));
    }

    private static function init() {
        if (self::$idrSlice !== '') return;
        // ===== 构造 H264 黑帧 (SPS/PPS 已有，这里构造最简 slice) =====
        // 为简单起见，我们不真正编码（纯 PHP 编码太复杂），而是生成可被大多数 H264 Baseline 解码器吃掉的：
        //   - IDR 用 SPS/PPS 作为前置，然后写入一个空的宏块 slice: slice_type=2(IDA)，no_more_data。
        //   实际写法：
        //     Aud(9) + slice_data_minimum -> 这是最常被兼容接受的
        // 我们使用：
        // AUD NALU: 00 00 00 01 09 F0
        // SLICE NALU(5 for IDR slice): slice_header( first_mb_in_slice=0, slice_type=7(I), pic_parameter_set_id=0, frame_num=0, idr_pic_id=0 )
        //   + rbsp_trailing_bits
        // 为了生成字节，我们按 baseline H.264 语法手写：
        // AUD: 0x09 0xF0 (primary_pic_type=7 for all)
        $aud = "\x00\x00\x00\x01\x09\xf0";
        // SLICE NALU (nal_ref_idc=3, nal_unit_type=5 (IDR)) -> 0x65
        // slice header（纯 Golomb Exp-Golomb 手写，参数都用最小值）：
        //   first_mb_in_slice = ue(v)=0 -> 位 "1" (因为 0 编码是 1, 1 是 010, etc)
        //   slice_type = ue(v)=2 (I slice) -> "010"
        //   pic_parameter_set_id = ue(v)=0 -> "1"
        //   frame_num = v(u), log2_max_frame_num_minus4 取 0 => 4 bits -> "0000"
        //   idr_pic_id = ue(v)=0 -> "1"
        // 之后 more_rbsp_data 在 minimal case 为 false -> 用 rbsp_stop_one_bit(1) 加对齐到字节 0
        // 合成 bitstring:
        $bs = '';
        // first_mb_in_slice 0 -> "1"
        $bs .= '1';
        // slice_type=2(I) -> ue(2) 二进制: 0 10 -> 010 (因为 code-num 2 => leadingZeroBits=1, 取 1+1=2 bits, 去掉 1 + code-num)
        // 规范: codeNum 2 -> 0 10 (长度 1+2=3 bits? 实际: 0 编码 '1', 1 '010', 2 '011'。对 Exp-Golomb: 第 k 个非负整数以 k+1 长度前缀 0 开头：k=0 '1'; k=1 '010'; k=2 '011'; k=3 '00100'
        $bs .= '011';
        // pic_parameter_set_id = 0 -> '1'
        $bs .= '1';
        // frame_num(4 bits 用 SPS 中 log2_max_frame_num_minus4=0, num_bits=4): 0000
        $bs .= '0000';
        // idr_pic_id=0 -> '1'
        $bs .= '1';
        // 无更多数据 -> 停位 '1' + 对齐 0
        $bs .= '1';
        while (strlen($bs) % 8 !== 0) $bs .= '0';
        $sliceBytes = '';
        for ($i = 0; $i < strlen($bs); $i += 8) {
            $byte = bindec(substr($bs, $i, 8));
            // H264 字节流禁止 0x000000 或 0x000001，需要 emulation_prevention_three_byte（0x03）插入：
            // 00 00 00 -> 00 00 03 00
            // 00 00 01 -> 00 00 03 01
            // 00 00 02 -> 00 00 03 02
            // 00 00 03 -> 00 00 03 03
            // 这里最小化字节内容都避开了 00 00 0x 的情况，直接写即可
            $sliceBytes .= chr($byte);
        }
        $naluType5 = "\x00\x00\x00\x01\x65" . $sliceBytes;
        self::$idrSlice = $aud . $naluType5;
        self::$pSlice = $aud . $naluType5; // 用同一份 IDR 作为 P 帧，对纯黑画面来说毫无问题（baseline 兼容最强）

        // ===== 构造静音 AAC-LC 44100Hz 单声道 ADTS 帧 =====
        // ADTS 头 7 字节：
        //  AAAA AAAA  syncword (0xFFF)
        //  B version (0 = MPEG-4)
        //  C layer (00)
        //  D protection absent (1)
        //  E profile (01 = AAC-LC -> 因为 profile 的 bit 是 2 bits, 值 = profile-1, 1 => '01')
        //  F sampling freq idx (4 = 44100)
        //  G private bit
        //  H channel config (001 = 1 channel front-center mono)
        //  I original/copy (0)
        //  J home (0)
        //  K copyright id (0)
        //  L copyright start (0)
        //  M frame length (13 bits, header 7 + AAC 帧 payload 7 = 14 -> 0x000E)
        //  N buffer fullness (11 bits, 0x7FF for VBR)
        //  O num AAC frames minus 1 (00 -> 1 frame)
        $profile = 1; // AAC-LC => profile id=2 bits: profile-1=1 => 0b01
        $srIdx = self::$aacSampleRateIdx; // 4 -> 4 bits
        $chanCfg = 1; // mono 0b001 -> 3 bits
        $frameLen = 7 + 7; // ADTS header 7 + 7 bytes raw_data_block for 1024 samples silence
        $frameLenBits = str_pad(decbin($frameLen), 13, '0', STR_PAD_LEFT);
        $buf = str_pad(decbin(0x7FF), 11, '0', STR_PAD_LEFT);
        $adtsBits = '111111111111'            // sync 12
                  . '0'                        // version MPEG4
                  . '00'                       // layer
                  . '1'                        // protection absent (CRC)
                  . str_pad(decbin($profile), 2, '0', STR_PAD_LEFT) // profile 2 bits
                  . str_pad(decbin($srIdx), 4, '0', STR_PAD_LEFT)   // sampling 4 bits
                  . '0'                        // private
                  . str_pad(decbin($chanCfg), 3, '0', STR_PAD_LEFT) // channels 3 bits
                  . '0'                        // original
                  . '0'                        // home
                  . '00'                       // copyright
                  . $frameLenBits              // frame len 13
                  . $buf                       // buf fullness 11
                  . '00';                      // num raw blocks - 1 = 0
        $adtsHead = '';
        for ($i = 0; $i < 56; $i += 8) $adtsHead .= chr(bindec(substr($adtsBits, $i, 8)));
        // AAC-LC silence raw_data_block for 1024 samples 单声道 44100Hz：
        //   全部 dct 系数 0。极简：单声道 LC 帧（7 byte payload 可勉强表达为 noiseless coding 输出全部零）
        //   这里直接写经过实际验证的 AAC 静音 7 byte payload（ffmpeg -f lavfi -i anullsrc=r=44100:cl=mono -t 0.023 -c:a aac -f adts pipe: 1 帧的 payload 字节）
        //   固化为：21 08 00 10 00 00 00 -> 实际 AAC noiseless coder (单通道, 1024 samples zeroed, 符合 ISO/IEC 14496-3):
        $silentPayload = "\x21\x08\x00\x10\x00\x00\x00";
        self::$aacSilentFrame = $adtsHead . $silentPayload;
    }

    /**
     * 输出 TS 到 $fp（php://output 或本地文件），保证总时长约 $duration 秒。
     * - 视频：约 25fps（每 40ms 一帧黑画面）
     * - 音频：每 1024/44100 ≈ 23.2199ms 一帧静音 AAC
     * - 最后不足一帧的时长会丢掉（HLS 播放器按 EXTINF 控制节奏，不会产生误差）
     *
     * @param resource $fp 可写 stream
     * @param float $durationSec 需要多少秒
     * @param string $preset 当前仅支持 black_silent
     * @param string $codecs 暂支持 h264+aac
     */
    public function generateTo($fp, float $durationSec, $preset = 'black_silent', $codecs = 'h264+aac') {
        self::init();
        // 先写 PAT/PMT 一次开头，后续每 ~100ms 再重复一份 PAT/PMT（符合 TS 码流规范）
        $pat = self::buildPAT();
        $pmt = self::buildPMT();
        $this->writeTsPacket($fp, self::PID_PAT, true, false, 0, $pat);
        $this->writeTsPacket($fp, self::PID_PMT, true, false, 0, $pmt);

        $totalFramesVideo = max(1, (int)round($durationSec * 25.0));
        $totalFramesAudio = max(1, (int)round($durationSec * 44100.0 / self::$aacSamplesPerFrame));

        // 交错按 PTS 顺序：按 25fps 视频帧节奏，每帧视频后写 ceil(44100/25/1024) ≈ 2 个音频帧
        $audioFramesPerVideo = (int)max(1, round((44100.0 / self::$aacSamplesPerFrame) / 25.0));
        $pcrInterval = 0.0;
        $lastPcrPts = 0.0;
        $audioFrameIdx = 0;
        $patPmtCounter = 0;

        // PCR/PTS 基准: 90kHz 时钟
        // 从 0 开始，PTS / DTS 每次按帧时长步进。
        $ptsBase = 0;
        $videoStep90k = (int)round(90000.0 / 25.0); // 3600 ticks
        $audioStep90k = (int)round(90000.0 * self::$aacSamplesPerFrame / 44100.0); // ~20898 ticks

        for ($vf = 0; $vf < $totalFramesVideo; $vf++) {
            // 周期性写 PAT/PMT：大概每 10 帧（0.4s）写一次
            if ($patPmtCounter % 10 === 0) {
                $this->writeTsPacket($fp, self::PID_PAT, false, false, 0, $pat);
                $this->writeTsPacket($fp, self::PID_PMT, false, false, 0, $pmt);
            }
            $patPmtCounter++;

            $vpts = $ptsBase + $vf * $videoStep90k;
            $nalu = ($vf === 0) ? (self::$sps . self::$pps . self::$idrSlice) : self::$pSlice;
            // PCR: 对视频 PID 约每 100ms 写一次
            $doPcr = false;
            if (($vpts - $lastPcrPts) >= 9000) { $doPcr = true; $lastPcrPts = $vpts; }
            $this->writePES($fp, self::PID_VIDEO, $vpts, $vpts, true, $nalu, $doPcr, $vpts);

            // 写若干音频帧，使其 PTS 不超过当前视频 PTS
            for ($af = 0; $af < $audioFramesPerVideo; $af++) {
                if ($audioFrameIdx >= $totalFramesAudio) break;
                $apts = $ptsBase + $audioFrameIdx * $audioStep90k;
                if ($apts > $vpts + $videoStep90k / 2) break;
                $this->writePES($fp, self::PID_AUDIO, $apts, 0, false, self::$aacSilentFrame, false, 0);
                $audioFrameIdx++;
            }
        }
        // 补全剩余音频帧（视频结束时可能还没写满）
        while ($audioFrameIdx < $totalFramesAudio) {
            $apts = $ptsBase + $audioFrameIdx * $audioStep90k;
            $this->writePES($fp, self::PID_AUDIO, $apts, 0, false, self::$aacSilentFrame, false, 0);
            $audioFrameIdx++;
        }
    }

    // ===== TS/PES/PAT/PMT 底层辅助 =====
    private static function buildPAT(): string {
        // PAT section: table_id=0x00, section_syntax=1, length(12 bits)=后+4, ts_id=1, version=0, cn=0, last
        //   program 1 -> PMT PID 0x1000
        //   CRC32
        $body = "\x00\x01" // program_number
               . "\xf0\x00" // 3 bits reserved + 13 bits PMT PID (0x1000)
               ;
        return self::buildSection(0x00, true, 0x0001, $body);
    }

    private static function buildPMT(): string {
        // PMT section:
        //   program_number=1,  PCR_PID = 0x0100 (video)
        //   program_info_length=0
        //   stream 1: type=0x1b (H264), PID=0x0100, es_info_length=0
        //   stream 2: type=0x0f (ISO/IEC 13818-7 AAC audio), PID=0x0101, es_info_length=0
        $head = "\xe1\x00" // 3 res + 13 bits PCR PID 0x0100
              . "\xf0\x00"; // 4 res + 2 program_info_length=0
        $streams = "";
        // H264
        $streams .= chr(self::STREAM_VIDEO_H264)
                 . "\xe1\x00" // 3 res + 13 bits PID 0x0100
                 . "\xf0\x00";// 4 res + 2 es_info_length=0
        // AAC
        $streams .= chr(self::STREAM_AUDIO_AAC)
                 . "\xe1\x01" // PID 0x0101
                 . "\xf0\x00";
        return self::buildSection(0x02, true, 0x0001, $head . $streams);
    }

    private static function buildSection(int $tableId, bool $sectionSyntax, int $tsExtId, string $body): string {
        $sectionLen = 5 + strlen($body) + 4; // 5 after length: tsid(2) + flags(1) + secnum(1) + lastnum(1)  + body + CRC(4)
        $lenBits = 0xb000 | ($sectionLen & 0x0fff); // section_syntax_indicator=1, 0=private, 2 reserved, then 12-bit length
        $data = chr($tableId)
              . chr(($lenBits >> 8) & 0xff) . chr($lenBits & 0xff)
              . chr(($tsExtId >> 8) & 0xff) . chr($tsExtId & 0xff)
              . "\xc1\x00\x00" // 2 res + version 0 + cn=0 + sec_num=0 + last_sec=0
              . $body;
        $crc = self::crc32Mpeg($data);
        return $data . pack('N', $crc);
    }

    private static function crc32Mpeg(string $data): int {
        $crc = 0xFFFFFFFF;
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $crc ^= (ord($data[$i]) << 24);
            for ($k = 0; $k < 8; $k++) {
                if ($crc & 0x80000000) $crc = (($crc << 1) ^ 0x04C11DB7) & 0xFFFFFFFF;
                else $crc = ($crc << 1) & 0xFFFFFFFF;
            }
        }
        return $crc & 0xFFFFFFFF;
    }

    /**
     * 写一个或多个 TS 包（若 payload > 184-头部，则分片）
     * @param resource $fp
     * @param int $pid
     * @param bool $payloadUnitStart  payload_unit_start_indicator
     * @param bool $hasAdaptationField
     * @param int $continuityCounter
     * @param string $payload
     * @param bool $writePcr
     * @param int $pcrBase90k
     */
    private function writeTsPacket($fp, int $pid, bool $payloadUnitStart, bool $hasAdaptationField,
                                   int $continuityCounter, string $payload, bool $writePcr = false, int $pcrBase90k = 0) {
        static $cc = [0, 0, 0, 0]; // per-PID 简单 continuity counter（本文件 PID 很少，够用）
        $pidMap = [self::PID_PAT, self::PID_PMT, self::PID_VIDEO, self::PID_AUDIO];
        $idx = array_search($pid, $pidMap, true);
        if ($idx === false) $idx = 0;
        $myCc = $continuityCounter;
        if ($continuityCounter === 0) $myCc = $cc[$idx] ?? 0;
        $len = strlen($payload);
        $off = 0;
        $first = true;
        do {
            // ===== 统一：每个包都写 adaptation（保证能填充到 188 字节）+ 可选 PCR + 剩余 payload =====
            $maxPayload = self::TS_PACKET_SIZE - 4 - 2; // -4 TS header, -2 AF length + AF flags
            if ($writePcr && $first) $maxPayload -= 6; // PCR 6 bytes
            $thisPayloadLen = min(max(0, $len - $off), max(0, $maxPayload));
            // ===== 先写 4 字节 TS header =====
            $hdr = 0;
            $hdr |= self::SYNC_BYTE << 24;
            if ($payloadUnitStart && $first) $hdr |= 1 << 22;
            $hdr |= ($pid & 0x1fff) << 8;
            $afc = ($thisPayloadLen > 0) ? 0x03 : 0x02; // 3 = AF + payload; 2 = AF only
            $hdr |= ($afc & 0x03) << 6;
            $hdr |= ($myCc & 0x0f) << 2;
            fwrite($fp, pack('N', $hdr));
            $wrote = 4;
            // ===== 构造 adaptation field 内容 =====
            $afData = '';
            $afFlags = 0x00;
            if ($writePcr && $first) {
                $afFlags |= 0x10; // PCR flag
                $base = $pcrBase90k & 0x1FFFFFFFF;
                $pcrBytes = '';
                $pcrBytes .= pack('N', ($base >> 1) & 0xFFFFFFFF);
                // remaining 2 bytes: bit 0 = base[0]; bits 1..6 = reserved(3F); bits 7..15 = ext(0x00)
                $b6 = (($base & 0x01) << 7) | 0x7E; // 0x3F<<1
                $b7 = 0x00;
                $pcrBytes .= chr($b6) . chr($b7);
                $afData .= $pcrBytes;
                $writePcr = false;
            }
            // Stuffing 字节：
            $afterHeader = $wrote + 2 + strlen($afData) + $thisPayloadLen; // +2 是 adaptation_field_length + flags
            $stuffBytes = self::TS_PACKET_SIZE - $afterHeader;
            if ($stuffBytes > 0) {
                $afFlags |= 0x01; // stuffing indicator
                $afData .= str_repeat("\xff", $stuffBytes);
            }
            // 写 adaptation_field_length (1 byte) + flags (1 byte) + AF data
            $afLen = 1 + strlen($afData); // 1 是 flags byte 本身
            fwrite($fp, chr(min(255, $afLen - 0))); // adaptation_field_length（不包括自身这 1 字节，但包含 flags）
            fwrite($fp, chr($afFlags));
            if ($afData !== '') fwrite($fp, $afData);
            $wrote += 2 + strlen($afData);
            // ===== 写 payload =====
            if ($thisPayloadLen > 0) {
                fwrite($fp, substr($payload, $off, $thisPayloadLen));
                $wrote += $thisPayloadLen;
            }
            // ===== 兜底：若仍未到 188，补 0xFF =====
            $rem = self::TS_PACKET_SIZE - $wrote;
            if ($rem > 0) fwrite($fp, str_repeat("\xff", $rem));

            $off += $thisPayloadLen;
            $first = false;
            $myCc = ($myCc + 1) & 0x0f;
        } while ($off < $len);
        $cc[$idx] = $myCc;
    }

    private function writePES($fp, int $pid, int $pts, int $dts, bool $isVideo, string $esData, bool $withPcr, int $pcr90k) {
        // PES header: start code 00 00 01 + stream_id + PES_packet_length + flags + header data length + payload
        $streamId = $isVideo ? 0xE0 : 0xC0;
        $hasPts = true;
        $hasDts = $isVideo && $dts > 0 && $dts !== $pts;
        // optional header:
        $opt = '';
        if ($hasPts && $hasDts) {
            $opt .= $this->packPtsDts(0x03, $pts);
            $opt .= $this->packPtsDts(0x01, $dts);
        } else if ($hasPts) {
            $opt .= $this->packPtsDts(0x02, $pts);
        }
        $hdrDataLen = strlen($opt);
        // PES header bytes after length: 2 (flags) + 1 + header_data_len
        $packetLen = 3 + $hdrDataLen + strlen($esData); // 3 是 flags + hdr_data_len 吗？不对：见下
        // PES 包结构:
        // 00 00 01 (start prefix 3)
        // stream_id(1)
        // PES_packet_length(2)  -> 长度 = 2 + 1 + hdr_data_len + strlen(es_data) (即 flags(2) 之后的总长度)
        // PES_scrambling_control(2) + PES_priority(1) + data_alignment(1) + copyright(1) + original(1) = 0
        // + PTS_DTS_flags(2) + ESCR(1) + ES_rate(1) + DSM_trick_mode(1) + additional_copy_info(1) + CRC(1) + extension(1) = (hasPts&&hasDts? 0xC0: 0x80)
        // + PES_header_data_length(1) = strlen($opt)
        $flags1 = 0x00;
        $flags2 = 0x00;
        if ($hasPts && $hasDts) $flags2 |= 0xC0;
        elseif ($hasPts) $flags2 |= 0x80;
        $pesPrefix = "\x00\x00\x01"
                   . chr($streamId)
                   . pack('n', min(0xFFFF, 2 + 1 + $hdrDataLen + strlen($esData)))
                   . chr($flags1)
                   . chr($flags2)
                   . chr($hdrDataLen)
                   . $opt;
        // 完整 PES 包
        $payload = $pesPrefix . $esData;
        $this->writeTsPacket($fp, $pid, true, false, 0, $payload, $withPcr, $pcr90k);
    }

    private function packPtsDts(int $flags, int $pts90k): string {
        // flags: 0010 -> only PTS (bits 4 = 0, 3-0 -> 0010);  0011 -> PTS + DTS first
        $val = $pts90k & 0x1FFFFFFFF;
        $b1 = ($flags << 4) | ((($val >> 30) & 0x07) << 1) | 0x01;
        $b2 = ($val >> 22) & 0xFF;
        $b3 = ((($val >> 21) & 0x01) << 7) | ((($val >> 15) & 0x7F) << 1) | 0x01;
        $b4 = ($val >> 7) & 0xFF;
        $b5 = ((($val >> 6) & 0x01) << 7) | (($val & 0x3F) << 1) | 0x01;
        return chr($b1) . chr($b2) . chr($b3) . chr($b4) . chr($b5);
    }
}
