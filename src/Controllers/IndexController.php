<?php

namespace App\Controllers;

use App\Controller;
use App\Database;

class IndexController extends Controller
{

    public function index()
    {
        $stmt = Database::pdo()
            ->prepare('SELECT cat_id, cat_name, cat_description FROM categories');
        $stmt->execute();

        $this->set('categories', $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function topic()
    {

    }

}
