<?php
require_once(__DIR__ . '/../configs/database.php');

class BaseModel {
    protected $connection;

    public function __construct() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
        if ($this->connection->connect_error) {
            die("Database connection failed: " . $this->connection->connect_error);
        }
        $this->connection->set_charset("utf8mb4");
    }

    protected function query($sql) {
        return $this->connection->query($sql);
    }

    protected function select($sql) {
        $result = $this->query($sql);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
        }
        return $rows;
    }

    protected function insert($sql) {
        if ($this->connection->query($sql) === TRUE) {
            return $this->connection->insert_id;
        }
        return false;
    }

    protected function update($sql) {
        return $this->connection->query($sql);
    }

    protected function delete($sql) {
        return $this->connection->query($sql);
    }
}
