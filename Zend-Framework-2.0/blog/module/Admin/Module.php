<?php
namespace Admin;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\Http\Client as HttpClient;
use Network\HttpRestJson\Client as HttpRestJsonClient;
use Zend\View\Model\ViewModel;


class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'checkLoggedIn'));
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'setLayout'));
        
        
    }
    public function checkLoggedIn(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $auth = new AuthenticationService;
        /**
         * redirect to login page if
         * 1. user is not logged in
         * 2. user is trying to access any other page then login page
         * 3. user is trying to access a page which has admin in URL
         */
        if (!$auth->hasIdentity() && 
                $routeMatch->getMatchedRouteName() != 'admin-login' && 
                strpos($e->getRequest()->getRequestUri(), 'admin') !== false
            ) 
        {
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine(
            'Location',
            $e->getRouter()->assemble(
                    array(),
                    array('name' => 'admin-login')
                )
            );
            $response->setStatusCode(302);
            return $response;
        }else{
            $this->checkUserRole($e);
        }
         
    }
    public function checkUserRole($e){
        $routeMatch = $e->getRouteMatch();
        $auth = new AuthenticationService;
        if($auth->hasIdentity()&& $routeMatch->getMatchedRouteName() != 'admin-login' && 
                strpos($e->getRequest()->getRequestUri(), 'admin') !== false){
            if(isset($e->getApplication()->getRequest()->getHeaders()->get('Cookie')->role))
            {
                $roleName = $e->getApplication()->getRequest()->getHeaders()->get('Cookie')->role;
                $response = $e->getResponse();
                if(!in_array($roleName,array('admin','data_entry'))){
                    
                    $response->getHeaders()->addHeaderLine(
                    'Location',
                    $e->getRouter()->assemble(
                            array(),
                            array('name'=>'home')
                        )
                    );
                    $response->setStatusCode(302);
                    return $response;
                }
                elseif($roleName == 'data_entry')
                {
                    $disallwedController = array('admin\controller\user','admin\controller\group');
                    $disallowedAction = array('block','unblock','delete');
                    $currentController = strtolower($routeMatch->getParam('controller'));
                    $currentAction = strtolower($routeMatch->getParam('action'));
           
                    if(in_array($currentController,$disallwedController) || in_array($currentAction, $disallowedAction))
                    {
                        $response->getHeaders()->addHeaderLine(
                            'Location',
                            $e->getRouter()->assemble(
                                    array(),
                                    array('name'=>'admin-dashboard')
                                )
                            );
                            $response->setStatusCode(302);
                            return $response;
                    }
                }      
            }
        }
    }
    public function setLayout($e)
    {
        $auth = new AuthenticationService;
        $viewModel = $e->getViewModel();
        
        $pageCaption = $viewModel->getVariable('pageCaption') ? $viewModel->getVariable('pageCaption') : array();
        $menuItems = $this->getMenu($e,'admin',$pageCaption);
        
        $factory = new ConstructedNavigationFactory($menuItems);
        $navigation = $factory->createService($e->getApplication()->getServiceManager());
       
        if($auth->hasIdentity())
        {
            $username=$auth->getIdentity()->getDisplayName();
            $viewModel->setVariables(array(
                'userName' => $username,
                'navigation' => $navigation,
            ));
        }
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    public function init(\Zend\ModuleManager\ModuleManager $manager)
    {
        $manager->getEventManager()->getSharedManager()->attach(__NAMESPACE__, 'dispatch', function($e){
            $e->getTarget()->layout('admin/layout');
        });
    }
     public function getMenu($e, $type = 'admin', $replaceArray = array())
    {
        $menu['main'] = array();
        $menu['admin'] = array(
            'dashboard' => array(
                'label'     => 'Dashboard',
                'route'     => 'admin-dashboard',
                'module'    => 'admin',
                'controller'=> 'dashboard',
                'action'    => 'index',
                'icon'      => 'fa fa-home',
            ),
            'UserManagement' => array(
                'label' => 'User Management',
                'controller' => 'User',
                'action' => 'list',
                'route' => 'route-name',
                'icon' => 'fa fa-user',
                'pages' => array(
                    'UserManagement' => array(
                        'label' =>'User Management',
                        'controller' => 'User',
                        'action' => 'list',
                        'route' => 'route-name',
                        
                    ),
                    'GroupManagement' => array(
                        'label' => 'Group Management',
                        'controller' => 'group',
                        'action' => 'list',
                        'route' => 'route-name',
                        'pages'     => array(
                            'edit'      => array(
                                'label'     => 'Edit group',
                                'route'     => 'route-name',
                                'controller' => 'group',
                                'action'    => 'edit',
                            ),
                            'add'      => array(
                                'label'     => 'Create group',
                                'route'     => 'route-name',
                                'controller' => 'group',
                                'action'    => 'create',
                            ),
                        ),
                    ),
                )
            ),
            'BlogManagement' => array(
                'label' => 'Blog Management',
                'controller' => 'blog',
                'action' => 'list',
                'route' => 'route-name',
                'icon' => 'fa fa-fire',
                'pages' => array(
                    'BlogManagement' => array(
                        'label' => 'Blog Management',
                        'controller' => 'blog',
                        'action' => 'list',
                        'route' => 'route-name',
                        'pages'     => array(
                            'edit'      => array(
                                'label'     => 'Edit Banner',
                                'route'     => 'route-name',
                                'controller' => 'blog',
                                'action'    => 'edit',
                            ),
                            'add'    => array(
                                'label'     => 'Add Banner',
                                'route'     => 'route-name',
                                'controller' => 'blog',
                                'action'    => 'create',
                            ),
                        ),
                    ),
                    'CategoryManagement' => array(
                        'label' => 'Category Management',
                        'controller' => 'category',
                        'action' => 'list',
                        'route' => 'route-name',
                        'pages'     => array(
                            'edit'      => array(
                                'label'     => 'Edit Category',
                                'route'     => 'route-name',
                                'controller' => 'category',
                                'action'    => 'edit',
                            ),
                            'add'    => array(
                                'label'     => 'Add Category',
                                'route'     => 'route-name',
                                'controller' => 'category',
                                'action'    => 'create',
                            ),
                        ),
                    ),
                )
            ),
           
            'Logout' => array(
                'label'     => 'Logout',
                'route'     => 'admin-logout',
                'action'    => 'logout',
                'icon'      => 'fa fa-key',
            ),
        );
        $menu = array_replace_recursive($menu, $replaceArray);
        return $menu[$type];
    }
}