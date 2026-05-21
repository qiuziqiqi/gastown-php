# 项目上下文 - AI 必读

> 本文件是 AI Agent 每次会话开始时必须读取的第一个文件

---

## 项目信息

| 项目属性 | 详情 |
|---------|------|
| **项目名称** | bookan (图书馆数字资源聚合平台) |
| **技术栈** | Lumen 5.7 (PHP 7.4.33), Laravel 8/10 |
| **开发环境** | Laradock |
| **代码管理** | GitLab + GitFlow 分支规范 |
| **接口文档** | Apifox |

---

## 核心约定

### 代码规范

1. **引用格式**: `Model::method()`, `@file:line`
2. **集合操作**: 优先使用 Laravel Collection (`map`, `filter`, `flatMap`, `values`, `take`)
3. **质疑冗余**: `flatMap` 必要性、`filter` 冗余性要主动提出
4. **追求优雅**: 简洁代码优于复杂代码

### 接口规范

```php
// 项目异常统一返回格式
return response()->json([
    'code' => 0,      // 0=成功, 非0=失败
    'msg'  => 'message',
    'data' => $data
]);
```

### 中间件

- `instanceCheck`: 实例验证中间件

### 数据库

| 配置项 | 值 |
|--------|-----|
| **主力库** | `app_appserver` |
| **连接名** | `mysql_appserver` |
| **引擎** | InnoDB (`.env` 中设置 `DB_ENGINE=InnoDB`) |

---

## 当前任务

> 详见 `TASKS.md`

## 最近工作摘要

> 详见 `WORKLOG.md`

---

## 常用命令

```bash
# Laradock 环境
docker-compose exec workspace bash

# GitFlow
git flow feature start <name>
git flow feature finish <name>
git push origin develop

# 数据库
php artisan migrate
php artisan db:seed
```

---

## 快速开始 (AI)

1. 先读 `TASKS.md` 了解当前任务
2. 再读 `WORKLOG.md` 了解上下文
3. 查看 `memory/MEMORY.md` 获取项目记忆
4. 开始工作，完成后更新 `WORKLOG.md`

---

## 持久化原则

```
每次完成重要工作后，必须:
1. 更新 WORKLOG.md (记录今日工作)
2. 更新 memory/MEMORY.md (如有新规范/偏好)
3. 更新 TASKS.md (如有已完成的任务)
```
