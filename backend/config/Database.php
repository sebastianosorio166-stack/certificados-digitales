<?php

declare(strict_types=1);

class Database
{
    public function conectar(): PDO
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $database = getenv('DB_NAME') ?: 'certificados_db';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
        // XAMPP local utiliza el puerto 3307 en este proyecto.
        // En otro entorno se puede sobrescribir con la variable DB_PORT.
        $port = getenv('DB_PORT') ?: '3307';

        return new PDO(
            "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
