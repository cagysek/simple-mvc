<?php

/**
 * Fasáda pro příprava dat pro statistiky
 */

namespace App\Model\Facade;


use App\Model\Repository\SettingsRepository;
use App\Model\Repository\StudentRepository;
use App\Model\Repository\TaskRepository;

class SummaryFacade
{
    private SettingsRepository $settingsRepository;

    private TaskRepository $taskRepository;

    private StudentRepository $studentRepository;

    public function __construct()
    {
        $this->settingsRepository = new SettingsRepository();
        $this->taskRepository = new TaskRepository();
        $this->studentRepository = new StudentRepository();
    }

    /**
     * Vytvoření datasetu pro tabulku s daty studenta
     *
     * @param int $studentId
     * @return array
     */
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
            "total" => $taskTotalRows[0]["total"] ?? 0,
            "endDate" => $taskTotalRows[0]["end_date"] ?? NULL,
            "startDate" => $taskTotalRows[0]["start_date"] ?? NULL,
            "unsuccessfulAttempt" => $taskTotalRows[0]["unsuccessful_attempt"] ?? 0,
            "successfulAttempt" => $taskTotalRows[0]["successful_attempt"] ?? 0,
            "successRate" => $taskTotalRows[0]["success_rate"] ?? 0,
            "maxAttempt" => $maxVal ?? 0,
        ];


        return $data;
    }

    /**
     * Příprava dat pro progress bar na detailu statistik studenta
     *
     * @param int $studentId
     * @return int[]
     */
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

        $data['total'] = $this->settingsRepository->getTaskTotalCount();

        return $data;

    }

    /**
     * Příprava dat pro graf hodin ve kterých student odevzdává
     *
     * @param int $studentId
     * @return array
     */
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

    /**
     * Příprava datasetu pro celkový přehled
     *
     * @return array
     */
    public function getOverviewData() : array
    {
        $totalTaskNameCount = $this->taskRepository->getMaxTaskNumber();

        $overviewTableData = $this->taskRepository->getOverviewData();

        $maxStudentAttemptsPerTask = $this->taskRepository->getOverviewMaxStudentAttemptsPerTask();

        $data = [];

        $totalStudentsSurrenderCount = 0;
        $maxStudentAttemptsTotal = 0;
        $averageAttemptsPerStudentTotal = 0;

        foreach ($overviewTableData as $task)
        {
            $studentSurrender = ($task['student_try_count'] ?? 0) - ($task['students_success_count'] ?? 0);

            $totalStudentsSurrenderCount += $studentSurrender;

            $maxStudentsAttempt = $maxStudentAttemptsPerTask[$task['name']];
            if ($maxStudentsAttempt > $maxStudentAttemptsTotal)
            {
                $maxStudentAttemptsTotal = $maxStudentsAttempt;
            }

            $averageAttemptsPerStudentTotal += $task['avg_per_student'];


            $data["task"][$task['name']] = [
                'studentsTryCount' => $task['student_try_count'],
                'studentsSuccessCount' => $task['students_success_count'],
                'studentsSurrenderCount' => $studentSurrender > 0 ? $studentSurrender : '-',
                'total' => $task['total'],
                'isNotOkAttempts' => $task['is_not_ok_attempts'] ?? '-',
                'isOkAttempts' => $task['is_ok_attempts'],
                'successRate' => round($task['succ_rate'] * 100),
                'averageAttemptsPerStudent' => round($task['avg_per_student']),
                'maxStudentAttempts' => $maxStudentsAttempt,
            ];
        }


        $overviewTableTotalData = $this->taskRepository->getOverviewDataTotal();

        $data["total"] = [
            'studentsTryCount' => $overviewTableTotalData['student_try_count'],
            'studentsSurrenderCount' => $totalStudentsSurrenderCount,
            'total' => $overviewTableTotalData['total'],
            'isNotOkAttempts' => $overviewTableTotalData['is_not_ok_attempts'],
            'isOkAttempts' => $overviewTableTotalData['is_ok_attempts'],
            'successRate' => round($overviewTableTotalData['succ_rate'] * 100),
            'maxStudentAttempts' => $maxStudentAttemptsTotal,
            'averageAttemptsPerStudent' => $totalTaskNameCount > 0 ? round($averageAttemptsPerStudentTotal / $totalTaskNameCount) : 0,
        ];



        return $data;
    }

    /**
     * Příprava dat pro graf s celkovým přehledem hodin ve kterých studenti odevzávají
     *
     * @return array
     */
    public function getGraphDataForOverview() : array
    {
        $data = [];

        for ($i = 0 ; $i < 24 ; $i++)
        {
            $data[$i] = 0;
        }

        $attemptsTimes = $this->taskRepository->getAttemptsTimes();

        foreach ($attemptsTimes as $time)
        {
            $data[$time['hour']] = $time['count'];
        }

        return $data;
    }




}