# 当前任务列表

> 格式: `[状态] 任务描述 (优先级: P1/P2/P3)`

## 进行中

- [P1] CDP 调试端口接入 WorkBuddy 客户端实现签到自动化
- [P1] php-mcp/laravel 包集成调研
- [P2] OpenCode 作为 Claude Code 平替调研

## 待办

- [P1] 微信静默登录 (wxSilentLogin) 功能完善
- [P2] Repository 层代码重构 (Laravel Collection 优化)
- [P3] 多 Agent 协作开发流程设计

## 已完成

- [x] UcServer\UserController::wxSilentLogin 微信静默登录实现
- [x] ResourceRepository.php 集合重构
- [x] InstanceRepository.php 集合重构
- [x] Lumen 数据库配置 (mysql_appserver)

## 任务 ID 规范

```
使用 GitLab Issue 编号作为任务 ID
格式: gl-#{issue_number}

示例:
- gl-#123 (GitLab Issue #123)
- feat-xxx (新功能，无 Issue)
```

## Convoy 批次

| Convoy ID | 任务 | 状态 | 负责人 |
|-----------|------|------|--------|
| gt-20260423-1 | 签到自动化 | 进行中 | AI |
