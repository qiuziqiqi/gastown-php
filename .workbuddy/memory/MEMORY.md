# MEMORY.md - 项目长期记忆

## 项目概况
- **项目名称**: bookan (图书馆数字资源聚合平台)
- **技术栈**: Lumen 5.7 (PHP 7.4.33) + Laravel 8/10
- **开发环境**: Laradock (Docker)
- **代码管理**: GitLab + GitFlow

## 技术决策
1. **开发环境**: 统一使用 Laradock
2. **集合操作**: 优先使用 Laravel Collection
3. **接口格式**: 统一 `{code, msg, data}` 响应
4. **代码引用**: `Model::method()`, `@file:line`
5. **Git 工作流**: GitFlow (feature → develop)

## 常用配置
- 数据库引擎: InnoDB
- 主力库连接: mysql_appserver
- 中间件: instanceCheck
