<?php

namespace PhpSting;


use Exception;
use PDO;
use PHPUnit_Framework_TestCase;

class PdoWrapperTest extends PHPUnit_Framework_TestCase
{
    /** @var array **/
    private $credentials;

    public function setUp()
    {
        $this->credentials = json_decode(file_get_contents(__DIR__ . "/../credentials.json"), true);
    }

    public function tearDown()
    {
    }

    public function test_connect_throwsExceptionOnConnectionFailure()
    {
        // given
        $db = new PdoWrapper('wrong', 'wrong', 'wrong', 'wrong');

        // then
        $this->expectException(Exception::class);

        // when
        $db->connect();
    }

    public function test_isConnected_returnFalseWhenNotConnectedToDb()
    {
        // given
        $db = new PdoWrapper('', '', '', '');

        // when + then
        $this->assertFalse($db->isConnected());
    }

    public function test_isConnected_returnTrueWhenConnectedToDb()
    {
        // given
        $db = new PdoWrapper(
            $this->credentials['host'],
            $this->credentials['database'],
            $this->credentials['username'],
            $this->credentials['password']);

        // when
        $db->connect();

        // then
        $this->assertTrue($db->isConnected());
    }

    public function test_runSqlStatement_invalidFixture()
    {
        // given
        $sqlFileA = new SqlFileStream(__DIR__ . '/_data/exception_in_fixture_a.sql');
        $sqlFileB = new SqlFileStream(__DIR__ . '/_data/exception_in_fixture_b.sql');
        $db = new PdoWrapper(
            $this->credentials['host'],
            $this->credentials['database'],
            $this->credentials['username'],
            $this->credentials['password']);
        $db->connect();


        // then
        $this->expectException(Exception::class);

        // when
        $db->applyFixture($sqlFileA);
        $db->applyFixture($sqlFileB);
    }

    public function test_runSqlStatement_throwExceptionOnQueryFailure()
    {
        // given
        $sqlFile = new SqlFileStream(__DIR__ . '/_data/exception_in_fixture_a.sql');
        $db = new PdoWrapper(
            $this->credentials['host'],
            $this->credentials['database'],
            $this->credentials['username'],
            $this->credentials['password']);
        $db->connect();
        $db->applyFixture($sqlFile);

        // then
        $this->expectException(Exception::class);

        // when
        $db->runSqlStatement("INSERT INTO test VALUES (1);");
    }
}
