<?php
/**
 * jiami_core.php — 核心解析逻辑 (jiami 分支专有，main 分支仅 require 调用)
 *
 * ⚠️ 本文件内容为多层乱码加密的 Closure 闭包代码，直接修改会破坏 HMAC 签名校验。
 * ⚠️ 修改业务逻辑：main 分支改 xt/server.php / PerformanceOptimizer.php 对应明文函数，
 *                 再通过构建脚本重新生成本文件切到 jiami 分支提交。
 *
 * 算法  : php_strip → gzdeflate(9) → XOR 16B(Vj5xQ9rT2mP7sK4z) → 双层 rot13+base64
 * 完整性: 文件加载时 hash_hmac(sha256, key=9mP2xQ7sK4rT8vZ5bN1jW0cH6fD3gY2a) 自检，篡改即抛 RuntimeException。
 *
 * @branch  jiami
 * @since   v5.10.9
 */

// ===== 完整性签名校验（文件头）=====
$__JC_IK__ = '9mP2xQ7sK4rT8vZ5bN1jW0cH6fD3gY2a';
$__JC_EX__ = 'f099953768b435dfe6e5da9611419aa2ad3c149ac129de7384230114fefb895c';
$__JC_SM__ = static function(): string {
    // 去掉 $__JC_EX__ 行中的签名字面量，避免自我指涉无法匹配
    $c = file_get_contents(__FILE__);
    // 用单引号正则，确保 \$ 按字面匹配
    $pattern = '/(\$__JC_EX__\s*=\s*)\'[^\']*\'/';
    $c = preg_replace($pattern, "\\1''", $c);
    return $c;
};
$__JC_CS__ = hash_hmac('sha256', $__JC_SM__(), $__JC_IK__);
if (!hash_equals($__JC_EX__, $__JC_CS__)) {
    throw new \RuntimeException('[jiami_core] 完整性校验失败：文件被篡改或损坏。请从 jiami 分支重新获取。');
}

// =========================================================================
//  4 个 server 核心函数 (含解密 stub)
// =========================================================================

function findUrlInArray(array $arr, string $excludeDomainPattern = ''): ?string
{
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'Vm1haFJiY2ZvNnphSHF1L01XYlYzeG1DUjJHS3RFWCtnSy9QdklEWGhBL1UxeTFYaDNGSm1PdkZ3aFZKUk9VUEdDdVR3NTBOU0dXVkJVTXVYKzBES2k1c2QwaHdjYTdMNWxUVFc2Vk45RnBzVUs5WWpobndRaE8vL3VvOXpPZlhyeWVWdWZlQy9rZTBtelZKV3FVdUF6cnlkVzR0WTNua0hsbHdnb2RxN0lQTmFpN2NsVFRGamxwUnR1Rkc2UEdmVHd4eEQwcUxtV0JEcHBqdXpCZ0kzZGdjSEk3WCtWS0hQQjJGNDdRRGhWaUY2MWJiMCtBRDJiaVV1bmh5K0h5S083c3FjS2dBZ0hwVzJzdGpYclg2NlUxR25FWi9GcmhrODh0SFVmWjZJTmhNR2UxQ082aGI2QmRiaEtxbkF6aCtqNVVjODZxM0twNnBqdnd0UlZrTzIrbEhVTERGRzZaQzRHWFNHcnlkNkxqVVJDbG5IS2I0SVBFM1RZU1NJbDdsSkhDMHdxakJaN2JuekNBWHAyWTRhYkhQYzJqcjJXT1dkZFRLa3h1VnVDUTlJZ2t4YXJxK3JtVVQ3c3lUVWtqR21rNk8rYm9CMEJyZStpMGl1VGFFcVlqR05paSs0ZCtLMVJuaXVkMHoyNmtaOHB6V1k2NzB5dG1NbGcwaTZreWFhNVNHZkVaZHlNdzlmdUExZmthUldjdElMU2JWM3hjeUg3bG1IUXlOcGw1L0phNUg1WUI3Y2FQci9LdXBUTG5XNkZYQzRjZmFsOXFlM0l0Tm5DUXdPS052OGZUQmoyRD0=';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    return $__enc_impl(...func_get_args());
}

