<?php
/**
 * StatusCommand - 查看协作状态
 *
 * 对应原始 scripts/status.sh
 * 用法: php gastown status
 */

namespace Gastown\Commands;

class StatusCommand
{
    /**
     * @param array $args
     */
    public function execute(array $args)
    {
        $workDir = GASTOWN_ROOT;
        $wbDir = "$workDir/.workbuddy";
        $memoryDir = "$wbDir/memory";

        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║           Gas Town 协作状态面板                              ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n\n";

        // Mayor 状态
        echo "📊 Mayor (协调器)\n";
        $stateFile = "$wbDir/agents/mayor/state.json";
        if (file_exists($stateFile)) {
            $state = json_decode(file_get_contents($stateFile), true);
            $status = $state['status'] ?? 'unknown';
            $lastActivity = $state['last_activity'] ?? '无';
            $activeCount = $state['active_polecats'] ?? 0;
            $totalCompleted = $state['total_completed'] ?? 0;

            echo "   状态: $status\n";
            echo "   最后活动: $lastActivity\n";
            echo "   活跃 Worker: $activeCount\n";
            echo "   已完成任务: $totalCompleted\n";
        } else {
            echo "   ❌ 未初始化\n";
        }
        echo "\n";

        // 任务队列
        echo "📋 任务队列\n";
        $queueFile = "$wbDir/agents/mayor/tasks/queue.json";
        if (file_exists($queueFile)) {
            $queueData = json_decode(file_get_contents($queueFile), true);
            $queueCount = count($queueData['tasks'] ?? []);
            echo "   待分配: $queueCount 个\n";
        } else {
            echo "   ❌ 未初始化\n";
        }
        echo "\n";

        // 进行中任务
        echo "🔄 进行中任务\n";
        $activeFile = "$wbDir/agents/mayor/tasks/active.json";
        if (file_exists($activeFile)) {
            $activeData = json_decode(file_get_contents($activeFile), true);
            $tasks = $activeData['tasks'] ?? [];
            if (count($tasks) > 0) {
                foreach ($tasks as $task) {
                    $tid = $task['id'] ?? '?';
                    $desc = $task['description'] ?? '无描述';
                    $polecat = $task['polecat_id'] ?? '未知';
                    echo "   • $tid: $desc\n";
                    echo "     └─ Worker: $polecat\n";
                }
            } else {
                echo "   (无)\n";
            }
        } else {
            echo "   ❌ 未初始化\n";
        }
        echo "\n";

        // 最近提交
        echo "📝 最近提交\n";
        $gitLog = shell_exec('git --no-pager log --oneline -5 2>/dev/null');
        if ($gitLog !== null && !empty(trim($gitLog))) {
            echo $gitLog;
        } else {
            echo "   (非 Git 仓库)\n";
        }
        echo "\n";

        // 今日工作
        echo "📅 今日工作\n";
        $today = date('Y-m-d');
        $todayLog = "$memoryDir/$today.md";
        if (file_exists($todayLog)) {
            echo "   已记录 (见 $todayLog)\n";
        } else {
            echo "   (无记录)\n";
        }
        echo "\n";

        echo "💡 常用命令:\n";
        echo "   php gastown init     - 初始化\n";
        echo "   php gastown assign   - 分配任务\n";
        echo "   php gastown complete - 完成任务\n";
        echo "   php gastown status   - 查看状态\n";
    }
}
