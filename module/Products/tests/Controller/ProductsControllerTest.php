<?php

namespace ProductsTest\Controller;

use Products\Model\Products;
use Products\Model\ProductsMapper;
use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use Zend\Stdlib\ArrayUtils;
use Products\Controller\ProductsController;

class ProductsControllerTest extends AbstractControllerTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->setApplicationConfig(
            ArrayUtils::merge(
            // Grabbing the full application configuration:
                include __DIR__ . '/../../../../config/application.config.php',
                []
            )
        );
        $this->config = $this->getApplicationServiceLocator()->get('config');
        $this->mapper = new ProductsMapper($this->config);
    }


    public function testFilePath()
    {

        $jsonFileName = sprintf("%s%s%s%s%s", $this->config['webroot'], DIRECTORY_SEPARATOR,$this->config['json_folder'],DIRECTORY_SEPARATOR,$this->config['json']);
        $this->assertFileExists($jsonFileName);
    }

    public function testFileContent()
    {
        $productsModel = new Products($this->config);
        $products = $productsModel->loadData();
        $this->assertJson(json_encode($products));
    }

    public function testMaxFive()
    {
        $productsResult = $this->mapper->getProducts([]);
        $this->assertCount(5, $productsResult);
    }

    public function testJsonStructure()
    {
        $productsResult = $this->mapper->getProducts([]);


        $this->assertArrayHasKey('sku', $productsResult[0]);
        $this->assertArrayHasKey('name', $productsResult[0]);
        $this->assertArrayHasKey('category', $productsResult[0]);
        $this->assertArrayHasKey('price', $productsResult[0]);
        $this->assertArrayHasKey('original', $productsResult[0]['price']);
        $this->assertArrayHasKey('final', $productsResult[0]['price']);
        $this->assertArrayHasKey('currency', $productsResult[0]['price']);
        $this->assertArrayHasKey('discount_percentage', $productsResult[0]['price']);
    }

    public function testDiscount()
    {
        $productsResult = $this->mapper->getProducts(["category" => "boots"]);
        $this->assertContains("30%", $productsResult[0]['price']['discount_percentage']);
    }

    public function testOriginalAndFinal()
    {
        $productsResult = $this->mapper->getProducts(["category" => "sneakers"]);

        $this->assertEquals(null, $productsResult[0]['price']['discount_percentage']);
        $this->assertEquals($productsResult[0]['price']['original'], $productsResult[0]['price']['final']);
    }


}