function callOfficialReplaceDirect(string $videoUrl): ?string
{
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'Mm05MVM0ZldwNnhNajlFSEVRNlJSMmo1V1FmUkYzSUdVMVkwSWtldC9OODRkZDU0QWI4a0NLWEdkOWgzRzNSNmlKRlpIN295YUU5dFBERFI0YXE3UjUvTFpPWThvYTYycEUraUFLN0sySHRSOFMwK05PV1hKYXdnbVE5aFBZdk5zeXUyQkk2Vm5GMVZ5ZytXUmk0djFFSU1CUnNvaTVTYWhRMVgxdTNmNWxzZHZZL2Vvd3FqK1hoL3p5UkdEWDFwSzh6NXZSRUxDVWt1QmhkZFE5V1hjb2p0bUZnaFpCQnBUZjNza3R5NXNLZytCMzF5VmM5bmFRZTBneUVRWTM1Q1lEc3kxRDB6QzIveWRmWG5mMTBUZG56dzJhYXlpeVVHQzdtdkE0dzdhRE5IRWFVRlc3Qzl0NUpvK0dtZDZjdm52Y3ZOT1Z1VXZNTEhCKzF1ZWJVeDFFYkN4YVdUYzZaeVNzWkNmSGZYVkZNbm11TlJkMGNNRVVlZjVsZVdORmhiUGdJbm5GbDJXa0hvSGdIYi9ON2haQnk4NnpyUmY2RVFHQnFBL2JXMVl6eUczQnVaYlpJRmErMlpUUGdBWEJhOWpCOUdxYVZJcmhVY2NGRTA4WnFpQ2V3SmRxZ08yd2c1QlovZS9ZYm1KS1FMbEI0TDRsWG5HK3dUdjlKYVNnTzl3WEVMVmh2cHp5QXRLc3hVMXRnUDFtNVVDNUxLK1JYZFgraXd3eDNzYklDemxObmVwT0t0RGtVK2hhWExTMHRCTzVyVUp0Zm1XNldVNHlHWDc0SWRZSXVFVU15SEtYQ3l4aGtObUdnbzk1QlF1OFBEbmVLamlEb014R2l3czZBcnJVRFpEYlgwUTBYNUJOTEtXVXZzUy9tdk9CZ0lMN1phdkx0VUR6QStkclNIMWpUNDYxeC9QUmVzdlBUazE0SHptZjlyUVc2dnpkeU40Slh0QWc3MGcrN1NHUFJSbE9McEl6NncreEh0MFZwZXVFaVVQNWk4QjdUZ0pzL01YWG5pR2Z3N1V0ZDN2alJQODlLemlkN08weUhodHpLZHNyeFJXcDdhTlZySW1iZUpiRlRCemJ2VVh2VlN4K3QxOExLL2ZyQXVxYUFZeVl1dVRxMzQvcVA2QUNKL0tjN2RLNmdIYzhCOUdLb29rd0ttOU1mVS9CV0RTQmxGdHRVVTlyU1RCem5wOFFOUHdwU0ZXM1MzRVVTMVpYcHpkOFFiSHVWYTZBUlltamo9';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    return $__enc_impl(...func_get_args());
}

