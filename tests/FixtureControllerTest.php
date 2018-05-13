<?php

namespace PhpSting;


use Exception;
use PDO;
use PHPUnit_Framework_TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;

class FixtureControllerTest extends PHPUnit_Framework_TestCase
{
    const FIXTURE_LOCATION = __DIR__ . '/_data/test.sql';

    /** @var  ObjectProphecy|PdoWrapper **/
    private $dbProphecy;

    /** @var Prophet **/
    private $prophecy;

    /** @var array **/
    private $credentials;

    /** @var PdoWrapper **/
    private $db;

    /** @var FixtureController **/
    private $controller;

    public function setUp()
    {
        $this->prophecy = new Prophet();
        $this->dbProphecy = $this->prophecy->prophesize(PdoWrapper::class);
        $this->credentials = json_decode(file_get_contents(__DIR__ . "/../credentials.json"), true);

        // given
        $this->db = new PdoWrapper(
            $this->credentials['host'],
            $this->credentials['database'],
            $this->credentials['username'],
            $this->credentials['password']);

        $this->controller = new FixtureController($this->db);
    }

    public function tearDown()
    {
    }

    /**
     * @dataProvider dataProvider_test_applyDatabaseFixture_throwsExceptionCases
     *
     * @param string $fixtureFileLocation
     */
    public function test_applyDatabaseFixture_throwsExceptionCases($fixtureFileLocation)
    {
        // given
        $controller = new FixtureController($this->dbProphecy->reveal());

        // then
        $this->expectException(Exception::class);

        // when
        $controller->applyDatabaseFixture($fixtureFileLocation);
    }

    public function dataProvider_test_applyDatabaseFixture_throwsExceptionCases()
    {
        return [
            "incorrect_file_name" => ["/_data/bad_file_name"],
            "file_not_exists" => ["/_data/does_not_exists.sql"]
        ];
    }

    public function test_applyDatabaseFixture()
    {
        // when
        $this->controller->applyDatabaseFixture(self::FIXTURE_LOCATION);

        // then
        $this->assertEquals(
            ["test_table"],
            $this->db->runSqlStatement("SHOW TABLES LIKE '%test_table%'", PDO::FETCH_COLUMN, 0)
        );
    }

    public function test_getRecordsFromTableAsArray_success()
    {
        // given
        $expectedResult = 'a:1:{i:0;a:6:{s:2:"id";s:1:"1";s:3:"day";s:6:"Monday";s:5:"month";s:7:"January";s:4:"date";s:19:"2018-05-12 00:00:00";s:6:"flag_1";s:1:"1";s:6:"flag_2";s:1:"1";}}';
        $this->controller->applyDatabaseFixture(self::FIXTURE_LOCATION);

        // when
        $result = $this->controller->getRecordsFromTableAsArray('test_table', 1);

        // then
        $this->assertEquals(
            unserialize($expectedResult),
            $result
        );
    }

    public function test_cleanup_success()
    {
        // given
        $this->controller->applyDatabaseFixture(self::FIXTURE_LOCATION);

        // when
        $this->controller->cleanup();

        // then
        $this->assertEquals(
            [],
            $this->db->runSqlStatement("SHOW TABLES LIKE '%test_table%'", PDO::FETCH_COLUMN, 0)
        );
    }
}
