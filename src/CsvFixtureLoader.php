<?php


namespace PhpSting;


class CsvFixtureLoader
{
    const READ_MODE = 'r';
    const WRITE_MODE = 'w';
    const LINE_LENGTH = 1000;
    const DELIMITER = ',';

    private $lineLength;
    private $delimiter;

    /**
     * CustomFixtureLoader constructor.
     * @param int $lineLength
     * @param string $delimiter
     */
    public function __construct($lineLength = self::LINE_LENGTH, $delimiter = self::DELIMITER)
    {
        $this->lineLength = $lineLength;
        $this->delimiter = $delimiter;
    }

    /**
     * @param string $csvFileLocation
     * @return array
     */
    public function loadFixtureFromCSV($csvFileLocation)
    {
        $fixture = [];
        $fileHandle = fopen($csvFileLocation, self::READ_MODE);
        if ($fileHandle !== FALSE) {
            $fixtureLabels = fgetcsv($fileHandle, $this->lineLength, $this->delimiter);

            do {
                $currentRow = fgetcsv($fileHandle, $this->lineLength, $this->delimiter);
                $fixture[] = $this->loadFixtureRow($fixtureLabels, $currentRow);
            } while ($currentRow !== FALSE);

            fclose($fileHandle);
        }
        return $fixture;
    }

    /**
     * @param array $fixtureLabels
     * @param array $fixtureRow
     * @return array
     */
    private function loadFixtureRow($fixtureLabels, $fixtureRow)
    {
        $currentFixture = [];
        foreach ($fixtureLabels as $currentLabelKey => $currentLabel) {
            $currentFixture[$currentLabel] = $fixtureRow[$currentLabelKey];
        }
        return $currentFixture;
    }

    /**
     * @param string $csvFileLocation
     * @param array $data
     */
    public function saveFixtureToCSV($csvFileLocation, $data)
    {
        $fileHandle = fopen($csvFileLocation, self::WRITE_MODE);
        if ($fileHandle !== FALSE) {
            fputcsv($fileHandle, array_keys($data[0]));

            foreach ($data as $currentRow) {
                fputcsv($fileHandle, $currentRow);
            }

            fclose($fileHandle);
        }
    }
}