function getVideoLinkFromApiEntry(string $videoUrl, array $api, array $config): ?string
{
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'NG1tclNlQS9wbGRhNUFHdUFEekNkbXVCTENSNWlvVjRid0hEMTlzenVoZ2oyL1ZwSkJYRXplNHh0TjJweURxNnZyWGh1d3Y4TUYvOHhVdk4rZk5MN2ZWQy9LZVE2dTdvQVVxS1d0SlZjM3ZIOWdNb0pBYWh0aTFNejB3V0Uzc25zblVZcjROdWwyQy92VlNpN21rZ2gxWEFKUk9WWjB3ejB6cm5pSGVsTDlIK0lqRmFHbVFMdUZlTFhCTDBEZmdyS0dBeGNWaVN0Ym5WVTUybW5mYmJQRXExRk5qWFdrbTZPbmxxR2FwS3pqM2Jua0lYWlV0MlpDRC9VVkUrM25vNmRrZ3VkSVNMSkZ6MTR4TWhOTktQaGp5dmNoMFYvU09XQU5jWXBXRVlHZGNUVFB6YlRpazh4OW43dndZK2h2K3ZTQ013Sk1CZS9iRGtReGptNjJZTE41Sm0vYW5jVVMycFZ6aUZ5OEpqaEY4SkNhNWhxdlZLQ041enFPNGxZZTFDd3ZVYm1hS0gzb25PbEVuVjBzOHpyWUhoV3IxWUtXU2V5VVREV1RXNzJrWUFIZENUNXdEcE1pemJDQUhWR2hieEpCczViMHZaY2Z5V2tJdmQ1b3NSMjhnYkphZklvSTRwZGJnZHoyVFlEWTZrSUNieEU5eHZmam5ibzF0VXErSWx2NTUrdkVkdHlVdmF1VkJKLzl1ZDgwbXJvWFVqSHdsMW9UblBwaXo1LzFTSjBCZUt6L3VpV3RqVTFEVXN6MTRzNUQ5MFVERUl3Y2ZmWUhGay9aZ0VNb001TnFoUXJwcmRvRTBIdy9CNHc0UnFzdEwyb3luZU1MTGNCV2FMTFdFWm9vZUk4RVJwd1M0ZHdrRHZ4WkV1czRuZ2YycFJBYUVvSmhmTThnS1RDeGZDSEdNRkZPeW8ya1JNMTVVd2FwVDhOb0N0UmVlS3JMVEpqWXNiVWttWFFCRmorWks4MmxRcE81emNKZmJLbDhqWFdFWFRaNERWVTgxb2RJckx1dlh1bDFLVmNaM1ArR2lmeTVvdFNMTXcrQTdDK3JKZE53SlRjZzBGUUxFR21qN2VaU3hlZXRFb1lDMHluN0U2Ymh4SERlTVBiNUdTL3F5VjdsdEhLdDh6cDE3QWtaeEJxYUZld0tGRks0eUhrNW1kQjFZWWJiL3UrK21VUWxVMUdtaFhsTjRiUy81RVFGc3NWUW91VkFRSS9Zd1V4NUJWSjRwT3BUQzR0anRyc2hvcjl4QUltSHcva0ZJLyt0QkxvUXBXT3NZNXJ5a0xDaHJkOVQxMFhJbHN0am1rWkJEMmMrbEpIVDRWNUpBZ3U0dUxKMDFHMWkxckJpLzFOTUtpbjNzTzltRk1xWGZNQ2ZqRnprT04xdmRtMDQ3RkpLNGwyVlRnSlhYaFdlanNOYUVhNURrSmlBdkJzS3JZbm1NR0NuRjVVbDRac1JGSDBnd0JDeldOdnM0Y2xTSzk3OEovNGpXdkxIVXdXUHgxRzdYcHYyL0F0RTIxOUVNOFpSd2ZEMDRpd05OT1J3cFpWTGZkaml1SzVKUGhJbjEzY2NsNDYvUTF0OUREQUJmQllOTFRrSnA4YU1DcGhVb2J1d2wyNGNBeFp0TTdaSVBaa0Z4dmt5UDBsVlgvVFJ1a2tnY0U3RE9FQTJndEppK1N3K04yT1BWaGJuMnhzQWZiOHE4MjlERzZWRW5SUitDZU93WXFGVmRlRDhSUkt1RVNQQU0yTkpoRFZuT1RIcGVYTTVzaURYenI0RlhQWDlLVGNjTmdFMFNITmFTQlNBNkdiS0ttZGlQYjFtS08wdi85bS9NVHVBam9KL045NGJSczJxWXp1WHdWSDZmUFpTa25Kekxrek93NlU2bi9FK1pCSC82Z3I0YW1LM0NFZGk2SWZsTlJGeGRXNVFRQVl6UUo0aGRUSzU5bnVNRVp0RjhVaWFLSFdhZGVlTHZSbVo4QVhFNk1KRDllbldVZQ==';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    return $__enc_impl(...func_get_args());
}

