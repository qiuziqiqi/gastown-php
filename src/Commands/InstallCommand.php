<?php
/**
 * InstallCommand - 安装协作管理系统
 *
 * 对应原始 install.sh
 * 用法: php gastown install [--git-hooks]
 */

namespace Gastown\Commands;

class InstallCommand
{
    /**
     * @param array $args
     */
    public function execute(array $args)
    {
        $useGitHooks = false;
        foreach ($args as $arg) {
            if ($arg === '--git-hooks' || $arg === '-g') {
                $useGitHooks = true;
            }
        }

        $scriptDir = GASTOWN_ROOT;

        echo "🚀 安装 Gas Town 协作管理系统...\n\n";

        // 创建工作目录
        echo "📁 创建工作目录...\n";
        $this->ensureDir("$scriptDir/.workbuddy");
        $this->ensureDir("$scriptDir/.workbuddy/agents");
        $this->ensureDir("$scriptDir/.workbuddy/memory");
        $this->ensureDir("$scriptDir/.workbuddy/convoys");

        // 复制配置文件
        $this->copyIfExists("$scriptDir/config.toml", "$scriptDir/.workbuddy/config.toml");
        $this->copyIfExists("$scriptDir/CLAUDE.md", "$scriptDir/CLAUDE.md");
        $this->copyIfExists("$scriptDir/TASKS.md", "$scriptDir/TASKS.md");
        $this->copyIfExists("$scriptDir/WORKLOG.md", "$scriptDir/WORKLOG.md");

        // 安装 Git hooks
        if ($useGitHooks) {
            echo "🪝 安装 Git hooks...\n";

            if (is_dir("$scriptDir/.git")) {
                // 复制 hooks（使用 PHP 版本的 hook）
                $preCommitSource = "$scriptDir/hooks/pre-commit";
                $postCommitSource = "$scriptDir/hooks/post-commit";
                $preCommitTarget = "$scriptDir/.git/hooks/pre-commit";
                $postCommitTarget = "$scriptDir/.git/hooks/post-commit";

                copy($preCommitSource, $preCommitTarget);
                copy($postCommitSource, $postCommitTarget);
                chmod($preCommitTarget, 0755);
                chmod($postCommitTarget, 0755);
                echo "✅ Git hooks 已安装\n";
            } else {
                echo "⚠️  当前目录不是 Git 仓库，跳过 hooks 安装\n";
            }
        }

        // 设置脚本权限
        $scriptsDir = "$scriptDir/scripts";
        if (is_dir($scriptsDir)) {
            foreach (glob("$scriptsDir/*.sh") as $shFile) {
                chmod($shFile, 0755);
            }
        }

        // 初始化
        echo "\n🔧 初始化工作目录...\n";
        $currentDir = getcwd();
        chdir($scriptDir);
        $initCmd = new InitCommand();
        $initCmd->execute([]);
        chdir($currentDir);

        echo "\n✅ 安装完成!\n\n";
        echo "📋 后续步骤:\n";
        echo "   1. 阅读 CLAUDE.md 了解项目上下文\n";
        echo "   2. 编辑 TASKS.md 添加任务\n";
        echo "   3. 运行 php gastown assign gt-001 \"任务描述\" 开始工作\n\n";
        echo "💡 提示: 使用 --git-hooks 参数安装提交钩子\n";
    }

    private function ensureDir($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function copyIfExists($source, $target)
    {
        if (file_exists($source)) {
            copy($source, $target);
        }
    }
}
