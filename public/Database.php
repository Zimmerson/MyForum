<?php

class Database
{

    /** @var self An instance if this class. */
    static $instance;

    /** @var PDO The database connection. */
    private $pdo;

    /**
     * Database constructor.
     *
     * @param string $dbHost Database host.
     * @param string $dbName Database name.
     * @param string $dbUser Database user.
     * @param string $dbPass Password for the database user.
     */
    private function __construct($dbHost, $dbName, $dbUser, $dbPass)
    {
        $this->pdo = new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {

            // Load the credentials for use.
            $dbHost = getenv('DB_HOST');
            $dbName = getenv('DB_NAME');
            $dbUser = getenv('DB_USER');
            $dbPass = getenv('DB_PASS');

            self::$instance = new self($dbHost, $dbName, $dbUser, $dbPass);
        }

        return self::$instance;
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    // TODO: Rewrite the below for this project.

//    public function getforum()
//    {
//        $stmt = $this->pdo->prepare('SELECT * FROM todos WHERE deleted_at IS NULL');
//        $stmt->execute();
//        $todos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//
//        return $todos;
//    }
//
//    public function addTodo($summary, $isImportant)
//    {
//        $stmt = $this->pdo->prepare("
//            INSERT INTO forum (`summary`, `is_important`)
//            VALUES (:summary, :isImportant)
//        ");
//
//        $stmt->execute([
//            'summary' => $summary,
//            'isImportant' => $isImportant ? 1 : 0
//        ]);
//
//    }
//
//    public function updateTodo($id, $fieldsAndValues)
//    {
//        $sets = [];
//        foreach ($fieldsAndValues as $field => $value) {
//            $sets[] = "$field = '$value'";
//        }
//        $sets = implode(', ', $sets);
//
//        $stmt = $this->pdo->prepare("
//            UPDATE todos
//            SET $sets
//            WHERE id = $id AND deleted_at IS NULL
//        ");
//        $stmt->execute();
//    }

}
