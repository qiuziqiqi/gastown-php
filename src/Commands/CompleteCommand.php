<?php
/**
 * CompleteCommand - 完成任务并触发交接
 *
 * 对应原始 scripts/complete-task.sh
 * 用法: php gastown complete <task_id> [summary]
 */

namespace Gastown\Commands;

class CompleteCommand
{
    /**
     * @param array $args
     */
    public function execute(array $args)
    {
        $taskId = $args[0] ?? '';
        $summary = $args[1] ?? '';

        if (empty($taskId)) {
            echo "用法: php gastown complete <task_id> [summary]\n";
            exit(1);
        }

        $workDir = GASTOWN_ROOT;
        $activeFile = "$workDir/.workbuddy/agents/mayor/tasks/active.json";
        $completedFile = "$workDir/.workbuddy/agents/mayor/tasks/completed.json";
        $polesDir = "$workDir/.workbuddy/agents/polecats";
        $memoryDir = "$workDir/.workbuddy/memory";
        $stateFile = "$workDir/.workbuddy/agents/mayor/state.json";

        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $today = date('Y-m-d');

        // 查找当前活动的 polecat
        $polecatId = '';
        if (file_exists($stateFile)) {
            $stateData = json_decode(file_get_contents($stateFile), true);
            $polecatId = $stateData['current_polecat'] ?? '';
        }

        // 更新 WORK.md（如 polecat 目录存在）
        if (!empty($polecatId) && is_dir("$polesDir/$polecatId")) {
            $workMd = "$polesDir/$polecatId/WORK.md";
            if (file_exists($workMd)) {
                $content = file_get_contents($workMd);
                $content = str_replace('- [ ] (准备开始)', "- [x] (已完成: $timestamp)", $content);
                file_put_contents($workMd, $content);
            }
        }

        // 移动任务到已完成
        $activeContent = json_decode(file_get_contents($activeFile), true);
        $completedContent = json_decode(file_get_contents($completedFile), true);

        $taskObj = null;
        $taskIndex = -1;
        foreach ($activeContent['tasks'] as $index => $task) {
            if ($task['id'] === $taskId) {
                $taskObj = $task;
                $taskIndex = $index;
                break;
            }
        }

        if ($taskObj !== null) {
            // 从 active 移除
            array_splice($activeContent['tasks'], $taskIndex, 1);
            $activeContent['updated_at'] = $timestamp;
            file_put_contents($activeFile, json_encode($activeContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");

            // 添加到 completed
            $taskObj['completed_at'] = $timestamp;
            $taskObj['summary'] = $summary;
            $completedContent['tasks'][] = $taskObj;
            $completedContent['updated_at'] = $timestamp;
            file_put_contents($completedFile, json_encode($completedContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        }

        // 更新工作日志
        $logFile = "$memoryDir/$today.md";
        if (file_exists($logFile)) {
            $logEntry = "\n\n### $taskId ($timestamp)\n$summary\n";
            file_put_contents($logFile, $logEntry, FILE_APPEND);
        } else {
            file_put_contents($logFile, "# $today\n\n### $taskId ($timestamp)\n$summary\n");
        }

        // 更新 Mayor 状态
        $polecatCount = 0;
        if (is_dir($polesDir)) {
            $polecatCount = count(array_filter(scandir($polesDir), function ($item) use ($polesDir) {
                return $item !== '.' && $item !== '..' && is_dir("$polesDir/$item");
            }));
        }

        $stateData = [
            'status' => 'idle',
            'last_activity' => $timestamp,
            'active_polecats' => $polecatCount,
            'total_completed' => count($completedContent['tasks']),
        ];
        file_put_contents($stateFile, json_encode($stateData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");

        echo "✅ 任务已完成: $taskId\n\n";
        echo "📋 完成摘要: $summary\n";
        echo "⏰ 完成时间: $timestamp\n\n";
        echo "📊 统计:\n";
        echo "   - 进行中: " . count($activeContent['tasks']) . " 个\n";
        echo "   - 已完成: " . count($completedContent['tasks']) . " 个\n";
    }
}
