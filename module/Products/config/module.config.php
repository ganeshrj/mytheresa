<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonImporter for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Products;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/Products',
                    'defaults' => [
                        'controller' => Controller\ProductsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'Products' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/Products/[:action]',
                    'defaults' => [
                        'controller' => Controller\ProductsController::class,
                       
                    ],
                ],
            ],

        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\ProductsController::class => function ($container) {
                  return new Controller\ProductsController(
                   $container

                  );
              }


        ],

    ],
    
    'view_manager' => [
        'strategies' => array(
           'ViewJsonStrategy',
        ),
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
