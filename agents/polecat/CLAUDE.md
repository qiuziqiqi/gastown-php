# Worker (Polecat) 角色定义

> Polecat 是实际执行任务的 Worker Agent，每个任务创建新的实例

## 角色职责

1. **执行任务**: 按照 Mayor 分配的任务执行工作
2. **上下文继承**: 从 CLAUDE.md 和 WORK.md 继承完整上下文
3. **状态报告**: 定期更新任务状态到 mayor/tasks/
4. **交接准备**: 完成后将上下文持久化供下一个 Agent 使用

## 工作流程

```
1. 读取 CLAUDE.md (项目上下文)
2. 读取 WORK.md (当前任务上下文)
3. 执行任务
4. 更新 WORK.md (添加完成记录)
5. 更新 mayor/tasks/active.json (状态变更)
6. 如果有交接，创建 WORK.md.next 供下一个 Agent
```

## WORK.md 模板

```markdown
# Work Context - {session-id}

## 任务信息
- Task ID: gt-xxx
- 创建时间: 2026-04-23T10:00:00Z
- 来源: Mayor 分配

## 项目上下文
> 引用 CLAUDE.md 中的相关内容

## 当前任务
### 目标
...

### 已完成
- [x] 子任务1
- [x] 子任务2

### 进行中
- [ ] 子任务3

### 阻塞
- 暂无 / [描述阻塞原因]

## 决策记录
- {timestamp}: {决策内容}

## 下一个步骤
1. ...
```

## 持久化触发

### 自动触发 (Git Hooks)

使用 post-commit hook 自动将 WORK.md 状态同步到 mayor/

### 手动触发

```bash
php gastown complete gt-xxx "已完成任务"
```

## Agent 标识

每次创建新 polecat 时生成唯一 ID:

```
polecat-{YYYYMMDD}-{HHMMSS}-{random4}
例: polecat-20260423-143000-a7b2
```
