<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
//    'service_manager' => array(
//        'factories' => array(
//            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory', // <-- add this
//        ),
//    ),
    
    'router' => array(
        'routes'=>array(
            'admin-user' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action][/:id]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]*'
                            ),
                            'defaults' => array(
                                'id'=>'0'
                            ),
                        ),
                    ),
                ),
            ),
            'route-name' => array(
                    'type' => 'segment',
                    'options' => array(
                        'route'    => '/admin[/:controller[/:action][/:id]][/]',
                        'defaults' => array(
                            '__NAMESPACE__' => 'Admin\Controller',
                            'controller'    => 'Index',
                            'action'        => 'index',
                            'id'         => '[0-9]*'
                        ),  
                    ),
                ),
            
            'admin-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/login',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'login',
                    ),
                ),
            ),
            
            'admin-dashboard' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/dashboard',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'dashboard',
                    ),
                ),
            ),
            'admin-logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/logout',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'logout',
                    ),
                ),
            ),
            'admin-profile' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/profile/',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'profile',
                    ),
                ),
            ),
            'send-mail' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/mail/send',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\SideBanner',
                        'action'     => 'sendMail',
                    ),
                ),
            ),
            
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
    ),
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Index' => 'Admin\Controller\IndexController',
            'Admin\Controller\User' => 'Admin\Controller\UserController',
            'Admin\Controller\Group' => 'Admin\Controller\GroupController',
            'Admin\Controller\Country' => 'Admin\Controller\CountryController',
            'Admin\Controller\City' => 'Admin\Controller\CityController',
            'Admin\Controller\Category' => 'Admin\Controller\CategoryController',
            'Admin\Controller\Blog' => 'Admin\Controller\BlogController'
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'partialMenu'                => __DIR__ . '/../view/blocks/partialMenu.phtml',
            'partialBreadcrumbs'                => __DIR__ . '/../view/blocks/partialBreadcrumbs.phtml',
            'admin/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
