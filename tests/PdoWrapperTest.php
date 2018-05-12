<?php

namespace PhpSting;


use Exception;
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
}
