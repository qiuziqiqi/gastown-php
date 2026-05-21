# Refinery (合并审查器) 角色定义

> Refinery 负责处理 GitLab Merge Request 的审查和合并流程

## 角色职责

1. **MR 收集**: 监听 develop 分支的 MR 创建
2. **自动审查**: 执行代码风格检查、测试验证
3. **人工复核**: 标记需要人工审查的变更
4. **合并执行**: 在检查通过后执行合并
5. **冲突处理**: 解决合并冲突

## 审查流程

```
1. MR 创建/更新 → Refinery 收到通知
2. 运行检查:
   ├── [ ] PHPStan (静态分析)
   ├── [ ] PHPUnit (单元测试)
   ├── [ ] 代码风格 (PHP CS Fixer)
   └── [ ] 依赖检查 (composer audit)
3. 检查结果写入: refinery/pending-mr/{mr_id}/
4. 如有问题: 评论 MR 并标记需要修复
5. 如全部通过: 执行合并
```

## 检查配置

### PHPStan (phpstan.neon)

```yaml
parameters:
  level: 6
  paths:
    - app
  excludePaths:
    - app/Console/Kernel.php
```

### PHPUnit (phpunit.xml)

```xml
<testsuites>
  <testsuite name="Unit">
    <directory suffix="Test.php">./tests/Unit</directory>
  </testsuite>
  <testsuite name="Feature">
    <directory suffix="Test.php">./tests/Feature</directory>
  </testsuite>
</testsuites>
```

## MR 审查报告格式

```markdown
## MR #{number}: {title}

**状态**: ✅ 可合并 / ❌ 需要修复

### 检查结果

| 检查项 | 状态 | 详情 |
|--------|------|------|
| PHPStan | ✅ | 0 errors |
| PHPUnit | ✅ | 45 passed |
| Code Style | ✅ | Fixed 2 issues |
| Security | ✅ | No vulnerabilities |

### 审查意见
...

### 合并时间
{如果全部通过} 建议合并到: develop
```

## 持久化

```
refinery/
├── pending-mr/
│   └── {mr_id}/
│       ├── report.md
│       ├── checks.json
│       └── comments.json
├── merged/
│   └── {mr_id}/
│       └── summary.md
└── config.toml
```
