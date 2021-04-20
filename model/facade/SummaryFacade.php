<?php


namespace App\Model\Facade;


use App\Model\Repository\SettingsRepository;
use App\Model\Repository\TaskRepository;

class SummaryFacade
{
    private SettingsRepository $settingsRepository;

    private TaskRepository $taskRepository;

    public function __construct()
    {
        $this->settingsRepository = new SettingsRepository();
        $this->taskRepository = new TaskRepository();
    }


    public function getStudentTableData(int $studentId) : array
    {
        $taskRows = $this->taskRepository->getStudentTasksSummary($studentId);


        $data = [];

        $maxVal = 0;

        foreach ($taskRows as $row)
        {
            $data["student"][$row['name']] = [
                'endDate' => $row["end_date"],
                'startDate' => $row["start_date"],
                'unsuccessfulAttempt' => $row["unsuccessful_attempt"],
                'successfulAttempt' => $row["successful_attempt"],
                'successRate' => $row["success_rate"],
                'total' => $row["total"],
            ];

            if ($maxVal < $row['total'])
            {
                $maxVal = $row['total'];
            }
        }

        $taskTotalRows = $this->taskRepository->getStudentTasksTotalSummary($studentId);

        $data["total"] = [
            "total" => $taskTotalRows[0]["total"],
            "endDate" => $taskTotalRows[0]["end_date"],
            "startDate" => $taskTotalRows[0]["start_date"],
            "unsuccessfulAttempt" => $taskTotalRows[0]["unsuccessful_attempt"],
            "successfulAttempt" => $taskTotalRows[0]["successful_attempt"],
            "successRate" => $taskTotalRows[0]["success_rate"],
            "maxAttempt" => $maxVal,
        ];


        return $data;
    }

    public function getProgressBarData(int $studentId) : array
    {
        $taskData = $this->taskRepository->getProgressBarData($studentId);

        $data = [
            "successful" => 0,
            "unsuccessful" => 0,
        ];

        foreach ($taskData as $task)
        {
            if (isset($task["successful"]) && $task["successful"] > 0)
            {
                $data["successful"] += 1;
            }
            elseif (isset($task["unsuccessful"]) && $task["unsuccessful"] > 0)
            {
                $data["unsuccessful"] += 1;
            }
        }

        return $data;

    }

    public function getGraphDataForStudent(int $studentId) : array
    {
        $data = [];

        for ($i = 0 ; $i < 24 ; $i++)
        {
            $data[$i] = 0;
        }

        $attemptsTimes = $this->taskRepository->getStudentAttemptsTimes($studentId);

        foreach ($attemptsTimes as $time)
        {
            $data[$time['hour']] = $time['count'];
        }

        return $data;
    }






}