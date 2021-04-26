<?php

/**
 * Fasáda pro obsluhu požadavků spojených s nastavením aplikace
 */

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

    /**
     * Načte vstupní soubory ze složky "input"
     *
     * @return array
     */
    public function getInputFiles() : array
    {
        return $this->fileService->getInputFiles();
    }

    /**
     * Obsluha pro zpracování souboru s úlohami
     *
     * @param string $filename
     * @return bool
     */
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


    /**
     * Obsluha pro zpracování souboru se studenty
     *
     * @param string $filename
     * @return bool
     */
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

        $preparedData = $this->prepareStudentData($data);

        $this->studentRepository->insertStudents($preparedData);

        return true;
    }

    /**
     * Kontrola jestli soubor se studenty obsahuje požadované hlavičky
     *
     * @param array $headerRow
     * @return bool
     */
    private function isStudentsFile(array $headerRow) : bool
    {

        $headersMushHave = ["osCislo", "jmeno", "prijmeni"];

        return count(array_diff($headersMushHave, $headerRow)) == 0;
    }

    /**
     * Kontrola jestli soubor s úlohami obsahuje požadované hlavičky
     *
     * @param array $headerRow
     * @return bool
     */
    private function isTaskFile(array $headerRow) : bool
    {
       $headersMushHave = [
           "Nazev tematu","Osobni cislo","Datum odevzdani",
           "Autom. validace-Vysledek"
       ];

        return count(array_diff($headersMushHave, $headerRow)) == 0;
    }

    /**
     * Preprocessing dat pro uložení studentů do db
     *
     * @param array $originalData
     * @return array
     */
    private function prepareStudentData(array $originalData) : array
    {
        $data = [];

        foreach ($originalData as $row)
        {
            $data[] = [
                StudentRepository::COL_SCHOOL_NUMBER => $row[0],
                StudentRepository::COL_FIRSTNAME => $row[1],
                StudentRepository::COL_LASTNAME => $row[2],
            ];
        }

        return $data;
    }

    /**
     * Preprocessing dat pro uložení úloh do DB
     *
     * @param array $originalData
     * @return array
     * @throws \Exception
     */
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

    /**
     * Aktualizace počtu úloh
     *
     * @param int $totalTaskCount
     */
    public function updateTotalTaskCount(int $totalTaskCount) : void
    {
        $this->settingsRepository->updateTotalTaskCount($totalTaskCount);
    }

    /**
     * Resetování/Inicializace tabulek
     */
    public function initDatabase() : void
    {
        $this->taskRepository->dropTable();
        $this->settingsRepository->dropTable();
        $this->studentRepository->dropTable();

        $this->studentRepository->createTable();
        $this->settingsRepository->createTable();
        $this->taskRepository->createTable();
    }

    /**
     * Kontrola existence tabulek v DB
     */
    public function checkDatabase() : void
    {
        if (!$this->settingsRepository->isTableExists())
        {
            $this->initDatabase();
        }
    }
}