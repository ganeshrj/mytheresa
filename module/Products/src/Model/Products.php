<?php

namespace Products\Model;

class Products
{
    protected $id;
    protected $sku;
    protected $name;
    protected $category;
    protected $price;


    public function __construct($config)
    {
        $this->config = $config;
    }


    public function loadData()
    {
        $jsonFileName = sprintf("%s%s%s%s%s", $this->config['webroot'], DIRECTORY_SEPARATOR,$this->config['json_folder'],DIRECTORY_SEPARATOR,$this->config['json']);
        $json = file_get_contents($jsonFileName);

        return json_decode($json, 1);
    }

}


