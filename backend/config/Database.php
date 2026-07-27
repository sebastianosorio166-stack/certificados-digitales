<?php

class Database
{
    private $host = "localhost";
    private $database = "certificados_db";
    private $username = "root";
    private $password = "";
    private $port = "3306";

    public function conectar()
    {
        try {

            $conexion = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8",
                $this->username,
                $this->password
            );

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conexion;

        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());

        }
    }
}