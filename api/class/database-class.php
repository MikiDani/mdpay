<?php

    require "../backend/connect.php";

    class Database {
        private $host;
        private $user;
        private $password;
        private $dbName;

        public function __construct() {
            $this->host = HOST;
            $this->user = USER;
            $this->password = PASSWORD;
            $this->dbName = DBNAME;
        }

        protected function connect() {
            $conn = new PDO("mysql:dbname=$this->dbName;host=$this->host", $this->user, $this->password);
            return $conn;
        }
    }

?>