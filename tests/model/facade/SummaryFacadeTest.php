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

    }

    public function testGetStudentTableData()
    {

    }

    public function testGetGraphDataForStudent()
    {

    }


    public function testGetProgressBarData()
    {

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
                ['hour' => 0, 'count' => 20],
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

        $this->assertEquals($expected, $data);
    }
}
