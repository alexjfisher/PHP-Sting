<?php


namespace PhpSting;


class CsvFixtureLoader implements FixtureLoaderInterface
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
    public function loadFixture($csvFileLocation)
    {
        $fixture = [];
        $fileHandle = fopen($csvFileLocation, self::READ_MODE);
        if ($fileHandle !== FALSE) {
            $fixtureLabels = fgetcsv($fileHandle, $this->lineLength, $this->delimiter);

            while (($currentRow = fgetcsv($fileHandle, $this->lineLength, $this->delimiter)) !== FALSE) {
                $fixture[] = $this->loadFixtureRow($fixtureLabels, $currentRow);
            }

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
            $currentFixture[trim($currentLabel)] = trim($fixtureRow[$currentLabelKey]);
        }
        return $currentFixture;
    }

    /**
     * @param string $csvFileLocation
     * @param array $data
     */
    public function saveFixture($csvFileLocation, $data)
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