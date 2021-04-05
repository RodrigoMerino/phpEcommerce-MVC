<?php

namespace Core;
use Philo\Blade\Blade;

class View
{
    public static function render($view,$args= []){
        extract($args, EXTR_SKIP);
        $file = "../App/Views/$view";//accede a mi carpeta de views desde aca
        if (is_readable($file)) {
            require $file;
        }
        else{
            echo '$file not found';
        }
    }

    // public static function renderTemplate($template, $args = [])
    // {
    //     static $twig = null;

    //     if ($twig === null) {
    //         $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '../App/Views');
    //         $twig = new \Twig_Environment($loader);
    //     }

    //     echo $twig->render($template, $args);
    // }

    //blade rendering views
    public static function bladeRenderTemplate($template, $args =[])
    {
        $view = dirname( __DIR__ ). '/App/Views';
        //echo $blade->render($template,$args);
        $cache= dirname( __DIR__ ). '/App/Views/cache';

    // $cache = __DIR__ . '/../../bootstrap/cache';
    
    $blade = new Blade($view, $cache);
    echo $blade->view()->make($template, $args)->render();

    }
}
