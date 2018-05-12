<?php

namespace PhpSting;


use PHPUnit_Framework_TestCase;

class SqlFileStreamTest extends PHPUnit_Framework_TestCase
{
    const FIXTURE_LOCATION = __DIR__ . '/_data/test.sql';

    /** @var SqlFileStream **/
    private $sqlFile;

    public function setUp()
    {
        // given
        $this->sqlFile = new SqlFileStream(self::FIXTURE_LOCATION);
    }

    public function tearDown()
    {
    }

    public function test_next_incrementsPointerByOneWhenItsCalled()
    {
        // when
        $this->sqlFile->next();

        // then
        $this->assertSame(
            1,
            $this->sqlFile->key()
        );
    }

    public function test_current_LoadsQueryWhenItsCalled()
    {
        // when
        $this->sqlFile->next();

        // then
        $this->assertEquals(
            'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";',
            $this->sqlFile->current()
        );
    }

    public function test_valid_returnFalseOnJustOpenedFile()
    {
        // when + then
        $this->assertFalse($this->sqlFile->valid());
    }

    public function test_valid_returnTrueWhenCurrentQueryIsAvailable()
    {
        // when
        $this->sqlFile->next();

        // then
        $this->assertTrue($this->sqlFile->valid());
    }

    /**
     * @dataProvider dataProviderFor_test_formatSqlQuery
     *
     * @param string $sampleString
     * @param string $expectedOutput
     */
    public function test_formatSqlQuery($sampleString, $expectedOutput)
    {
        // when + then
        $this->assertEquals(
            $expectedOutput,
            $this->sqlFile->formatSqlQuery($sampleString)
        );
    }

    public function dataProviderFor_test_formatSqlQuery()
    {
        return [
            "mysql comment" => ["-- ---------------------", ""],
            "new line" => ["\n", ""],
            "comment and new line" => ["--\n", ""],
            "correct query" => ["CREATE TABLE IF NOT EXISTS `test` ()\n", "CREATE TABLE IF NOT EXISTS `test` ();"],
            "query with comments" => [
                "  PRIMARY KEY (`id`)\n -- a crap comment\n) ENGINE=InnoDB DEFAULT CHARSET=utf8\n",
                "PRIMARY KEY (`id`)\n\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
            ]
        ];
    }

    public function test_checkWholeFile()
    {
        // given
        $expectedQueries = unserialize(file_get_contents(__DIR__ . '/_data/expected_query.txt'));

        // when
        $result = [];
        foreach ($this->sqlFile as $current) {
            $result[] = $current;
        }

        // then
        $this->assertEquals(
            $expectedQueries,
            $result
        );
    }
}
