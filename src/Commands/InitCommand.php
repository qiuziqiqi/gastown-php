<?php
/**
 * InitCommand - 初始化工作目录
 *
 * 对应原始 scripts/init-workdir.sh
 * 用法: php gastown init
 */

namespace Gastown\Commands;

class InitCommand
{
    /**
     * @param array $args
     */
    public function execute(array $args)
    {
        $workDir = GASTOWN_ROOT;
        $agentsDir = "$workDir/.workbuddy/agents";
        $memoryDir = "$workDir/.workbuddy/memory";

        echo "🚀 初始化 Gas Town 协作环境...\n";

        // 创建目录结构
        echo "📁 创建目录结构...\n";
        $this->ensureDir("$agentsDir/mayor/tasks");
        $this->ensureDir("$agentsDir/refinery/pending-mr");
        $this->ensureDir("$agentsDir/refinery/merged");
        $this->ensureDir("$agentsDir/polecats");
        $this->ensureDir($memoryDir);
        $this->ensureDir("$workDir/.workbuddy/convoys");
        $this->ensureDir("$workDir/.workbuddy/hooks");

        // 创建初始状态文件
        echo "📝 初始化状态文件...\n";

        $queueFile = "$agentsDir/mayor/tasks/queue.json";
        if (!file_exists($queueFile)) {
            file_put_contents($queueFile, json_encode([
                'version' => '1.0',
                'updated_at' => '2026-04-23T00:00:00Z',
                'tasks' => [],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        }

        $activeFile = "$agentsDir/mayor/tasks/active.json";
        if (!file_exists($activeFile)) {
            file_put_contents($activeFile, json_encode([
                'version' => '1.0',
                'updated_at' => '2026-04-23T00:00:00Z',
                'tasks' => [],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        }

        $completedFile = "$agentsDir/mayor/tasks/completed.json";
        if (!file_exists($completedFile)) {
            file_put_contents($completedFile, json_encode([
                'version' => '1.0',
                'updated_at' => '2026-04-23T00:00:00Z',
                'tasks' => [],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        }

        $stateFile = "$agentsDir/mayor/state.json";
        if (!file_exists($stateFile)) {
            file_put_contents($stateFile, json_encode([
                'status' => 'idle',
                'last_activity' => null,
                'active_polecats' => 0,
                'total_completed' => 0,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        }

        // 初始化今日日志
        $today = date('Y-m-d');
        $logFile = "$memoryDir/$today.md";
        if (!file_exists($logFile)) {
            file_put_contents($logFile, "# $today\n\n## 工作记录\n\n## 决策记录\n\n## 待处理\n");
        }

        echo "✅ 初始化完成!\n\n";

        // 显示状态
        $queueData = json_decode(file_get_contents($queueFile), true);
        $activeData = json_decode(file_get_contents($activeFile), true);
        $completedData = json_decode(file_get_contents($completedFile), true);

        echo "📋 当前状态:\n";
        echo "   - 任务队列: " . count($queueData['tasks']) . " 个待办\n";
        echo "   - 进行中: " . count($activeData['tasks']) . " 个\n";
        echo "   - 已完成: " . count($completedData['tasks']) . " 个\n";
    }

    private function ensureDir($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
