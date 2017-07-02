<?php
/**
 * Class: CityController
 *
 * @package  Controller
 * @author   Arslan Ali <shayansolutions@gmail.com>
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Form\CityForm;
use Application\Entity\City;

class CityController extends AbstractActionController
{
    protected $em;
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
    /**
     * list city
     * @return ViewModel
     */
    
    public function listAction()
    {
        $listCity = $this->getEntityManager()->getRepository('Application\Entity\City')->findAll();
        return new ViewModel(array('cities'=>$listCity));
    }
    /**
     * create city
     * @return ViewModel
     */
    
    public function createAction()
    {
        $entityManager = $this->getEntityManager();
        $form = new CityForm($entityManager);
        $view=new ViewModel();
        $view->setVariable('cityForm', $form);
        return $view;
    }
    /**
     * edit city
     * @return ViewModel
     */
    
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $entityManager = $this->getEntityManager();
        $form = new CityForm($entityManager);
        $city = $this->getEntityManager()->find('\Application\Entity\City', $id);
        
        $form->bind($city);
        
        $form->get('country_id')->setValue($city->getCountry()->getId()); 
        if ($this->request->isPost()) {
            $countryId=$this->getRequest()->getPost('country_id');
            $country = $this->getEntityManager()->find('\Application\Entity\Country', $countryId);
        
            $city->setName($this->getRequest()->getPost('name'));
            $city->setCountry($country);
            $city->setStatus($this->getRequest()->getPost('status'));
            $this->getEntityManager()->persist($city);
            $this->getEntityManager()->flush();
            $this->addMessage('success','Record updated successfully');
            return $this->redirect()->toRoute('route-name', array('controller'=>'city','action' => 'list'));
        }
        $view=new ViewModel();
        $view->setVariable('cityForm', $form);
        return $view;
    }
    /**
     * save city
     * @return ViewModel
     */
    
    public function saveAction()
    {
        if ($this->request->isPost()) {
            $city = new City();
            $countryId=$this->getRequest()->getPost('country_id');
            $country = $this->getEntityManager()->find('\Application\Entity\Country', $countryId);
        
            $city->setName($this->getRequest()->getPost('name'));
            $city->setCountry($country);
            $city->setStatus($this->getRequest()->getPost('status'));

            $this->getEntityManager()->persist($city);
            $this->getEntityManager()->flush();
            $this->addMessage('success','Record created successfully');
            return $this->redirect()->toRoute('route-name', array('controller'=>'city','action' => 'list'));
        }
        $view=new ViewModel();
        $view->setTemplate("admin/city/create.phtml");
        $view->setVariable('form', $form);
        return $view;
    }
    /**
     * delete city
     * @return ViewModel
     */
    
    public function deleteAction()
    {
        if($this->redirection){
            return $this->redirect()->toRoute('admin-dashboard');
        }else{
            $id = $this->params()->fromRoute('id');
            $country = $this->getEntityManager()->find('\Application\Entity\City', $id);
            $this->getEntityManager()->remove($country);
            $this->getEntityManager()->flush();
            $this->addMessage('success','Record deleted successfully');
            return $this->redirect()->toRoute('route-name', array('controller'=>'city','action' => 'list'));    
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

