## Gas Town 协作管理系统 (PHP版)

> 基于 Steve Yegge 的 Gas Town 理念，为 Laravel/Lumen 项目打造的多 Agent 协作工作流
> 
> 本项目是原 Shell 版本的纯 PHP 重写版

## 核心理念

```
AI Agents 是短暂的 (ephemeral)，但工作上下文应该是永久的 (permanent)
```

---

## 快速安装

```bash
# 在项目根目录执行
cd /path/to/your/project
git clone https://github.com/qiuziqiqi/gastown-php.git .
php gastown install --git-hooks
```

---

## 命令列表

| 命令 | 说明 | 原始 Shell 对应 |
|------|------|----------------|
| `php gastown install [--git-hooks]` | 安装协作系统 | `./install.sh` |
| `php gastown init` | 初始化工作目录 | `./scripts/init-workdir.sh` |
| `php gastown assign <id> <desc> [context]` | 分配任务 | `./scripts/assign-task.sh` |
| `php gastown complete <id> [summary]` | 完成任务 | `./scripts/complete-task.sh` |
| `php gastown status` | 查看状态 | `./scripts/status.sh` |

---

## 使用示例

```bash
# 初始化
php gastown install --git-hooks

# 分配任务
php gastown assign gt-001 "实现签到功能"

# 完成任务
php gastown complete gt-001 "已完成签到API"

# 查看状态
php gastown status
```

---

## Agent 角色

| 角色 | 职责 | 配置位置 |
|------|------|---------|
| **Mayor** | 协调器，任务分配 | `agents/mayor/` |
| **Polecat** | Worker，执行任务 | `agents/polecat/` |
| **Refinery** | 合并审查 | `agents/refinery/` |

---

## 目录结构

```
project/
├── gastown                 # CLI 入口 (PHP)
├── src/                    # PHP 源代码
│   └── Commands/           # 命令类
│       ├── InstallCommand.php
│       ├── InitCommand.php
│       ├── AssignCommand.php
│       ├── CompleteCommand.php
│       └── StatusCommand.php
├── hooks/                  # Git hooks (PHP)
│   ├── pre-commit
│   └── post-commit
├── CLAUDE.md               # AI 必读上下文
├── TASKS.md                # 任务列表
├── WORKLOG.md              # 工作日志
├── config.toml             # 配置文件
├── agents/                 # Agent 配置
│   ├── mayor/CLAUDE.md
│   ├── polecat/CLAUDE.md
│   └── refinery/CLAUDE.md
├── .workbuddy/             # 协作系统
│   ├── memory/             # 持久化记忆
│   ├── agents/             # Agent 配置
│   └── config.toml         # 配置文件
└── scripts/                # 兼容性保留 (原 Shell 脚本)
```

---

## Shell 与 PHP 版本对比

| 功能 | Shell 版本 | PHP 版本 |
|------|-----------|---------|
| 主入口 | `./install.sh`, `./scripts/*.sh` | `php gastown <command>` |
| JSON 处理 | `jq` 命令行工具 | `json_decode()` / `json_encode()` |
| 随机 ID 生成 | `openssl rand -hex 2` | `bin2hex(random_bytes(2))` |
| Git 操作 | 原生 bash git 命令 | `shell_exec()` / `exec()` |
| 可移植性 | Linux/macOS | 任何支持 PHP 的平台 |

---

## 参考资料

- [Steve Yegge - Welcome to Gas Town](https://steve-yegge.medium.com/welcome-to-gas-town-4f25ee16dd04)
- [Gas Town 官方文档](https://docs.gastownhall.ai/)
