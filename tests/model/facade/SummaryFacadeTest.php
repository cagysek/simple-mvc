<?php

namespace Model\Facade;

use App\Model\Facade\SummaryFacade;
use App\Model\Repository\SettingsRepository;
use App\Model\Repository\StudentRepository;
use App\Model\Repository\TaskRepository;
use PHPUnit\Framework\TestCase;

class SummaryFacadeTest extends TestCase
{
    private ?StudentRepository $studentRepository;
    private ?TaskRepository $taskRepository;
    private ?SettingsRepository $settingsRepository;
    private ?SummaryFacade $summaryFacade;


    public function __construct()
    {
        parent::__construct();

        $this->studentRepository = $this->createMock(StudentRepository::class);
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->settingsRepository = $this->createMock(SettingsRepository::class);


        $this->summaryFacade = new SummaryFacade();

        $this->summaryFacade->setTaskRepository($this->taskRepository);
        $this->summaryFacade->setStudentRepository($this->studentRepository);
        $this->summaryFacade->setSettingsRepository($this->settingsRepository);
    }

    public function testGetOverviewData()
    {
        $this->taskRepository
            ->method('getMaxTaskNumber')
            ->willReturn(3);

        $this->taskRepository
            ->method('getOverviewData')
            ->willReturn([
                ['name' => 1, 'total' => 14, 'is_ok_attempts' => 2, 'is_not_ok_attempts' => 12, 'succ_rate' => 0.1429, 'students_success_count' => 2, 'student_try_count' => 3, 'avg_per_student' => 4.6667],
                ['name' => 2, 'total' => 5, 'is_ok_attempts' => 4, 'is_not_ok_attempts' => 1, 'succ_rate' => 0.8000, 'students_success_count' => 3, 'student_try_count' => 3, 'avg_per_student' => 1.6667],
                ['name' => 3, 'total' => 3, 'is_ok_attempts' => 2, 'is_not_ok_attempts' => 1, 'succ_rate' => 0.6667, 'students_success_count' => 2, 'student_try_count' => 3, 'avg_per_student' => 1.0000],
            ]);

        $this->taskRepository
            ->method('getOverviewMaxStudentAttemptsPerTask')
            ->willReturn(
                [1 => 10, 2 => 3, 3 => 1]
            );

        $this->taskRepository
            ->method('getOverviewDataTotal')
            ->willReturn(
                ['name' => 1, 'total' => 22, 'is_ok_attempts' => 8, 'is_not_ok_attempts' => 14, 'succ_rate' => 0.3636, 'student_try_count' => 3]
            );

        $data = $this->summaryFacade->getOverviewData();

        $this->assertIsArray($data);
        $this->assertEquals(2, sizeof($data));
        $this->assertEquals(3, sizeof($data['task']));
        $this->assertEquals($data['total']['studentsTryCount'], sizeof($data['task']));

        $total = 0;
        $ok = 0;
        $notOk = 0;

        foreach ($data['task'] as $task) {

            $ok += $task['isOkAttempts'];
            $notOk += $task['isNotOkAttempts'];
            $total += $task['total'];
        }

        $this->assertEquals($data['total']['total'], $total);
        $this->assertEquals($data['total']['isOkAttempts'], $ok);
        $this->assertEquals($data['total']['isNotOkAttempts'], $notOk);
    }

    public function testGetStudentTableData()
    {
        $this->taskRepository
            ->method('getStudentTasksSummary')
            ->willReturn([
                ['name' => 1, 'total' => 2, 'end_date' => '2021-03-02 16:37:00', 'start_date' => '2021-03-02 16:35:00', 'unsuccessful_attempt' => 1, 'successful_attempt' => 1, 'success_rate' => 0.5],
                ['name' => 2, 'total' => 1, 'end_date' => '2021-03-05 19:11:00', 'start_date' => '2021-03-05 19:11:00', 'unsuccessful_attempt' => 0, 'successful_attempt' => 1, 'success_rate' => 1],
            ]);

        $this->taskRepository
            ->method('getStudentTasksTotalSummary')
            ->willReturn([
                ['total' => 3, 'end_date' => '2021-03-05 19:11:00', 'start_date' => '2021-03-02 16:35:00', 'unsuccessful_attempt' => 1, 'successful_attempt' => 2, 'success_rate' => 0.6667],
            ]);

        $data = $this->summaryFacade->getStudentTableData(0);

        $this->assertIsArray($data);
        $this->assertEquals(2, sizeof($data));
        $this->assertEquals(2, sizeof($data['student']));

        $total = 0;
        $ok = 0;
        $notOk = 0;

        foreach ($data['student'] as $task) {

            $total += $task['total'];
            $ok += $task['successfulAttempt'];
            $notOk += $task['unsuccessfulAttempt'];
        }

        $this->assertEquals($data['total']['total'], $total);
        $this->assertEquals($data['total']['successfulAttempt'], $ok);
        $this->assertEquals($data['total']['unsuccessfulAttempt'], $notOk);
    }

    public function testGetGraphDataForStudent()
    {
        $this->taskRepository
            ->method('getStudentAttemptsTimes')
            ->willReturn([
                ['hour' => 16, 'count' => 2],
                ['hour' => 19, 'count' => 1]
            ]);

        $data = $this->summaryFacade->getGraphDataForStudent(0);

        $expected = [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 2,
            17 => 0,
            18 => 0,
            19 => 1,
            20 => 0,
            21 => 0,
            22 => 0,
            23 => 0
        ];

        $this->assertIsArray($data);
        $this->assertEquals($expected, $data);
    }

    public function testGetGraphDataForStudentNoAttemptsTimes()
    {
        $this->taskRepository
            ->method('getStudentAttemptsTimes')
            ->willReturn([]);

        $data = $this->summaryFacade->getGraphDataForStudent(0);

        $this->assertEquals([], $data);
    }

    public function testGetProgressBarData()
    {
        $this->taskRepository
            ->method('getProgressBarData')
            ->willReturn([
                ['unsuccessful' => 1, 'successful' => 0],
                ['unsuccessful' => 0, 'successful' => 1],
                ['unsuccessful' => 0, 'successful' => 1]
            ]);

        $this->settingsRepository
            ->method('getTaskTotalCount')
            ->willReturn(3);

        $data = $this->summaryFacade->getProgressBarData(0);

        $this->assertIsArray($data);
        $this->assertEquals(3, sizeof($data));
        $this->assertEquals(1, $data['unsuccessful']);
        $this->assertEquals(2, $data['successful']);
    }

    public function testGetGraphDataForOverview()
    {
        $this->taskRepository
            ->method('getAttemptsTimes')
            ->willReturn([
                ['hour' => 1, 'count' => 2],
                ['hour' => 5, 'count' => 10],
                ['hour' => 23, 'count' => 23],
                ['hour' => 8, 'count' => 7],
                ['hour' => 0, 'count' => 20]
            ]);

        $data = $this->summaryFacade->getGraphDataForOverview();

        $expected = [
            0 => 20,
            1 => 2,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 10,
            6 => 0,
            7 => 0,
            8 => 7,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 0,
            17 => 0,
            18 => 0,
            19 => 0,
            20 => 0,
            21 => 0,
            22 => 0,
            23 => 23,
        ];

        $this->assertIsArray($data);
        $this->assertEquals($expected, $data);
    }
}
