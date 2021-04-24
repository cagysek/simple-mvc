<?php


namespace App\model\facade;


use App\Model\Repository\SettingsRepository;
use App\Model\Repository\StudentRepository;
use App\service\FileService;

class SettingsFacade
{

    private FileService $fileService;

    private StudentRepository $studentRepository;

    private SettingsRepository $settingsRepository;

    public function __construct()
    {
        $this->fileService = new FileService();
        $this->studentRepository = new StudentRepository();
        $this->settingsRepository = new SettingsRepository();
    }


    public function getInputFiles() : array
    {
        return $this->fileService->getInputFiles();
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

    private function isStudentsFile($headerRow) : bool
    {
        $headers = ["osCislo", "jmeno", "prijmeni"];

        return $headers === $headerRow;
    }

    public function updateTotalTaskCount(int $totalTaskCount) : void
    {
        $this->settingsRepository->updateTotalTaskCount($totalTaskCount);
    }
}