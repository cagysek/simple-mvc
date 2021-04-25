<?php


namespace App\model\facade;


use App\Model\Repository\SettingsRepository;
use App\Model\Repository\StudentRepository;
use App\Model\Repository\TaskRepository;
use App\service\FileService;

class SettingsFacade
{

    private FileService $fileService;

    private StudentRepository $studentRepository;

    private SettingsRepository $settingsRepository;

    private TaskRepository $taskRepository;

    public function __construct()
    {
        $this->fileService = new FileService();
        $this->studentRepository = new StudentRepository();
        $this->settingsRepository = new SettingsRepository();
        $this->taskRepository = new TaskRepository();
    }


    public function getInputFiles() : array
    {
        return $this->fileService->getInputFiles();
    }

    public function loadTasks(string $filename) : bool
    {
        $data = $this->fileService->loadInputFile($filename);

        if (!$data)
        {
            return false;
        }

        if (!$this->isTaskFile($data[0]))
        {
            return false;
        }

        // posun o jednu pozici (odstraní header row)
        array_shift($data);

        $preparedData = $this->prepareTaskData($data);

        $this->taskRepository->insertTasks($preparedData);

        return true;
    }



    public function loadStudents(string $filename) : bool
    {
        $data = $this->fileService->loadInputFile($filename);

        if (!$data)
        {
            return false;
        }

        if (!$this->isStudentsFile($data[0]))
        {
            return false;
        }

        // posun o jednu pozici (odstraní header row)
        array_shift($data);

        $this->studentRepository->insertStudents($data);

        return true;
    }

    private function isStudentsFile(array $headerRow) : bool
    {
        $headers = ["osCislo", "jmeno", "prijmeni"];

        return $headers === $headerRow;
    }

    private function isTaskFile(array $headerRow) : bool
    {
       $headers = [
           "Predmet","Nazev tematu","Osobni cislo","Jmeno",
           "Nazev souboru","Pokus","Datum odevzdani",
           "Autom. validace-Vysledek","Autom. validace-URL",
           "Hodnoceni","Hodnoceni-pocet bodu","Hodnoceni-datum",
           "Hodnoceni-vyucujici","Hodnoceni-poznamka"
       ];

       return $headerRow === $headers;
    }

    private function prepareTaskData(array $originalData) : array
    {
        // načtu si studenty ve tvaru školní id => id v db kvůli klíči
        $students = $this->studentRepository->getStudentSchoolNumberIdMap();

        $data = [];

        foreach ($originalData as $row)
        {
            if (isset($students[$row[2]]))
            {
                // z názvu získám idčko úlohy
                preg_match('/OKS-\s*(\d+)/', $row[1], $matches);

                // odstranění nul ze začátku
                $name = ltrim($matches[1], '0');

                $submitted = new \DateTime($row[6]);

                $data[] = [
                    TaskRepository::COL_NAME => $name,
                    TaskRepository::COL_STUDENT_ID => $students[$row[2]],
                    TaskRepository::COL_SUBMITTED => $submitted->format('Y-m-d H:i'),
                    TaskRepository::COL_RESULT => $row[7] == "OK" ? 1 : 0
                ];
            }
        }

        return $data;
    }

    public function updateTotalTaskCount(int $totalTaskCount) : void
    {
        $this->settingsRepository->updateTotalTaskCount($totalTaskCount);
    }
}