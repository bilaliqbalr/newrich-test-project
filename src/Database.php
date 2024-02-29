<?php

class Database {

    private $host;
    private $user;
    private $password;
    private $db;
    private $port;
    private $connection;

    public function __construct($host, $user, $password, $db, $port) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;
        $this->port = $port;

        $this->connect();
    }

    public function __destruct() {
        $this->disconnect();
    }

    public function connect() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->db, $this->port);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function disconnect() {
        $this->connection->close();
    }

    public function select($table, $columns = "*", $where = [], $order = null, $limit = null) {
        $sql = "SELECT $columns FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", array_map(function($key, $value) {
                return "$key=?";
            }, array_keys($where), $where));
        }
        if ($order) {
            $sql .= " ORDER BY $order";
        }
        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        $stmt = $this->connection->prepare($sql);
        if (!empty($where)) {
            $stmt->bind_param(str_repeat('s', count($where)), ...array_values($where));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            return $this->connection->insert_id;
        } else {
            return false;
        }
    }

    public function update($table, $data, $where) {
        $updates = [];
        foreach ($data as $key => $value) {
            $updates[] = "$key=?";
        }
        $updates = implode(", ", $updates);

        $sql = "UPDATE $table SET $updates WHERE $where";
        $stmt = $this->connection->prepare($sql);
        $bind_params = array_merge([str_repeat('s', count($data))], array_values($data));
        $stmt->bind_param(...$bind_params);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public function delete($table, $where) {
        $sql = "DELETE FROM $table WHERE $where";
        $result = $this->connection->query($sql);
        return $this->connection->affected_rows;
    }

}

