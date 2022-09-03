<?php

namespace Classes;

use Classes\MyDB;

class Controller 
{
    private $objDB;

    public function __construct()
    {
        $this->objDB = new MyDB;
    }

    public function render()
    {
        $commentHTML = $this->objDB->showComments();

        $statusComm = $this->objDB->storeComments();

        $statusRate = $this->objDB->storeRate();

        $getStat = $this->objDB->getStat();

        echo $this->getContent(compact('commentHTML', 'statusComm', 'statusRate', 'getStat'));
    }

    public function getContent($vars = [])
    {
        ob_start();
        extract($vars);

        $url = $_SERVER['REQUEST_URI'];
        if (preg_match('#/([a-z0-9_-]+)#', $url, $params) || $url === '/') {
            if($url === '/') $url = 'index.php';
            trim($url, '/');
            if (!require_once 'templates/' . $url) {
                throw new \Exception('Templat not found ' . $_SERVER['REQUEST_URI']);
            }
        } else {
            throw new \Exception('Path not found ' . $_SERVER['REQUEST_URI']);
        }
        
        return ob_get_clean();

    }


}