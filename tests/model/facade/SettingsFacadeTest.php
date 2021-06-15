<?php

namespace model\facade;

use App\model\facade\SettingsFacade;
use App\Model\Facade\SummaryFacade;
use App\Model\Repository\SettingsRepository;
use App\Model\Repository\StudentRepository;
use App\Model\Repository\TaskRepository;
use App\service\FileService;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\TestCase;

class SettingsFacadeTest extends TestCase
{

    private ?StudentRepository $studentRepository;
    private FileService $fileService;
    private ?TaskRepository $taskRepository;
    private ?SettingsRepository $settingsRepository;
    private ?SettingsFacade $settingsFacade;


    public function __construct()
    {
        parent::__construct();

        $this->studentRepository = $this->createMock(StudentRepository::class);
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->settingsRepository = $this->createMock(SettingsRepository::class);
        $this->fileService = $this->createMock(FileService::class);

        $this->settingsFacade = new SettingsFacade();

        $this->settingsFacade->setFileService($this->fileService);
        $this->settingsFacade->setTaskRepository($this->taskRepository);
        $this->settingsFacade->setStudentRepository($this->studentRepository);
        $this->settingsFacade->setSettingsRepository($this->settingsRepository);
    }

    public function testLoadTasks()
    {
        $this->fileService
            ->method('loadInputFile')
            ->willReturn([
                ['Predmet','Nazev tematu','Osobni cislo','Jmeno','Nazev souboru','Pokus','Datum odevzdani','Autom. validace-Vysledek','Validator'],
                ['KIV/OKS','OKS-01 - oks-01.jar','A10B0001P','RŮŽOVÁ Anita','oks-01.jar','1','02.03.2021 16:35','Špatné výsledky','http://validator.zcu.cz'],
                ['KIV/OKS','OKS-02 - oks-02.jar','A10B1111P','SIVÝ Charlie','oks-02.jar','1','09.03.2021 18:38','Jiná chyba','http://validator.zcu.cz'],
                ['KIV/OKS','OKS-01 - oks-01.jar','A10B4321P','ŠEDIVÝ Emil','oks-01.jar','1','03.03.2021 22:42','Jiná chyba','http://validator.zcu.cz']
            ]);

        $this->studentRepository
            ->method('getStudentSchoolNumberIdMap')
            ->willReturn(
                ['A10B0001P' => 1],
                ['A10B8765P' => 2],
                ['A10B1111P' => 3],
                ['A10B4321P' => 4],
                ['A10B9999P' => 5]
            );


        $this->assertEquals(true, $this->settingsFacade->loadTasks(''));
    }

    public function testLoadTasksNoData()
    {
        $this->fileService
            ->method('loadInputFile')
            ->willReturn([]);

        $this->assertEquals(false, $this->settingsFacade->loadTasks(''));
    }

    public function testLoadTasksOnlyHeaders()
    {
        $this->fileService
            ->method('loadInputFile')
            ->willReturn([
                ['Predmet','Nazev tematu','Osobni cislo','Jmeno','Nazev souboru','Pokus','Datum odevzdani','Autom. validace-Vysledek','Validator']
            ]);

        $this->assertEquals(false, $this->settingsFacade->loadTasks(''));
    }

    public function testLoadTasksNoHeaders()
    {
        $this->fileService
            ->method('loadInputFile')
            ->willReturn([
                ['KIV/OKS','OKS-01 - oks-01.jar','A10B0001P','RŮŽOVÁ Anita','oks-01.jar','1','02.03.2021 16:35','Špatné výsledky','http://validator.zcu.cz'],
                ['KIV/OKS','OKS-02 - oks-02.jar','A10B1111P','SIVÝ Charlie','oks-02.jar','1','09.03.2021 18:38','Jiná chyba','http://validator.zcu.cz'],
                ['KIV/OKS','OKS-01 - oks-01.jar','A10B4321P','ŠEDIVÝ Emil','oks-01.jar','1','03.03.2021 22:42','Jiná chyba','http://validator.zcu.cz']
            ]);

        $this->assertEquals(false, $this->settingsFacade->loadTasks(''));
    }

    public function testLoadStudents()
    {
        $this->fileService
            ->method('loadInputFile')
            ->willReturn([
                ['osCislo', 'jmeno', 'prijmeni'],
                ['A10B0001P', 'Anita', 'RŮŽOVÁ'],
                ['A10B8765P', 'Cyril', 'ŽLUTÝ'],
                ['A10B1111P', 'Charlie', 'SIVÝ']
            ]);
        
        $this->assertEquals(true, $this->settingsFacade->loadStudents(''));
    }

    public function testLoadStudentsNoData()
    {
        $this->fileService
            ->method('loadInputFile')
            ->willReturn([]);

        $this->assertEquals(false, $this->settingsFacade->loadStudents(''));
    }

    public function testLoadStudentsNoHeaders()
    {
        $this->fileService
            ->method('loadInputFile')
            ->willReturn([
                ['A10B0001P', 'Anita', 'RŮŽOVÁ'],
                ['A10B8765P', 'Cyril', 'ŽLUTÝ'],
                ['A10B1111P', 'Charlie', 'SIVÝ']
            ]);

        $this->assertEquals(false, $this->settingsFacade->loadStudents(''));
    }

    public function testGetInputFiles()
    {
        $this->fileService
            ->method('getInputFiles')
            ->willReturn(['1', '2']);

        $inputFiles = $this->settingsFacade->getInputFiles();

        $this->assertIsArray($inputFiles);
        $this->assertEquals(2, sizeof($inputFiles));
    }
}
