<?php

    class Database{

        private $host = "localhost";
        private $username = "root";
        private $password = "";
        private $dbName = "";
        private $connection;

        //Constructor for the class that initializes database connection parameters 
        //and establishes a connection to the MySQL database upon object creation.
        public function __construct($host, $username, $password, $dbName) {
            $this->host = $host;
            $this->username = $username;
            $this->password = $password;
            $this->dbName = $dbName;
            $this->connect();
        }

        //Connect to the MySQL database using PDO and return the connection object
        private function connect(){
            $dsn= 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
            try{
                $this->connection= new PDO($dsn, $this->username, $this->password);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $this->connection;
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        //Close the connection to the MySQL database by setting the connection object to null.
        public function disconnect(){
            $this->connection = null;
        }

        //Execute a SELECT query on the specified table and return the results as an associative array
        public function select($table, $columns = '*', $where = null, $params = []){
            try{
                $sql= "SELECT " . $columns . " FROM " . $table;
                if ($where){
                    $sql.=" WHERE " . $where;  
                }
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Select query failed: " . $e->getMessage());
            }
            
        }

        //Insert a new record into the specified table using the provided data array
        //and return the ID of the last inserted record.
        public function insert($table, $data){
            if (!$this->connection){
                $this->connect();
            }
            try{
                $columns = implode(",", array_keys($data)); 
                $values =":" .  implode(", :", array_keys($data));
                $sql = "INSERT INTO " . $table . "(" . $columns . ") VALUES (" . $values . ")";
    
                $stmt= $this->connection->prepare($sql);
                $stmt->execute($data);
    
                return $this->connection->lastInsertId();   
            } catch (PDOException $e) {
                throw new Exception("Insert query failed: " . $e->getMessage());
            }


        }
        
        //Delete records from the specified table based on the given WHERE condition 
        //and return the number of affected rows.
        public function delete($table, $where, $params =[]){
            try{
                $sql= "DELETE FROM " . $table . " WHERE " . $where;  
            
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($params);

                return $stmt->rowCount();
            } catch (PDOException $e) {
                throw new Exception("Delete query failed: " . $e->getMessage());
            }
            
        }

        //Update existing records in the specified table based on the provided data and WHERE condition,
        //and return the number of affected rows.
        public function update($table, $data, $where){
            try{
                $set = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
                $sql= "UPDATE " . $table . " SET " . $set . " WHERE " . $where;
    
                $stmt= $this->connection->prepare($sql);
                $stmt->execute($data);
                
                return $stmt->rowCount();
            } catch (PDOException $e) {
                throw new Exception("Update query failed: " . $e->getMessage());
            }

        }


        //Create a new table in the database with the specified columns if it does not already exist.
        public function createTable($table, $columns) {
            $columnsSql = implode(", ", $columns);
            $sql = "CREATE TABLE IF NOT EXISTS `$table` ($columnsSql)";

            try {
                $stmt= $this->connection->prepare($sql);
                $stmt->execute();
            } catch (PDOException $e) {
                throw new Exception("Create table failed: " . $e->getMessage());
            }
        }

        //Check if a record exists in the specified table based on the given WHERE condition.
        public function recordExists($table, $where, $params = []) {
            try {
                $sql = "SELECT COUNT(*) FROM " . $table . " WHERE " . $where;
                
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($params);
                
                $count = $stmt->fetchColumn();
                return $count > 0;

            } catch (PDOException $e) {
                throw new Exception("Record existence check failed: " . $e->getMessage());
            }
        }

        public function getConnection(){
            return $this->connection;
        }
        

    }