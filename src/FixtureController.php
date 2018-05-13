<?php

namespace PhpSting;

use Exception;
use PDO;

class FixtureController
{
    /**
     * @var PdoWrapper
     */
    private $db;

    const FIXTURE_FILE_PATTERN = '/(^.*\/)([A-Za-z0-9_\-\+]*)(.sql$)/';

    /**
     * @var string
     */
    private $fixtureFileLocation;

    /**
     * @var SqlFileStream
     */
    private $sqlFixture;

    /**
     * FixtureController constructor.
     * @param PdoWrapper $db
     */
    public function __construct(PdoWrapper $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $fixtureFileLocation
     * @throws Exception
     */
    public function applyDatabaseFixture($fixtureFileLocation)
    {
        if ($this->checkIfFixtureExists($fixtureFileLocation)) {
            $this->fixtureFileLocation = $fixtureFileLocation;
        }

        $this->sqlFixture = new SqlFileStream($this->fixtureFileLocation);
        $this->db->connect();
        $this->db->applyFixture($this->sqlFixture);
    }

    /**
     * @param string $fixtureFileLocation
     * @return bool
     * @throws Exception
     */
    private function checkIfFixtureExists($fixtureFileLocation)
    {
        if (preg_match(self::FIXTURE_FILE_PATTERN, $fixtureFileLocation) === 0) {
            throw new Exception('Invalid file name.');
        }
        if (! file_exists($fixtureFileLocation)) {
            throw new Exception('Fixture file does not exists.');
        }
        return true;
    }

    /**
     * @param string $tableName
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function getRecordsFromTableAsArray($tableName, $limit = null) {
        $this->db->connect();

        $sql = sprintf("SELECT * FROM test.%s;", $tableName);
        if (! is_null($limit)) {
            $sql = sprintf("SELECT * FROM test.%s LIMIT %d;", $tableName, $limit);
        }

        return $this->db->runSqlStatement($sql, PDO::FETCH_ASSOC);
    }

    /**
     * drops all tables in test db
     * @throws Exception
     */
    public function cleanup()
    {
        $this->db->connect();

        $testTableNames = $this->db->runSqlStatement("SHOW TABLES FROM test;", PDO::FETCH_COLUMN, 0);

        foreach ($testTableNames as $currentTestTableName) {
            $this->db->runSqlStatement(sprintf("DROP TABLE IF EXISTS test.%s;", $currentTestTableName));
        }
    }
}