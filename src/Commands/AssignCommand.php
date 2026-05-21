<?php
/**
 * AssignCommand - 分配任务给 Worker
 *
 * 对应原始 scripts/assign-task.sh
 * 用法: php gastown assign <task_id> <description> [context_file]
 */

namespace Gastown\Commands;

class AssignCommand
{
    /**
     * @param array $args
     */
    public function execute(array $args)
    {
        $taskId = $args[0] ?? '';
        $description = $args[1] ?? '';
        $contextFile = $args[2] ?? '';

        if (empty($taskId) || empty($description)) {
            echo "用法: php gastown assign <task_id> <description> [context_file]\n";
            echo "例:  php gastown assign gt-001 \"实现签到功能\" context.md\n";
            exit(1);
        }

        $workDir = GASTOWN_ROOT;
        $queueFile = "$workDir/.workbuddy/agents/mayor/tasks/queue.json";
        $activeFile = "$workDir/.workbuddy/agents/mayor/tasks/active.json";
        $polesDir = "$workDir/.workbuddy/agents/polecats";

        // 生成 polecat ID
        $polecatId = 'polecat-' . date('Ymd-His') . '-' . bin2hex(random_bytes(2));
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        // 创建 polecat 工作目录
        $polecatDir = "$polesDir/$polecatId";
        $this->ensureDir($polecatDir);

        // 创建 WORK.md
        $workMd = <<<MD
# Work Context - $polecatId

## 任务信息
- Task ID: $taskId
- 创建时间: $timestamp
- 来源: Mayor 分配
- 描述: $description

## 项目上下文
> 请参考项目根目录的 CLAUDE.md 获取完整上下文

## 当前任务
### 目标
$description

### 已完成
- [ ] (无)

### 进行中
- [ ] (准备开始)

### 阻塞
- 暂无

## 决策记录
- $timestamp: 任务已分配给 $polecatId

## 下一个步骤
1. 阅读 CLAUDE.md 了解项目上下文
2. 阅读 TASKS.md 了解详细任务要求
3. 开始执行任务
MD;

        file_put_contents("$polecatDir/WORK.md", $workMd);

        // 复制上下文文件（如有）
        if (!empty($contextFile) && file_exists($contextFile)) {
            copy($contextFile, "$polecatDir/context.md");
        }

        // 更新 active.json
        $taskData = [
            'id' => $taskId,
            'polecat_id' => $polecatId,
            'description' => $description,
            'assigned_at' => $timestamp,
            'status' => 'in_progress',
        ];

        $activeContent = json_decode(file_get_contents($activeFile), true);
        $activeContent['tasks'][] = $taskData;
        $activeContent['updated_at'] = $timestamp;
        file_put_contents($activeFile, json_encode($activeContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");

        // 更新 Mayor 状态
        $polecatCount = 0;
        if (is_dir($polesDir)) {
            $polecatCount = count(array_filter(scandir($polesDir), function ($item) use ($polesDir) {
                return $item !== '.' && $item !== '..' && is_dir("$polesDir/$item");
            }));
        }

        $stateData = [
            'status' => 'active',
            'last_activity' => $timestamp,
            'active_polecats' => $polecatCount,
            'current_task' => $taskId,
            'current_polecat' => $polecatId,
        ];
        file_put_contents("$workDir/.workbuddy/agents/mayor/state.json", json_encode($stateData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");

        echo "✅ 任务已分配\n\n";
        echo "📋 任务信息:\n";
        echo "   Task ID: $taskId\n";
        echo "   Polecat: $polecatId\n";
        echo "   工作目录: $polecatDir\n\n";
        echo "📝 工作上下文已创建: $polecatDir/WORK.md\n\n";
        echo "💡 下一步:\n";
        echo "   cd $polecatDir\n";
        echo "   # 启动 AI Agent 并指示其阅读 WORK.md\n";
    }

    private function ensureDir($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