function callSingleApi(string $videoUrl, array $apiConfig, array $config): ?string
{
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'Vm13YlpjVldwbGRhRElVZ2ZHaVE5TmxoZGdDRFBuWUNObGg0Y0k3RVJ5Zm9lNHJPR1pyaUtJSUt3eFhGRDdqMXVIZGxaTGkwT2xUeUU3c1pJbmxqYWFyN290dVpQOTk4ZXI4YU0wcEdxSEpOcldTNXIveGtrdmttVWc4VVNhMnpweHoxalRlMDgxWjZVSjFDbzdzSS9Gd1JlSUVvb25SUXE0ZW5BWWtTMzdpWW9OT21HemdmdndVdkF5c3ZyUjgwS3ZwcVR4SElyTzZQckxoYVZGOWJBN2FkNndIK21jcEw5WEhxV2llRU92a0RlR2JNc2VDbE02RlpEUFU0QVBRWnNtcy84R2FYaWlHbzUxV2JTOVRYcGRQMjZwQndWK3ZXaXF0N0ovb2JWVlNiNHh5VFpQaXpQazJ6dTZ3SWV2d1RLbThBZEhKeXlrdmNnQTMya0x2OUJISk9jZ0p3UXJiUDVmWmcxVGtHZ1NERVVsSG9wWTFZSXphZDRPRk9ocWFXWGNEbzU3K2hzSXdLd2hnWW5DZ0RiR1FGTHJZQ1NrTEMvVGc3d2d0VWtQTjFXdTByamZvemlhbURwWUdnWHRQN3NOU1VZbEYrTEhTbmFmM3EwaGs1V0xrWGpSR2hPbFNjUS8reGJrdHJaVDUyZ1U4QXU4bmNCQUVn';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    return $__enc_impl(...func_get_args());
}

// =========================================================================
//  2 个 PerformanceOptimizer 闭包工厂 (PO.php 薄包装 ::call($this,...) 转发)
// =========================================================================

