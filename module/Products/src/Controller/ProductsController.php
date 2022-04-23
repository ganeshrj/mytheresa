<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonImporter for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Products\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Products\Service\ProductsService;

class ProductsController extends AbstractActionController
{
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $config = $this->serviceLocator->get("config");

        $this->productService = new ProductsService($config);
    }

    public function indexAction()
    {
        try {
            $filter = [];
            if (!empty($this->params()->fromQuery('category'))) {
                $filter['category'] = $this->params()->fromQuery('category');
            }

            if (!empty($this->params()->fromQuery('priceLessThan'))) {
                if (is_numeric($this->params()->fromQuery('priceLessThan'))) {
                    $filter['priceLessThan'] = $this->params()->fromQuery('priceLessThan');
                } else {
                    throw new \Exception("Invalid value filter value");
                }
            }
            $data = $this->productService->getProducts($filter);
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }

        return new JsonModel($data);
    }

    var $serviceLocator;


}
