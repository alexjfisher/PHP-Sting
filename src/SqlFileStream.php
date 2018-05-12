<?php


namespace PhpSting;


use Iterator;

class SqlFileStream implements Iterator
{
    const STREAM_MODE_READ = 'r';

    const SQL_QUERY_LENGTH_LIMIT = 10000;

    const STREAM_CONNECTION_TIMEOUT = 100 * 60.0;

    const SQL_COMMENT_REGEX = '/(^\s*\/\*.*\*\/)|(^\s*--.*$)/sUm';

    /** @var string **/
    private $currentQuery;

    /** @var string **/
    private $fileLocation;

    /** @var resource **/
    private $activeStreamHandle;

    /** @var resource **/
    private $streamContext;

    /** @var int **/
    private $pointer;

    /**
     * SqlFileStream constructor.
     * @param string $fileLocation
     */
    public function __construct($fileLocation)
    {
        $this->fileLocation = $fileLocation;
        $this->streamContext = stream_context_create([]);
        $this->activeStreamHandle = $this->openStream();
    }

    /**
     * open stream to the file
     * @return bool|resource
     */
    private function openStream()
    {
        return fopen($this->fileLocation, self::STREAM_MODE_READ);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return string
     * @since 5.0.0
     */
    public function current()
    {
        return $this->currentQuery;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $unformattedQuery = $this->loadNextLine();
        $this->currentQuery = $this->formatSqlQuery($unformattedQuery);
        if ($this->valid()) {
            $this->pointer++;
        }
    }

    private function loadNextLine()
    {
        return stream_get_line($this->activeStreamHandle, self::SQL_QUERY_LENGTH_LIMIT, ';');
    }

    /**
     * @param string $unformattedQuery
     * @return string
     */
    public function formatSqlQuery($unformattedQuery)
    {
        $formattedQuery = preg_replace(self::SQL_COMMENT_REGEX, '', $unformattedQuery);
        $trimmedFormattedQuery = trim($formattedQuery);
        if (! empty($trimmedFormattedQuery)) {
            $trimmedFormattedQuery .= ';';
        }
        return $trimmedFormattedQuery;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return ! empty($this->currentQuery);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        if (gettype($this->activeStreamHandle) === 'resource') {
            rewind($this->activeStreamHandle);
        }
        $this->next();
        $this->pointer = 1;
    }
}