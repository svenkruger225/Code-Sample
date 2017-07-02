<?php
/**
 * Class: GroupController
 *
 * @package  Controller
 * @author   Arslan Ali <shayansolutions@gmail.com>
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Group;

class GroupController extends AbstractActionController
{
    protected $em;
    protected $form;
    protected $redirection;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->init();
        return parent::onDispatch($e);
    }
    
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
    public function listAction()
    {
        $groupUser = $this->getEntityManager()->getRepository('Application\Entity\Group')->findAll();
        return new ViewModel(array('groupList'=>$groupUser));
    }
    
    public function createAction()
    {
        $group = new Group();
        $form = $group->getForm();
        $form->bind($group);
        $view=new ViewModel();
        $view->setVariable('groupForm', $form);
        return $view;
    }
    
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $group = $this->getEntityManager()->find('\Application\Entity\Group', $id);
        $form = $group->getForm();
        $form->bind($group);
        $view=new ViewModel();
        $view->setVariable('groupForm', $form);
        return $view;
    }
    
    public function saveAction()
    {
        $id = $this->params()->fromPost('id',0);
        $mode = "edit";
        $msg='Record updated successfully';
        $errorMsg='Error in updating record';
        $group = $this->getEntityManager()->find('Application\Entity\Group', $id);
        if(!$group instanceof Group) {
            $group = new Group();
            $msg='Record created successfully';
            $errorMsg='Error in saving record';
            $mode = "create";
        }
        $form = $group->getForm();
        $form->bind($group);
        if ($this->request->isPost()) {
           $form->setData($this->getRequest()->getPost());
           $validator = new \DoctrineModule\Validator\NoObjectExists(array(
                   'object_repository' => $this->getEntityManager()->getRepository('Application\Entity\Group'),
                   'fields' => array('name')
               ));
           if($form->isValid()){
               if (!($validator->isValid($this->getRequest()->getPost('name'))))
               {
                   $this->addErrorMessage('Group Name should be Unique');
                   $view = new ViewModel();
                   $view->setTemplate("admin/group/" . $mode . ".phtml");
                   $view->setVariable('groupForm', $form);
                   return $view;
               }
               $this->getEntityManager()->persist($group);
               $this->getEntityManager()->flush();
               $this->addSuccessMessage('Record created successfully');
               return $this->redirect()->toRoute('route-name', array('controller'=>'group','action' => 'list'));
           }else{
               $this->addErrorMessage('Enter Valid Data');
           }
        }
        $view=new ViewModel();
        $view->setTemplate("admin/group/" . $mode . ".phtml");
        $view->setVariable('groupForm', $form);
        return $view;
    }
    
    public function deleteAction()
    {
        if($this->redirection){
            return $this->redirect()->toRoute('admin-dashboard');
        }else{
            $id = $this->params()->fromRoute('id');
            $group = $this->getEntityManager()->find('\Application\Entity\Group', $id);
            $this->getEntityManager()->remove($group);
            $this->getEntityManager()->flush();
            $this->addSuccessMessage('Record deleted successfully');
            return $this->redirect()->toRoute('route-name', array('controller'=>'group','action' => 'list'));    
        }
    }
    public function addSuccessMessage($message) { $this->addMessage('success',$message); }
    public function addErrorMessage($message) { $this->addMessage('error',$message); }
    protected function addMessage($type,$message) { 
        $this->flashMessenger()->setNamespace($type)->addMessage(array($type => $message));
    
    }
}