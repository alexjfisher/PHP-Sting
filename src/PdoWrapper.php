<?php


namespace PhpSting;


use ErrorException;
use Exception;
use PDO;

class PdoWrapper
{
    /** @var string **/
    private $username;

    /** @var string **/
    private $password;

    /** @var string **/
    private $dbname;

    /** @var string **/
    private $host;

    /** @var PDO **/
    private $connection;

    /**
     * PdoWrapper constructor.
     * @param string $host
     * @param string $dbname
     * @param string $username
     * @param string $password
     */
    public function __construct($host, $dbname, $username, $password)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @throws Exception
     */
    public function connect()
    {
        set_error_handler([$this, 'errorHandler'], E_ALL);

        try {
            if (! $this->isConnected()) {
                $this->connection = new PDO(
                    sprintf("mysql:dbname=%s;host=%s", $this->dbname, $this->host),
                    $this->username,
                    $this->password
                );
            }
        }
        catch (Exception $exception) {
            throw new Exception("Connection failed.", 0, $exception);
        }
        finally {
            restore_error_handler();
        }
    }

    /**
     * @param int $errorNumber
     * @param string $errorString
     * @param string $errorFile
     * @param int $errorLine
     * @throws ErrorException
     */
    public function errorHandler($errorNumber, $errorString, $errorFile, $errorLine)
    {
        throw new ErrorException($errorString, 0, $errorNumber, $errorFile, $errorLine);
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return ($this->connection instanceof PDO);
    }

    /**
     * @param SqlFileStream $sqlFixture
     * @throws Exception
     */
    public function applyFixture(SqlFileStream $sqlFixture)
    {
        $this->connection->beginTransaction();
        try{
            foreach ($sqlFixture as $currentQuery) {
                $this->connection->exec($currentQuery);
            }
            $this->connection->commit();
        }
        catch (Exception $exception) {
            $this->connection->rollBack();
            throw new Exception("Applying Db fixture failed.", 0, $exception);
        }
    }

    /**
     * @param string $sqlStatement
     * @param int $fetchStyle - optional PDO:FETCH_COLUMN | PDO:FETCH_ASSOC etc check PDOStatement::fetchAll for details
     * @param int $fetchArgument
     * @return array
     * @throws Exception
     */
    public function runSqlStatement($sqlStatement, $fetchStyle = null, $fetchArgument = null)
    {
        set_error_handler([$this, 'errorHandler'], E_ALL);

        try {
            $statement = $this->connection->prepare($sqlStatement);
            $statement->execute();
            if (! is_null($fetchStyle) && ! is_null($fetchArgument)) {
                return $statement->fetchAll($fetchStyle, $fetchArgument);
            }
            elseif (! is_null($fetchStyle)) {
                return $statement->fetchAll($fetchStyle);
            }
        }
        catch (Exception $exception) {
            throw new Exception("Statement failed.", 0, $exception);
        }
        finally {
            restore_error_handler();
        }
    }
}