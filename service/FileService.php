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
            $data[] = explode(';', str_replace("\"", '', $row[0]));
        }

        fclose($handler);

        return $data;
    }

}