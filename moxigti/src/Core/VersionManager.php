<?php
/**
 * 沫兮官替 · 核心版本管理框架 (MoxiGTI Core Version Manager)
 * -----------------------------------------------------------
 * 版本进位规则(100 进制 patch):
 *   初始版本为 v.0.0.1；
 *   patch 每维护/修改一次 +1；
 *   当 patch 累计到 100 时进一位 minor 并归零，即 v.0.0.100 -> v.0.1.0；
 *   随后 patch 继续从 1 递增：v.0.1.1 ... v.0.1.100 -> v.0.2.0，依次类推。
 */

declare(strict_types=1);

namespace MoxiGti\Core;

use InvalidArgumentException;
use RuntimeException;

/** 单个 minor 周期内的版本步长(含过渡位 .0) */
const STEP = 100;

/**
 * Class VersionManager
 * 核心版本管理类，实现版本进位逻辑。
 */
class VersionManager
{
    /** @var array{int,int,int} 版本元组 [major, minor, patch] */
    private array $version;

    /** @var array<int,array{from:string,to:string,at:string,reason:string}> 版本变更日志 */
    private array $log = [];

    /**
     * @param int $major 主版本号
     * @param int $minor 次版本号
     * @param int $patch 补丁版本号
     */
    public function __construct(int $major = 0, int $minor = 0, int $patch = 1)
    {
        $this->version = [$major, $minor, $patch];
    }

    /** 当前版本字符串 v.x.y.z */
    public function current(): string
    {
        return self::formatVersion($this->version);
    }

    /** 当前累计修订计数 */
    public function revision(): int
    {
        [, $minor, $patch] = $this->version;
        return $minor * (STEP + 1) + $patch;
    }

    /**
     * 执行一次版本修订(维护/修改)。
     * @param string $reason 修订说明
     * @return array{from:string,to:string,at:string,reason:string}
     */
    public function bump(string $reason = '(维护)'): array
    {
        [$major, $minor, $patch] = $this->version;

        if ($patch === STEP) {
            $minor += 1;
            $patch = 0;
        } else {
            $patch += 1;
        }

        $before = $this->current();
        $this->version = [$major, $minor, $patch];

        $entry = [
            'from'   => $before,
            'to'     => $this->current(),
            'at'     => gmdate('c'),
            'reason' => $reason,
        ];
        $this->log[] = $entry;

        return $entry;
    }

    /** 最近一条变更记录 */
    public function lastEntry(): ?array
    {
        $count = count($this->log);
        return $count > 0 ? $this->log[$count - 1] : null;
    }

    /** 获取全部变更日志 */
    public function log(): array
    {
        return $this->log;
    }

    /** 导出可持久化状态 */
    public function toArray(): array
    {
        return [
            'version' => $this->version,
            'log'     => $this->log,
        ];
    }

    /**
     * 从持久化状态恢复
     * @param array{version:array{int,int,int},log:array<int,array>} $data
     */
    public static function fromArray(array $data): self
    {
        $mgr = new self();

        if (isset($data['version']) && is_array($data['version']) && count($data['version']) === 3) {
            $mgr->version = array_map('intval', $data['version']);
        }

        if (isset($data['log']) && is_array($data['log'])) {
            $mgr->log = $data['log'];
        }

        return $mgr;
    }

    /**
     * 解析版本字符串 v.x.y.z 为元组
     * @return array{int,int,int}
     * @throws InvalidArgumentException
     */
    public static function parseVersion(string $str): array
    {
        $str = trim($str);
        if (!preg_match('/^v?\.?(\d+)\.(\d+)\.(\d+)$/', $str, $matches)) {
            throw new InvalidArgumentException("无法解析版本号: {$str}，期望形如 v.0.0.1");
        }
        return [(int)$matches[1], (int)$matches[2], (int)$matches[3]];
    }

    /**
     * 版本元组 -> 展示字符串
     * @param array{int,int,int} $tuple
     */
    public static function formatVersion(array $tuple): string
    {
        return sprintf('v.%d.%d.%d', $tuple[0], $tuple[1], $tuple[2]);
    }

    /**
     * 从累计修订计数 N(=1,2,3...) 反推版本元组
     * @return array{int,int,int}
     * @throws InvalidArgumentException
     */
    public static function versionFromRevision(int $rev): array
    {
        if ($rev < 1) {
            throw new InvalidArgumentException("修订计数必须为 >= 1 的整数，收到: {$rev}");
        }
        $minor = (int) floor($rev / (STEP + 1));
        $patch = $rev % (STEP + 1);
        return [0, $minor, $patch];
    }
}