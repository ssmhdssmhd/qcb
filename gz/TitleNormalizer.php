<?php

class TitleNormalizer {
    private static $seasonPatterns = null;
    private static $qualityPatterns = null;
    private static $langPatterns = null;
    private static $versionPatterns = null;
    private static $symbolReplacements = null;

    public static function normalize($title) {
        if (empty($title)) return '';

        $title = trim($title);

        $title = self::normalizeSymbols($title);

        $title = self::normalizeSeason($title);

        $title = self::normalizeQuality($title);

        $title = self::normalizeLanguage($title);

        $title = self::normalizeVersion($title);

        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title);

        return $title;
    }

    public static function getBaseTitle($title) {
        $normalized = self::normalize($title);
        $normalized = preg_replace('/\s*(第[一二三四五六七八九十百千\d]+[季部期篇辑卷]|[上下]部|[全]集|合集|完整版|高清|4K|2K|1080P|720P|蓝光|HD|BD|WEB|S\d+|EP?\d+)\s*$/iu', '', $normalized);
        $normalized = preg_replace('/^\s*(第[一二三四五六七八九十百千\d]+[季部期篇辑卷]|[上下]部)\s+/iu', '', $normalized);
        $normalized = trim($normalized);
        return $normalized;
    }

    public static function getSeasonInfo($title) {
        $seasonNum = null;
        $normalized = self::normalizeSeason($title);

        if (preg_match('/S(\d+)/i', $normalized, $m)) {
            $seasonNum = intval($m[1]);
        } elseif (preg_match('/第(\d+)[季部期篇辑卷]/u', $normalized, $m)) {
            $seasonNum = intval($m[1]);
        }

        return $seasonNum;
    }

    private static function normalizeSeason($title) {
        if (self::$seasonPatterns === null) {
            self::initSeasonPatterns();
        }

        foreach (self::$seasonPatterns as $pattern => $replacement) {
            $title = preg_replace($pattern, $replacement, $title);
        }

        if (preg_match('/第([一二三四五六七八九十]+)季/u', $title, $m)) {
            $num = self::chineseToNumber($m[1]);
            if ($num > 0) {
                $title = str_replace($m[0], '第' . $num . '季', $title);
            }
        }

        if (preg_match('/第([一二三四五六七八九十]+)部/u', $title, $m)) {
            $num = self::chineseToNumber($m[1]);
            if ($num > 0) {
                $title = str_replace($m[0], '第' . $num . '季', $title);
            }
        }

        if (preg_match('/第([一二三四五六七八九十]+)[期篇辑卷]/u', $title, $m)) {
            $num = self::chineseToNumber($m[1]);
            if ($num > 0) {
                $suffix = mb_substr($m[0], -1);
                $title = str_replace($m[0], '第' . $num . '季', $title);
            }
        }

        $title = preg_replace('/第1季/ui', '', $title);
        $title = preg_replace('/(?<=[^\d])S(?=1\D|1$)/i', '', $title);
        $title = preg_replace('/\bS1\b/i', '', $title);
        $title = preg_replace('/\s+/', ' ', $title);

        return $title;
    }

    private static function normalizeQuality($title) {
        if (self::$qualityPatterns === null) {
            self::initQualityPatterns();
        }

        foreach (self::$qualityPatterns as $pattern => $replacement) {
            $title = preg_replace($pattern, $replacement, $title);
        }

        return $title;
    }

    private static function normalizeLanguage($title) {
        if (self::$langPatterns === null) {
            self::initLangPatterns();
        }

        foreach (self::$langPatterns as $pattern => $replacement) {
            $title = preg_replace($pattern, $replacement, $title);
        }

        return $title;
    }

    private static function normalizeVersion($title) {
        if (self::$versionPatterns === null) {
            self::initVersionPatterns();
        }

        foreach (self::$versionPatterns as $pattern => $replacement) {
            $title = preg_replace($pattern, $replacement, $title);
        }

        return $title;
    }

    private static function normalizeSymbols($title) {
        if (self::$symbolReplacements === null) {
            self::initSymbolReplacements();
        }

        foreach (self::$symbolReplacements as $from => $to) {
            $title = str_replace($from, $to, $title);
        }

        $title = preg_replace('/[\(\)\[\]\{\}【】「」『』〈〉《》<>〖〗]/u', ' ', $title);
        $title = preg_replace('/[，。；：！？、·〜～\-—_]/u', ' ', $title);
        $title = preg_replace('/\s+/', ' ', $title);

        return $title;
    }

    private static function initSeasonPatterns() {
        self::$seasonPatterns = [
            '/第(\d+)[季部期篇辑卷]/ui' => '第$1季',
            '/第(\d+)部/ui' => '第$1季',
            '/第(\d+)期/ui' => '第$1季',
            '/第(\d+)篇/ui' => '第$1季',
            '/第(\d+)辑/ui' => '第$1季',
            '/第(\d+)卷/ui' => '第$1季',
            '/第([一二三四五六七八九十百千]+)季/u' => '第${1}季',
            '/\bS(\d+)\b/i' => ' 第$1季 ',
            '/\bSeason\s*(\d+)\b/i' => ' 第$1季 ',
            '/\bEp(\d+)\b/i' => ' EP$1',
            '/\bE(\d+)\b/i' => ' EP$1',
            '/(?<=\s)(\d+)集/ui' => ' EP$1',
            '/Ⅰ/ui' => ' 第1季 ',
            '/Ⅱ/ui' => ' 第2季 ',
            '/Ⅲ/ui' => ' 第3季 ',
            '/Ⅳ/ui' => ' 第4季 ',
            '/Ⅴ/ui' => ' 第5季 ',
            '/Ⅵ/ui' => ' 第6季 ',
            '/Ⅶ/ui' => ' 第7季 ',
            '/Ⅷ/ui' => ' 第8季 ',
            '/Ⅸ/ui' => ' 第9季 ',
            '/Ⅹ/ui' => ' 第10季 ',
        ];
    }

    private static function initQualityPatterns() {
        self::$qualityPatterns = [
            '/4K超高清/ui' => ' 4K ',
            '/蓝光4K/ui' => ' 4K ',
            '/2160P/ui' => ' 4K ',
            '/超高清8K/ui' => ' 8K ',
            '/蓝光2K/ui' => ' 2K ',
            '/2K高清/ui' => ' 2K ',
            '/1440P/ui' => ' 2K ',
            '/1080P高清/ui' => ' 1080P ',
            '/720P清晰/ui' => ' 720P ',
            '/4K\s*HDR/ui' => ' 4K ',
            '/蓝光1080P/ui' => ' 1080P ',
            '/\bUHD\b/i' => ' 4K ',
            '/\bHDrip\b/i' => ' HD ',
            '/\bBDrip\b/i' => ' 蓝光 ',
            '/\bBRrip\b/i' => ' 蓝光 ',
            '/\bWEBRip\b/i' => ' 网络版 ',
            '/\bWEB\b/i' => ' 网络版 ',
            '/\bBD\b/i' => ' 蓝光 ',
            '/\bBluray\b/i' => ' 蓝光 ',
            '/\bHQ\b/i' => ' 高清 ',
            '/高清修复版/ui' => ' 高清 ',
            '/4K修复版/ui' => ' 4K ',
            '/1080P修复版/ui' => ' 1080P ',
            '/2K重制版/ui' => ' 2K ',
            '/1080P重制版/ui' => ' 1080P ',
            '/8K超高清/ui' => ' 8K ',
            '/8K版/ui' => ' 8K ',
            '/4K版/ui' => ' 4K ',
            '/2K版/ui' => ' 2K ',
            '/1080P版/ui' => ' 1080P ',
            '/720P版/ui' => ' 720P ',
            '/480P/ui' => ' 标清 ',
            '/360P/ui' => ' 流畅 ',
            '/\bSD\b/i' => ' 标清 ',
            '/超清/ui' => ' 高清 ',
            '/高动态范围/ui' => ' HDR ',
            '/Dolby Vision/ui' => ' HDR ',
            '/杜比视界/ui' => ' HDR ',
            '/HDR/ui' => ' HDR ',
            '/杜比全景声/ui' => '',
            '/Dolby Atmos/ui' => '',
        ];
    }

    private static function initLangPatterns() {
        self::$langPatterns = [
            '/国语版/ui' => ' 国语 ',
            '/普通话版/ui' => ' 国语 ',
            '/普通话/ui' => ' 国语 ',
            '/中配/ui' => ' 国语 ',
            '/华语/ui' => ' 国语 ',
            '/中文/ui' => ' 国语 ',
            '/内地国语/ui' => ' 国语 ',
            '/大陆版/ui' => ' 国语 ',
            '/国版/ui' => ' 国语 ',
            '/国产篇/ui' => ' 国语 ',
            '/内地版/ui' => ' 国语 ',
            '/台配/ui' => ' 台语 ',
            '/台语版/ui' => ' 台语 ',
            '/闽南语配音/ui' => ' 台语 ',
            '/港版国语/ui' => ' 粤语 ',
            '/港配/ui' => ' 粤语 ',
            '/粤配/ui' => ' 粤语 ',
            '/广东话/ui' => ' 粤语 ',
            '/粤语版/ui' => ' 粤语 ',
            '/英配/ui' => ' 英语 ',
            '/英文/ui' => ' 英语 ',
            '/英语版/ui' => ' 英语 ',
            '/日文配音/ui' => ' 日语 ',
            '/日语版/ui' => ' 日语 ',
            '/韩文配音/ui' => ' 韩语 ',
            '/韩语版/ui' => ' 韩语 ',
            '/泰文配音/ui' => ' 泰语 ',
            '/泰语版/ui' => ' 泰语 ',
            '/越南语/ui' => ' 越南语 ',
            '/越配/ui' => ' 越南语 ',
            '/印尼语/ui' => ' 印尼语 ',
            '/印配/ui' => ' 印尼语 ',
            '/俄语/ui' => ' 俄语 ',
            '/俄配/ui' => ' 俄语 ',
            '/德语/ui' => ' 德语 ',
            '/德配/ui' => ' 德语 ',
            '/法语配音/ui' => ' 法语 ',
            '/法配/ui' => ' 法语 ',
            '/西班牙配/ui' => ' 西班牙语 ',
            '/西语/ui' => ' 西班牙语 ',
            '/藏语配音/ui' => ' 藏语 ',
            '/川配/ui' => ' 四川话 ',
            '/沪配/ui' => ' 上海话 ',
            '/湘配/ui' => ' 湖南话 ',
            '/原声/ui' => ' 原版 ',
            '/原版/ui' => ' 原版 ',
            '/原音/ui' => ' 原版 ',
            '/双语版/ui' => ' 双语 ',
            '/双语配音/ui' => ' 双语 ',
            '/双语音轨/ui' => ' 双语 ',
            '/双语/ui' => ' 双语 ',
            '/内嵌字幕/ui' => ' 内嵌 ',
            '/外挂字幕/ui' => ' 外挂 ',
            '/繁体字幕版/ui' => ' 繁体字幕 ',
            '/简体字幕版/ui' => ' 简体字幕 ',
            '/双语字幕版/ui' => ' 双语字幕 ',
            '/无字幕版/ui' => ' 无字幕 ',
        ];
    }

    private static function initVersionPatterns() {
        self::$versionPatterns = [
            '/特别版/ui' => '',
            '/先行版/ui' => '',
            '/抢先版/ui' => '',
            '/试映版/ui' => '',
            '/重制版/ui' => ' 重制 ',
            '/重置版/ui' => ' 重制 ',
            '/翻新版/ui' => ' 重制 ',
            '/加长版/ui' => ' 加长 ',
            '/扩展版/ui' => ' 加长 ',
            '/完整版2\.0/ui' => ' 完整版 ',
            '/导演终极版/ui' => ' 导演剪辑版 ',
            '/导演最终剪辑版/ui' => ' 导演剪辑版 ',
            '/导演剪辑版/ui' => ' 导演剪辑版 ',
            '/导剪版/ui' => ' 导演剪辑版 ',
            '/网络版/ui' => ' 网络版 ',
            '/线上版/ui' => ' 网络版 ',
            '/流媒体版/ui' => ' 网络版 ',
            '/限定版/ui' => '',
            '/限量版/ui' => '',
            '/特别发行版/ui' => '',
            '/剧场版/ui' => ' 剧场版 ',
            '/院线版/ui' => ' 剧场版 ',
            '/卫视版/ui' => ' TV版 ',
            '/上星版/ui' => ' TV版 ',
            '/全网独播版/ui' => '',
            '/卫视独播/ui' => '',
            '/独家版/ui' => '',
            '/tv版/ui' => ' TV版 ',
            '/dvd版/ui' => ' DVD版 ',
            '/\bTV\b/ui' => ' TV版 ',
            '/\bDVD\b/ui' => ' DVD版 ',
            '/删减版/ui' => '',
            '/未删减版/ui' => ' 完整版 ',
            '/未剪辑版/ui' => ' 完整版 ',
            '/剪辑版/ui' => '',
            '/精简版/ui' => '',
            '/完整版/ui' => ' 完整版 ',
            '/剧场版/ui' => ' 剧场版 ',
            '/动画版/ui' => ' 动画版 ',
            '/真人版/ui' => ' 真人版 ',
            '/电影版/ui' => ' 电影版 ',
            '/剧版/ui' => ' 剧版 ',
            '/漫改版/ui' => '',
            '/小说改/ui' => '',
            '/游戏改/ui' => '',
            '/实拍版/ui' => '',
            '/OVA版/ui' => ' OVA ',
            '/OAD版/ui' => ' OAD ',
            '/SP集/ui' => ' SP ',
            '/\bSP\b/ui' => ' SP ',
            '/特别篇/ui' => ' SP ',
            '/番外篇/ui' => ' 番外 ',
            '/衍生版/ui' => '',
            '/番外衍生版/ui' => '',
            '/续集版/ui' => '',
            '/前传版/ui' => '',
            '/重启版/ui' => '',
            '/改编版/ui' => '',
            '/原创版/ui' => '',
            '/高清重制版/ui' => ' 高清重制 ',
            '/4K重制版/ui' => ' 4K重制 ',
            '/无损版/ui' => '',
            '/高清无损版/ui' => '',
            '/压缩版/ui' => '',
            '/压缩高清版/ui' => '',
            '/简化版/ui' => '',
            '/简化剪辑版/ui' => '',
            '/导演解说版/ui' => '',
            '/演员解说版/ui' => '',
            '/制片人解说版/ui' => '',
            '/编剧解说版/ui' => '',
            '/合集篇/ui' => ' 合集 ',
            '/合集版/ui' => ' 合集 ',
            '/视频合集/ui' => ' 合集 ',
            '/全集/ui' => ' 合集 ',
            '/短篇集/ui' => ' 短篇 ',
            '/漫画画/ui' => ' 漫画 ',
            '/动态漫画/ui' => ' 动态漫 ',
            '/英国篇/ui' => ' 英版 ',
            '/英国版/ui' => ' 英版 ',
            '/美国篇/ui' => ' 美版 ',
            '/美国版/ui' => ' 美版 ',
            '/法国版/ui' => ' 法版 ',
            '/日本篇/ui' => ' 日版 ',
            '/日本版/ui' => ' 日版 ',
            '/港版/ui' => ' 港版 ',
            '/台版/ui' => ' 台版 ',
            '/澳版/ui' => '',
            '/新加坡版/ui' => '',
            '/马来版/ui' => '',
            '/海外版/ui' => '',
            '/国际版/ui' => '',
            '/本土版/ui' => '',
            '/方言版/ui' => '',
            '/字幕版/ui' => '',
            '/无字幕版/ui' => ' 无字幕 ',
            '/双语字幕版/ui' => ' 双语字幕 ',
            '/单语字幕版/ui' => '',
            '/幕后版/ui' => '',
            '/访谈版/ui' => '',
            '/特别直播版/ui' => '',
            '/剧场直播版/ui' => '',
            '/衍生剧版/ui' => '',
            '/番外剧版/ui' => '',
            '/重启版1/ui' => '',
            '/改编剧版/ui' => '',
            '/原创剧版/ui' => '',
            '/预告片1/ui' => '',
            '/花絮版1/ui' => '',
            '/直播回放版/ui' => '',
            '/剧场录播版/ui' => '',
            '/MKV/ui' => '',
            '/Matroska格式/ui' => '',
            '/Matroska/ui' => '',
            '/MP4/ui' => '',
            '/MPEG-4格式/ui' => '',
            '/MPEG-4/ui' => '',
            '/AVI/ui' => '',
            '/音频视频交错/ui' => '',
            '/H264/ui' => '',
            '/H265/ui' => '',
            '/AV1/ui' => '',
            '/x264/ui' => '',
            '/x265/ui' => '',
            '/\bHDR\b/ui' => '',
            '/杜比视界/ui' => '',
            '/杜比全景声/ui' => '',
            '/双音轨/ui' => '',
            '/单音轨/ui' => '',
            '/多音轨/ui' => '',
            '/内嵌双语/ui' => ' 内嵌 ',
            '/内嵌/ui' => ' 内嵌 ',
            '/外挂/ui' => ' 外挂 ',
            '/无码版/ui' => '',
            '/有码版/ui' => '',
            '/源码版/ui' => '',
            '/原画版/ui' => '',
            '/原生版/ui' => '',
            '/收藏版/ui' => '',
            '/珍藏版/ui' => '',
            '/定制版/ui' => '',
            '/会员版/ui' => '',
            '/付费版/ui' => '',
            '/资源版/ui' => '',
            '/网盘版/ui' => '',
            '/数字版/ui' => '',
            '/实体版/ui' => '',
            '/修复版/ui' => ' 修复 ',
            '/H264/ui' => '',
            '/H265/ui' => '',
        ];
    }

    private static function initSymbolReplacements() {
        self::$symbolReplacements = [
            '（' => ' ',
            '）' => ' ',
            '【' => ' ',
            '】' => ' ',
            '『' => ' ',
            '』' => ' ',
            '「' => ' ',
            '」' => ' ',
            '‹' => ' ',
            '›' => ' ',
            '〖' => ' ',
            '〗' => ' ',
            '《' => ' ',
            '》' => ' ',
            '〈' => ' ',
            '〉' => ' ',
            '：' => ' ',
            ':' => ' ',
            '，' => ' ',
            '。' => ' ',
            '？' => ' ',
            '?' => ' ',
            '！' => ' ',
            '!' => ' ',
            '“' => ' ',
            '”' => ' ',
            '、' => ' ',
            '—' => ' ',
            '——' => ' ',
            '·' => ' ',
            '・' => ' ',
            '~' => ' ',
            '～' => ' ',
            '〜' => ' ',
            'amp' => ' ',
            '&' => ' ',
            '*' => ' ',
            '$' => ' ',
            ';' => ' ',
            '@' => ' ',
            '[]' => ' ',
            '【】' => ' ',
            '\n' => ' ',
            ';' => ' ',
        ];
    }

    private static function chineseToNumber($cn) {
        $cnDigits = [
            '零' => 0, '一' => 1, '二' => 2, '三' => 3, '四' => 4,
            '五' => 5, '六' => 6, '七' => 7, '八' => 8, '九' => 9,
            '十' => 10, '百' => 100, '千' => 1000
        ];

        $len = mb_strlen($cn);
        if ($len === 0) return 0;
        if ($len === 1) {
            $char = mb_substr($cn, 0, 1);
            return isset($cnDigits[$char]) ? $cnDigits[$char] : 0;
        }

        $result = 0;
        $temp = 0;
        $chars = preg_split('//u', $cn, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($chars as $char) {
            if (!isset($cnDigits[$char])) continue;
            $val = $cnDigits[$char];
            if ($val >= 10) {
                if ($temp === 0) $temp = 1;
                $result += $temp * $val;
                $temp = 0;
            } else {
                $temp = $val;
            }
        }
        $result += $temp;

        return $result;
    }
}
