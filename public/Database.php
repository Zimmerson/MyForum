<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 19/12/2016
 * Time: 20:58
 */




class Database
{
    static $instance;
    private $pdo;

    private function __construct()
    {
        $this->pdo = new \PDO('mysql:host=localhost;dbname=forum', 'homestead', 'secret');
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getforum()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM todos WHERE deleted_at IS NULL');
        $stmt->execute();
        $todos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $todos;
    }

    public function addTodo($summary, $isImportant)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO forum (`summary`, `is_important`)
            VALUES (:summary, :isImportant)
        ");

        $stmt->execute([
            'summary' => $summary,
            'isImportant' => $isImportant ? 1 : 0
        ]);
        
    }

    public function updateTodo($id, $fieldsAndValues)
    {
        $sets = [];
        foreach ($fieldsAndValues as $field => $value) {
            $sets[] = "$field = '$value'";
        }
        $sets = implode(', ', $sets);
        
        $stmt = $this->pdo->prepare("
            UPDATE todos
            SET $sets
            WHERE id = $id AND deleted_at IS NULL
        ");
        $stmt->execute();
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}