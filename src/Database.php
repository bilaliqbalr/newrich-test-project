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

    public function connect() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->db, $this->port);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function disconnect() {
        $this->connection->close();
    }

    public function select($table, $columns = "*", $where = null, $order = null, $limit = null) {
        $sql = "SELECT $columns FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        if ($order) {
            $sql .= " ORDER BY $order";
        }
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        $result = $this->connection->query($sql);

        if ($result->num_rows > 0) {
            // Fetch data as associative or object arrays
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            return [];
        }
    }

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            return $this->connection->insert_id; // Return the inserted ID
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
        $stmt->bind_param(...$bind_params); // Add where clause parameters as well
        $stmt->execute();

        return $stmt->affected_rows; // Return number of affected rows
    }

    public function delete($table, $where) {
        $sql = "DELETE FROM $table WHERE $where";
        $result = $this->connection->query($sql);
        return $this->connection->affected_rows; // Return number of affected rows
    }

    // ... Add methods for other database operations (e.g., prepared statements)
}

