<?php
/**
 * Class: UserController
 *
 * @package  Controller
 * @author   Arslan Ali <shayansolutions@gmail.com>
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Form\UserForm;

class UserController extends AbstractActionController
{
    protected $em;
    protected $redirection;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->init();
        return parent::onDispatch($e);
    }
    /**
     * init function
     * 
     */
    public function init()
    {
        $roleName=$this->getRequest()->getHeaders()->get('Cookie')->role;
        if($roleName == 'data_entry'){
            $disallwedController = array('admin\controller\user','admin\controller\group');
            $disallowedAction = array('block','unblock','delete');
            $currentAction =$this->params('action');
            $currentController =$this->params('action');
            if(in_array($currentController,$disallwedController) || in_array($currentAction, $disallowedAction))
            {
                $this->redirection=true;
               
            }
        }
    }
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
     * list user
     * @return ViewModel
     */

    public function listAction()
    {
        $listUser = $this->getEntityManager()->getRepository('Application\Entity\User')->findAll();
        return new ViewModel(array('users'=>$listUser));
    }
    /**
     * create user
     * @return ViewModel
     */
    
    public function createAction()
    {
        $entityManager = $this->getEntityManager();
        $form = new UserForm($entityManager);
        $view=new ViewModel();
        $view->setVariable('userForm', $form);
        return $view;
    }
    /**
     * edit user
     * @return ViewModel
     */
    
    public function editAction()
    {
        return new ViewModel();
    }
    /**
     * block user
     */
    public function blockUserAction()
    {
        if($this->redirection){
            return $this->redirect()->toRoute('admin-dashboard');
        }else{
            $id = $this->params()->fromRoute('id');
            $user = $this->getEntityManager()->find('\Application\Entity\User', $id);
            $user->setState(0);
            $this->em->persist($user);
            $this->em->flush();
            $this->addMessage('success','User blocked successfully');
            return $this->redirect()->toRoute('route-name', array('controller' => 'user', 'action' => 'list'));
        }
    }
    /**
     * unblock user
     */
    public function unblockUserAction(){
        if($this->redirection){
            return $this->redirect()->toRoute('admin-dashboard');
        }else{
            $id = $this->params()->fromRoute('id');
            $user = $this->getEntityManager()->find('\Application\Entity\User', $id);
            $user->setState(1);
            $this->em->persist($user);
            $this->em->flush();
            $this->addMessage('success','User upblocked successfully');
            return $this->redirect()->toRoute('route-name', array('controller' => 'user', 'action' => 'list'));
        }
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