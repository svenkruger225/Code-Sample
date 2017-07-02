<?php
/**
 * Class: IndexController
 *
 * @package  Controller
 * @author   Arslan Ali <shayansolutions@gmail.com>
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Entity;
use Zend\Session\Container;
use Admin\Form\LoginForm;
use Admin\Form\Filter\LoginFilter;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Header\SetCookie;
use Admin\Form\profileForm;
use Admin\Form\Filter\profileFilter;
use Application\Entity\User;



class IndexController extends AbstractActionController
{
    protected $em;
    /**
     * get doctrine entity manager
     * @return type
     */

    public function getEntityManager()
    {
         if (null === $this->em)
        {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }
    
    /**
     * login user
     * @return ViewModel
     */
    public function loginAction()
    {
        $auth = new AuthenticationService;
       
        if ($auth->hasIdentity()){
            return $this->redirect()->toRoute('admin-dashboard', array('action' => 'dashboard'));
        }
        $request = $this->getRequest();

        $view = new ViewModel();
        $view->setTerminal(true);
        $loginForm = new LoginForm('loginForm');
        $loginForm->setInputFilter(new LoginFilter());
        
        if ($request->isPost())
        {
            $data = $request->getPost();
            $loginForm->setData($data);

            if ($loginForm->isValid())
            {
                $sm = $this->getServiceLocator();
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                $emailId=$loginForm->getInputFilter()->getValue('email');
                $password=$loginForm->getInputFilter()->getValue('password');
                $user = $this->getEntityManager()->getRepository('Application\Entity\User')->authenticate($adapter,$auth,$emailId,$password);
               if($user){
                    $auth->getStorage()->write($user);
                    if(!null==($user->getUserDetail())){
                        $userGroup=$user->getUserDetail()->getGroup()->getName();
                    }else{
                        $userGroup='';
                    }
                    /*Group value set in cookie*/
                    $cookie = new SetCookie('role',$userGroup); 
                    $response = $this->getResponse()->getHeaders();
                    $response->addHeader($cookie);
                    if(!in_array($userGroup,array('admin','data_entry')))
                    {
                        $this->addSuccessMessage('Login Successfully');
                        return $this->redirect()->toRoute('admin-dashboard', array('action' => 'dashboard'));
                    }
                    else
                    {
                        return $this->redirect()->toRoute('admin-login');
                    }
                    
                }
                else{
                    $this->addMessage('error','Authentication Failed');
                    $loginForm->get('password')->setMessages(array('Invalid email or password'));
                }
            }
            else
            {
                $errors = $loginForm->getMessages();
            }
        }
        $view->setVariable('loginForm', $loginForm);
        return $view;
    }

    /**
     * 
     * makes user logout
     */
    public function logoutAction()
    {
        $auth = new AuthenticationService;
        if ($auth->hasIdentity())
        {
            $auth->clearIdentity();
            //destroy all cookies
            if (isset($_SERVER['HTTP_COOKIE']))
            {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach ($cookies as $cookie)
                {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time() - 1000);
                    setcookie($name, '', time() - 1000, '/');
                }
            }
            return $this->redirect()->toRoute('admin-login', array('action' => 'login'));
        }
    }
    /**
     * dashboard page
     * @return ViewModel
     */

    public function dashboardAction()
    {
        return new ViewModel();
    }
    /**
     * profile page
     * @return ViewModel
     */
    public function profileAction()
    {
        $form = new profileForm();
        $form->setInputFilter(new profileFilter());
        $request=$this->getRequest();
        if ($request->isPost())
        {
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid())
            {
                 $user = $this->getEntityManager()->find('\Application\Entity\User', 1);
                 $user->setPassword(md5($this->getRequest()->getPost('password')));
                 $this->getEntityManager()->persist($user);
                 $this->getEntityManager()->flush();
                 $this->addMessage('success','Password updated successfully');
                 return $this->redirect()->toRoute('admin-dashboard', array('controller'=>'Index','action' => 'dashboard'));
                    
            }
        }
       
        $view=new ViewModel();
        $view->setVariable('profileForm', $form);
        return $view;
    }
    /**
     * flash success message
     */
    
    public function addSuccessMessage($message) 
    { 
        $this->addMessage('success',$message); 
    }
    /**
     * flash error message
     */
    
    public function addErrorMessage($message) 
    { 
        $this->addMessage('error',$message); 
    }
    /**
     * general flash success message
     */
    protected function addMessage($type,$message) 
    { 
        $this->flashMessenger()->setNamespace($type)->addMessage(array($type => $message));
    }
}
