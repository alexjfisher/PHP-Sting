<?php

namespace PhpSting;


class CustomCsvFixtureLoaderTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURE_LOCATION = __DIR__ . '/_data/sample.csv';

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_loadFixtureFromCSV_success()
    {
        // given
        $loader = new CsvFixtureLoader();
        $fixture = 'a:5:{i:0;a:3:{s:6:"value1";s:1:"1";s:7:" value2";s:6:" apple";s:7:" value3";s:7:" Canada";}i:1;a:3:{s:6:"value1";s:1:"2";s:7:" value2";s:7:" banana";s:7:" value3";s:3:" UK";}i:2;a:3:{s:6:"value1";s:1:"3";s:7:" value2";s:7:" orange";s:7:" value3";s:4:" USA";}i:3;a:3:{s:6:"value1";s:1:"4";s:7:" value2";s:7:" cherry";s:7:" value3";s:7:" Poland";}i:4;a:3:{s:6:"value1";N;s:7:" value2";N;s:7:" value3";N;}}';

        // when + then
        $result = $loader->loadFixtureFromCSV(self::FIXTURE_LOCATION);
        $this->assertEquals(
            unserialize($fixture),
            $result
        );
    }
}
