<?php

namespace Madlib;

use Madlib\Config;
use mysqli;

class Mysql
{
    protected Config $config;

    protected $mysqli = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getMysqli(): mysqli
    {
        if (null === $this->mysqli) {
            $this->mysqli = new mysqli(
                $this->config::MYSQL['host'],
                $this->config::MYSQL['user'],
                $this->config::MYSQL['password'],
                $this->config::MYSQL['database']
            );
            if ($this->mysqli->connect_errno) {
                throw new MysqlException("Failed to connect to MySQL: " . $this->mysqli->connect_error);
            }
        }
        return $this->mysqli;
    }

    public function esc(string $value): string
    {
        return $this->getMysqli()->real_escape_string($value);
    }

    /**
     * @return mysqli_result|true
     * @throws MysqlException
     */
    public function query(string $query)
    {
        $result = $this->getMysqli()->query($query);
        if (!$result) {
            $mysqli = $this->getMysqli();
            throw new MysqlException('Query error: "' . $query . '"' . $mysqli->error, $mysqli->errno);
        }
        return $result;
    }

    public function select(string $query): array
    {
        $result = $this->query($query);
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function selectRow(string $query): array
    {
        $result = $this->query($query);
        return (array)$result->fetch_assoc();
    }

    public function affect(string $query): int
    {
        $this->query($query);
        $affectedRows = $this->getMysqli()->affected_rows;
        if ($affectedRows < 0) {
            throw new MysqlException('Affect error');
        }
        return $affectedRows;
    }
    
    public function insert(string $query): int
    {
        return $this->affect($query);
    }

    public function update(string $query): int
    {
        return $this->affect($query);
    }

    public function delete(string $query): int
    {
        return $this->affect($query);
    }
}
