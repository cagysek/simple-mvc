<?php

/**
 * Service pro načítání souborů
 */

namespace App\service;


class FileService
{

    private string $inputFolder;

    /**
     * FileService constructor.
     */
    public function __construct()
    {
        $config = include(__DIR__ . '/../config/env.php');

        $this->inputFolder = $config['input_folder'];
    }

    /**
     * Získání vstupní složky
     *
     * @return array
     */
    public function getInputFiles() : array
    {
        return array_diff(scandir($this->inputFolder), ['..', '.']);
    }

    /**
     * Načtení souboru
     *
     * @param string $filename
     * @return array
     */
    public function loadInputFile(string $filename) : array
    {
        $filepath = $this->inputFolder . $filename;

        if (!file_exists($filepath))
        {
            return [];
        }

        $data = [];

        $handler = fopen($filepath, 'r');

        while ($row = fgetcsv($handler))
        {
            $inputLine =  $row[0];

            // převod na utf 8
            $inputLineUtf8 = iconv('WINDOWS-1250', 'UTF-8', $inputLine);

            $data[] = explode(';', str_replace("\"", '', $inputLineUtf8));
        }


        fclose($handler);

        return $data;
    }

}