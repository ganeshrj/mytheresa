<?php
namespace Products\Service;

use Products\Model\ProductsMapper;

class ProductsService
{
    public function __construct($config)
    {
        $this->config = $config;
        $this->mapper = new ProductsMapper($this->config);
    }

    public function getProducts($filter)
    {
        return $this->mapper->getProducts($filter);
    }
}

