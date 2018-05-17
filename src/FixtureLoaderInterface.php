<?php


namespace PhpSting;


interface FixtureLoaderInterface
{
    /**
     * @param string $fileLocation
     * @return array
     */
    public function loadFixture($fileLocation);

    /**
     * @param string $fileLocation
     * @param array $data
     */
    public function saveFixture($fileLocation, $data);
}