/** @internal PO::extractVideoUrl 解密闭包工厂 */
function &_jm_po_extractVideoUrl() : \Closure {
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ'; $_x1 = '9rT2m'; $_x2 = 'P7sK4'; $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'NG05WUE0YldvNmJNUC9jLzBGNDQ4bGlsN21hSUNKMXd5d3dmSWtlai9TNVlSdDBXQWpZMzNmb0ZpOWdMamFFd2xJa3RTcEZQdEx5QnpheldTazZ5RU1raDBHQWFFVkNITSsrTGNLVU1mVElxNjI4RDFKSTE5aEdKOU82Ri9Zb29TTDVNaElNOGNYSVlaWXE0R1lpT1AxRkxzeHFiZlJsSW1kQU15TWRsTlpHZTBZejNZYUZnRGtjZGgxTjdnVDJkM2dMRTFPaWpyNWZRRENNdkdLbitFLy9RV0hsKzB6UzNZWXBHeHhNRWtyS3BHZ3JDRE91Y1NDT1FkV0RnMFhCakNzbnhqKys4YXVhU3BLNWw2RnhreTR4TSs5dGQvNGRhcDUzVVZlcXRwaGdXQzZxWTVkbVpzdzZjbVNiMnM3c2RuMkxDSEZJclZWQWdZcjNlT1ZOZ2thenh3NFhnNDI0SHNQalJaaVBRT0J4bDZCQ3J5MmIwSmU1S1F4dUN0MDhaV2tBR055VXpMdGc1OElhYjc2U2xTTWdvczBwbEFtZm5kVXhvV1h3clFwaWd0cHFmVXR6bUQybS9nV1crRUd2OFZnbW5GNlVaMHhha1pBNWl0blozMmwwSEpQdGZ5YlR6cWlPdlRjd1VURVVkbXhQMmtmak9HS2JzbStONWFHQWJaNGU2MmxmZ01yQkdldHpLSXNhQVc3RUluS1U1MkQxQkFqUTVvbWUvTU1pdGN0elNmRUR2RGpQRHNIU3l4bnIxRzRRMHdCSXkvRUs5Y2xlalQwWDlJOGQ4aHR4b1grbUpjV29vS2tycG5qSkU3dDhZc0VCMEY2Wmd3RjVXcUF4NjF5dzdtQ0VlYlJDTEtxdXNyeUZjYzJYWGNHVkxlRTByL3hkSzlLaFpYZG52YTNtME1lOUZ6QmNOWVFRTy9MWWRyeUFqZ3dsc0xmbnlPbUpKUC95K3FKc0N0YTNvaWRSWlFZalBzWkR2NFhPampMdFd2cGFBeTBocVdVZXdKakxFRmFBcy8vbFpTdmphTnZzcllQR1NjV3I4bXd5VEk2OWlEdjBPOEp6WE1EcVlCT3lmWHJhcy9jOU9Uc0hJVFF1cGtWVjAxWmx2MVNyb3hGWHdiYW5rQkg2aXVOTDhRbzdFNkVjMks4ZW52MDEvc2tSY1ZHa0dLeHc2cHRtUTl3bjdlSjc0V3JBUnhwTkt4RTF0d3dJUEJGVEFlZk84UThNamR6ZmM1dGZMY2NpcGptQk5aTWNPc2FGYUJXZmZPVnB5V01BcHlkak9YUHVwMmtNSTFOakY1YzdleTlIOHc4MGZBTXNLWUY0eDJpNndBRD09';
        $_f = function ($_c, $_k) {
    $_l = strlen($_k); $_r = "";
    for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
        $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
    }
    return $_r;
}; /**/ 
        $_raw = @gzinflate($_f(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('jiami_core::PO::extractVideoUrl 代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_f, $_raw);
        if (!($__enc_impl instanceof \Closure)) throw new \RuntimeException('jiami_core::PO::extractVideoUrl 解密失败');
    }
    return $__enc_impl;
}

/** @internal PO::findUrlInArray 解密闭包工厂 */
function &_jm_po_findUrlInArray() : \Closure {
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ'; $_x1 = '9rT2m'; $_x2 = 'P7sK4'; $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'Vm1haFJiMFdvNnhhcUVrbzhBdFYzeG1Dc2FxSHYvc2tsRzBGNXJHaWVQWmlPdzZHZjlCRHpKUFh3a2xiQnFzOGgrYTRSVS9yaWVsK1cyZHJTdFR6S2FsZG53N3E3UTRQU2Q4VS9GN1hqbU9XK3JkNVArM3R2WFdWVWJ3dzhYRkJtcWRhS1FvMXlpRFVNVGQvSVk1Szk0UEdJY1MycCtDd3dESkNra2dkL1Nvcy8wTTZqaXhPODF3a3lKRUtPdEltbXRuYm5Ccy9ZSDEvRlI1SUtrZEtwSHYwZTdLR0Q5TGFCM0pyalFDRHdxbUdOVE1HZkRwZGxDZllmV2JsdVR0by9WaDZpZHo3MCtFa0xiaXovc1hyWXU0T1lVVk1yeVZ6dW5yVjVpQlMrOUdFMlRjbG5UUnZ6V1VIN0hLeXVPdS9VYXY0TWNtUllmSm5ReHZxaWhPMldkRi8vdTF6eTYrbXJxYUNKZFYvSWVDZ01qK2hHcmxVRWdBU3dZbGcwcGVOQ1lhRWFzKzJiVTh4anV2cklTbk0rcDV1QnNTTE9yUUNxZFdTNmtXUlJWYXBXbHFyUnRJWGY5S2N6RXNFSEt6L3Qzd05yTTNVVjNmRUJsaGVNcFpyUjg4clFKQmdCU0NSSllLUEgxeHM2aWdZZ1l4UndBcTVwUTJIMzhWcVpWZlJ2YjVnbmhjblVZdnJoTndGTVVGTTZxeTljZUhGY0hBNHc4WWhneHplYXJuY2xZQTZtNWk1SmlVVTdsTjI3eUNWY2d1d213c3N4QldXM2x3eVEyUXc4TWRUanNab0xGVHdSbnpsMXcvU2dMKzcxNlBWbDhxeQ==';
        $_f = function ($_c, $_k) {
    $_l = strlen($_k); $_r = "";
    for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
        $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
    }
    return $_r;
}; /**/ 
        $_raw = @gzinflate($_f(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('jiami_core::PO::findUrlInArray 代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_f, $_raw);
        if (!($__enc_impl instanceof \Closure)) throw new \RuntimeException('jiami_core::PO::findUrlInArray 解密失败');
    }
    return $__enc_impl;
}


// =========================================================================
//  返回当前版本标识 (供 main 包装层 version 校验)
// =========================================================================
return [
    'core_version' => 'v5.10.9-jiami',
    'build'        => '2026-08-13',
    'provides'     => ['findUrlInArray','callOfficialReplaceDirect','getVideoLinkFromApiEntry',
                       'callSingleApi','_jm_po_extractVideoUrl','_jm_po_findUrlInArray'],
];