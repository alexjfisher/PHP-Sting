<?php


namespace PhpSting;


class PhpSerializerFixtureLoader implements FixtureLoaderInterface
{

    public function loadFixture($fileLocation)
    {
        $fixture = NULL;
        $fileContents = file_get_contents($fileLocation);
        if ($fileContents !== False) {
            $fixture = unserialize($fileContents);
        }
        return $fixture;
    }

    public function saveFixture($fileLocation, $data)
    {
        $serializedFixture = serialize($data);
        if ($serializedFixture !== False) {
            file_put_contents($fileLocation, $serializedFixture);
        }
    }
}