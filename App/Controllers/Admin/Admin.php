<?php

namespace App\Controllers\Admin;

use App\Helpers\Request;
use App\Models\Category;
use \Core\View;
use \Core\SessionHandler;
use \App\Services\CategoryService;

class Admin extends \Core\Controller
{

    protected  $_categoryService;
    public $table = 'categories';

    public function __construct()
    {

        $this->_categoryService =  new CategoryService;
    }


    public function indexAction()
    {

        SessionHandler::addSession('admin', 'some testing sessionsaaa');

        if (SessionHandler::existSession('admin')) {
            $data = SessionHandler::getSession('admin');
        } else {
            $data = 'session name not defined';
        }
        View::bladeRenderTemplate('admin/index', [
            "data" => $data

        ]);
    }


    public function categoriesAction()
    {
        $total = $this->_categoryService->getAllCategory();
        $object = new Category;

        list($categories,$links) = pagination(5,$total, $this->table,$object);


        View::bladeRenderTemplate('admin/products/categories', compact('categories','links'));
    }

    public function createCategory()
    {
        
       $this->_categoryService->createCategory();
        $total = $this->_categoryService->getAllCategory();

        $object = new Category;
        list($categories,$links) = pagination(5,$total, $this->table,$object);

        View::bladeRenderTemplate('admin/products/categoryTable', compact('categories','links'));
    }
}
