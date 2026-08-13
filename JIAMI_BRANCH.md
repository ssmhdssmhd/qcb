# jiami 加密分支说明文档

> **当前分支**: `jiami`（`main` 为主分支明文版，用于二次开发/审计）
> **版本**: v5.10.9-jiami / build:20260813-jiami

---

## 一、为什么需要 jiami 分支？

在公网开放部署的视频解析系统容易遭受以下风险：

1. **特征扫描/批量抓站**：攻击者 grep `original_url` / `findUrlInArray` / `callOfficialReplaceDirect` 等关键字，批量识别同类系统并批量探测漏洞；
2. **核心逻辑泄漏**：后台官替匹配算法、去广告规则、isSafeVideoUrl 三层守卫逻辑一旦明文外泄，可被针对性构造绕过输入（例如仿冒 original_url 命名的字段变体）；
3. **虾米/官替接口签名识别**：加密失效的接口调用流程若暴露明文，可被竞品直接照搬配置。

因此 `jiami` 分支对 **核心解析链路的 6 个关键函数** 做了多层乱码+自解码加密。

---

## 二、加密范围（6 个核心函数）

| 文件 | 函数 | 作用 | 加密方式 |
|------|------|------|---------|
| `xt/server.php` | `findUrlInArray()` | 递归搜索 JSON 中的视频地址，排除 original_url 陷阱 | Closure 加密 |
| `xt/server.php` | `callOfficialReplaceDirect()` | 本地官替直调 OfficialReplaceManager，绕开 HTTP 回环 | Closure 加密 |
| `xt/server.php` | `getVideoLinkFromApiEntry()` | 单接口解析+isSafeVideoUrl 三层守卫 | Closure 加密 |
| `xt/server.php` | `callSingleApi()` | 嗅探单接口调用，检测本地官替走直调路径 | Closure 加密 |
| `xt/PerformanceOptimizer.php` | `extractVideoUrl()` | 并发模式下提取视频地址，同 isSafeVideoUrl 守卫 | Closure 加密 (bind $this) |
| `xt/PerformanceOptimizer.php` | `findUrlInArray()` | 并发模式递归提取 | Closure 加密 (bind $this) |

---

## 三、加密算法（多层乱码叠加）

```
明文 PHP 源码 (function 签名 + body)
  │
  ▼ 步骤1 剥离可见性前缀（public/private/static → 仅类方法）
  ▼ 步骤2 去除函数名 → 变为匿名 function($arg):T{ body }
  ▼ 步骤3 php_strip_whitespace 压缩（剥离注释/冗余空白）
  ▼ 步骤4 gzdeflate(level=9) 压缩（体积 ↓30-60%）
  ▼ 步骤5 XOR 16 字节密钥 Vj5xQ9rT2mP7sK4z 逐字节叠加
  ▼ 步骤6 str_rot13 + base64_encode
  ▼ 步骤7 str_rot13 + base64_encode （双层）
  ▼
最终密文：800~2100 字节 Base64 字符串
```

---

## 四、运行时解密（首次调用 → 一次解密 → Closure 常驻）

每个被加密函数的存根 (stub) 如下：

```php
function FUNC_NAME($param1, $param2 = 'def'): ?T {
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ'; $_x1 = '9rT2m'; $_x2 = 'P7sK4'; $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;         // 密钥拆四段拼接（防 grep "Vj5xQ9rT..."）
        $_p = '<BASE64 PAYLOAD>';                 // 密文（单引号字符串）
        $_xo = function ($_c, $_k) { ... };       // XOR 还原
        $_raw = gzinflate($_xo(                    // 4 层逆序解码
            str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        $__enc_impl = eval('return ' . $_raw . ';'); // 转成 Closure 对象
        unset($_x0..$_raw);                       // 立即清理中间变量
    }
    // 普通函数：直接调用闭包
    return $__enc_impl(...func_get_args());
    // 类方法（PO）：用 Closure::call($this) 绑定实例，使 body 内 $this 可用
    // return $__enc_impl->call($this, ...func_get_args());
}
```

### 关键特性
- **零感知性能**：首次调用只解密 1 次，结果存入 `static $__enc_impl`，第二次起直接 `call_user_func_array`，额外开销 < 0.1ms
- **类方法 `$this` 可用**：用 `Closure::call($this, ...)` 动态绑定，加密后的闭包体内可直接访问 `$this->config / $this->stats` 等成员
- **无特征字符串**：密钥拆分、变量名乱码 (`$_x0/$_k/$_xo/$_p`)、无 `base64_decode(gzinflate(eval(` 的直写连续模式
- **失败安全**：解密失败抛 `RuntimeException('核心解析代码损坏')`，不会静默输出错误地址
- **单仓协作**：jiami 与 main 两分支共享同一套核心实现 (jiami_core 加密前源码来自 main 明文)，杜绝分叉漂移

---

### 四.二、2026-08-13 架构升级：核心代码单独抽取为 `xt/jiami_core.php`

为了实现「jiami 分支改动尽量小 / main 分支能热插拔加密实现」，v5.10.9 起重新组织代码布局：

