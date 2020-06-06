<?php

require_once '../Parser.php';

class Controller
{

    private $dbPath = '/var/www/html/home/db/';
    private $olxFile = 'olx';
    private $realtyFile = 'realty';

    public function __construct()
    {
        $this->dbPath = dirname(__FILE__) . '/..' . '/db/';

        $this->olxFile = $this->dbPath . $this->olxFile;
        $this->realtyFile = $this->dbPath . $this->realtyFile;
    }

    public function save($file, $content) {
        switch ($file) {
            case 'olx':
                $file = $this->olxFile;
                break;
            case 'realty':
                $file = $this->realtyFile;
                break;

            default:
                return false;
        }

        file_put_contents($file,$content);

        return true;
    }

    public function update() {
        $parser = new Parser();

        $parser->find();

        $info = [
            'olx' => $parser->getOlxInfo(),
            'realty' => $parser->getRealtyInfo()
        ];

        echo json_encode($info);
    }
}