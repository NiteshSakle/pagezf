<?php

namespace Album;

use Album\Controller\AlbumController;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
//    'controllers' => [
//        'factories' => [
//            AlbumController::class => InvokableFactory::class,
//        ],
//    ],
    
    'router' => [
        'routes' => [
            'album' => [
                'type' => Segment::class,
                'options' => [
                   'route'    => '/album[/:action][/:id][/page/:page][/order_by/:order_by][/:order][(?:search_by)]',
                     'constraints' =>[
                         'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'action' => '(?!\bpage\b)(?!\border_by\b)(?!\bsearch_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                         'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                         'search_by' => '[a-zA-z][a-zA-z0-9_-]*'
                        ],
                    'defaults' => [
                        'controller' => AlbumController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    
    'view_manager' => [
        'template_path_stack' => [
            'album' => __DIR__ . '/../view',
        ],
    ],
];