```
xt/jiami_core.php   ← jiami 分支新增. 6 个加密核心函数 (4 普通函数 + 2 PO 工厂)
                    + 顶部 HMAC-SHA256 完整性校验 (篡改/损坏 → RuntimeException 阻断)
                    + 文件末尾 return ['core_version'=>'v5.10.9-jiami', 'provides'=>[6个函数名]]
                    (main 分支用它判版本/完整性)

xt/server.php       ← jiami 分支: require_once jiami_core + 缺失提示 (不再内联 4 个 stub)
                    ← main  分支: 顶部 @include_once jiami_core(若存在), 4 个明文函数外包裹 if-guard
                                   允许缺省回退, 也允许单文件覆盖到 main 直接享受加密版

xt/PerformanceOptimizer.php
                    ← jiami 分支: 两个 private 方法 body 改为 _jm_po_* 工厂 → Closure::call($this)
                    ← main  分支: body 开头注入转发判断, 失败时执行原明文 body (零侵入)
```

由此获得两个好处：
1. **jiami 分支更难被修改/破解**：所有核心代码在独立的 HMAC 签名文件里，server.php/PO 只剩几十行包装代码；改任何 1 字节都会触发完整性校验失败。
2. **main/jiami 分支单仓协作**：生产部署只需要从 jiami 分支拷贝 `xt/jiami_core.php` 覆盖到 main，无需整仓 `git checkout jiami`。

---

## 五、与 main 分支的功能一致性

| 功能 | main (明文) | jiami (加密) |
|------|-----------|------------|
| P0 original_url 陷阱修复 | ✅ | ✅（同逻辑，加密执行） |
| isSafeVideoUrl 三层守卫 | ✅ | ✅（同逻辑，加密执行） |
| 官替优先 (mode=replace) 默认开启 | ✅ | ✅（同逻辑） |
| callOfficialReplaceDirect 本地直调 | ✅ | ✅（同逻辑，加密执行） |
| 20+ 非视频字段黑名单 | ✅ | ✅（加密闭包内部处理） |
| 视频扩展名强校验 | ✅ | ✅（加密闭包内部处理） |
| PerformanceOptimizer 并发调用 | ✅ | ✅（类方法 bind $this） |
| 每一次请求行为 | 完全一致 | 完全一致 |
| 返回值/错误码 | 完全一致 | 完全一致 |

---

## 六、如何验证加密分支的正确性？

### 6.1 语法检查
```bash
php -l xt/server.php
php -l xt/PerformanceOptimizer.php
php -l jiexi.php
# 均应输出 No syntax errors detected
```

### 6.2 运行时单元测试（加密函数解密链路）
```bash
# 在本仓库目录执行：
php -r '
require_once "xt/server.php";
require_once "xt/PerformanceOptimizer.php";

// T1: original_url 陷阱（虾米官解返回验证失败）
$errJson = ["success"=>false,"code"=>500,
  "original_url"=>"https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html","play_url"=>""];
$vHost = parse_url($errJson["original_url"], PHP_URL_HOST);
$excl = "/\/\/".preg_quote($vHost, "/")."/i";
var_dump(findUrlInArray($errJson, $excl) === NULL);  // 应返回 bool(true)

// T2: 正常 m3u8 提取
$good = ["code"=>200,"data"=>["url"=>"https://cdn.x/vod/x.m3u8?sign=1"]];
var_dump(findUrlInArray($good));  // 应返回 string 正确 URL
'
```

### 6.3 端到端用户链路（请求环境）
```bash
# 确保 web 服务运行在 9002 端口后访问：
curl -s "http://127.0.0.1:9002/jiexi.php?url=https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html" | head -c 400
# 加密分支应：
#   - 若官替/资源站可用 → code=200 + 返回 .m3u8 广告过滤后地址
#   - 若资源站不可用  → code=500 明确错误（不再"假成功=200 返回原始URL"）
# 无论哪种，都 ❌不应返回 url=https://v.youku.com/... （原始HTML页面地址）
```

---

## 七、如何重新加密/调整加密参数？

如果需要更新加密密钥或重新加密（例如 main 分支升级后同步合并到 jiami）：

### 步骤 1：同步 main → jiami
```bash
git checkout jiami
git fetch origin
git merge origin/main          # 或者 git merge main (本地)
# 解决冲突后：
```

### 步骤 2：运行加密工具
```bash
# 工具位于仓库根，仅在 jiami 分支存在
php _build_jiami_helper2.php patch-all

# 测试
php _build_jiami_helper2.php test-closure     # 普通函数加密链路
php _build_jiami_helper2.php test-class       # 类方法加密 + bind $this
php -l xt/server.php && php -l xt/PerformanceOptimizer.php
```

### 步骤 3：调整密钥
修改 `_build_jiami_helper.php::encrypt_core_code()` 第 3 参数以及 `_build_jiami_helper2.php` 中存根模板的 `$_x0+$_x1+$_x2+$_x3`，确保两端一致（建议 16 字节，可任意字符组合）。

---

## 八、分支切换 & 使用场景建议

| 场景 | 使用分支 | 说明 |
|------|---------|------|
| 本地开发/二次开发/审计代码 | `main` | 代码完全可读，便于调试和扩展 |
| 公网开放部署 / 生产对外服务器 | `jiami` | 核心逻辑加密，抵御特征扫描和泄漏 |
| 打包发布给第三方客户 | `jiami` | 保护核心算法实现 |

切换方式：
```bash
git checkout main     # 切换到明文版
# 修改代码、调试、合并 PR
git checkout jiami   # 切回加密版
git merge main       # 吸收 main 新功能
php _build_jiami_helper2.php patch-all   # 重新加密
```

---

## 九、版本一致性校验

```php
// 在 PHP 中读取：
$v = require 'version.php';
echo $v['branch'];   // 输出 'jiami' (本分支) / 'main' (主分支)
echo $v['version'];  // v5.10.9
echo $v['build'];    // 20260813-jiami
```
