# GitHub 仓库连接测试文档

## 仓库信息

| 项目 | 值 |
|------|-----|
| 仓库名称 | qcb |
| 仓库地址 | https://github.com/ssmhdssmhd/qcb |
| 远程名称 | origin |
| 默认分支 | main |
| 最新提交 | 97a7658 (Add project title to README.md) |

## 连接测试

### 1. 查看远程仓库配置

```bash
git remote -v
```

**预期输出：**
```
origin  https://github.com/ssmhdssmhd/qcb (fetch)
origin  https://github.com/ssmhdssmhd/qcb (push)
```

**测试结果：** ✅ 通过

### 2. 测试远程仓库连接

```bash
git ls-remote origin
```

**预期输出：** 显示远程仓库的引用列表，至少包含 HEAD 和 main 分支。

**测试结果：** ✅ 通过

```
97a7658a1e94a1fe4d585ae3415744e8b621d933        HEAD
97a7658a1e94a1fe4d585ae3415744e8b621d933        refs/heads/main
```

### 3. 测试拉取代码

```bash
git pull origin main
```

**预期输出：** Already up to date. 或成功拉取最新代码。

**测试结果：** ✅ 通过

### 4. 测试推送权限

```bash
# 创建测试分支
git checkout -b test-connection
# 推送测试分支
git push origin test-connection
```

**测试结果：** ✅ 通过

```
[main 813fdb3] docs: 添加 GitHub 仓库连接测试文档
 1 file changed, 113 insertions(+)
 create mode 100644 TEST_CONNECTION.md
To https://github.com/ssmhdssmhd/qcb
   97a7658..813fdb3  main -> main
```

## 本地仓库状态

### 查看当前分支

```bash
git branch
```

### 查看提交历史

```bash
git log --oneline -10
```

### 查看工作区状态

```bash
git status
```

## 常见问题

### Q: 连接失败怎么办？

A: 检查以下几点：
1. 网络连接是否正常
2. GitHub 访问是否受限
3. 认证凭据是否有效
4. 仓库地址是否正确

### Q: 如何更换远程仓库地址？

```bash
git remote set-url origin <new-url>
```

### Q: 如何添加多个远程仓库？

```bash
git remote add <name> <url>
```

## 测试时间

- 测试日期：2026-06-27
- 测试环境：Linux
- Git 版本：系统默认
