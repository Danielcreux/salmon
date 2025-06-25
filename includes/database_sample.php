<?php
// includes/database.php

class Database {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO('sqlite:database.db');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTables();
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    private function createTables() {
        $queries = [
            "CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                rol TEXT NOT NULL,
                nombre TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS asistencias (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER NOT NULL,
                fecha DATE NOT NULL,
                hora_entrada TIME,
                hora_salida TIME,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
            )"
        ];

        foreach ($queries as $query) {
            $this->pdo->exec($query);
        }

        // Crear usuario admin si no existe
        $this->createAdminUser();
    }

    private function createAdminUser() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = 'admin123'");
        $stmt->execute();
        
        if ($stmt->fetchColumn() == 0) {
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare(
                "INSERT INTO usuarios (username, password, rol, nombre) 
                VALUES ('admin123', ?, 'admin', 'Admin')"
            );
            $stmt->execute([$hashedPassword]);
        }
    }

    public function getPDO() {
        return $this->pdo;
    }
}

$db = new Database();
$pdo = $db->getPDO();
?>