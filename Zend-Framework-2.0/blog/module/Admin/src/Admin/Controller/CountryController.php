<?php
/**
 * Class: CountryController
 *
 * @package  Controller
 * @author   Arslan Ali <shayansolutions@gmail.com>
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Form\CountryForm;
use Application\Entity\Country;

class CountryController extends AbstractActionController
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
    public function listAction()
    {
        $listCountry = $this->getEntityManager()->getRepository('Application\Entity\Country')->findAll();
        return new ViewModel(array('countries'=>$listCountry));
    }
    public function createAction()
    {
        $form = new CountryForm('countryForm');
        $view=new ViewModel();
        $view->setVariable('countryForm', $form);
        return $view;
    }
    
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $form = new CountryForm('countryForm');
        $country = $this->getEntityManager()->find('\Application\Entity\Country', $id);
        $form->bind($country);
        if ($this->request->isPost()) {
            $country->setName($this->getRequest()->getPost('name'));
            $country->setCode($this->getRequest()->getPost('code'));
            $country->setStatus($this->getRequest()->getPost('status'));
            $country->setCurrencyCode($this->getRequest()->getPost('currencyCode'));

            $this->getEntityManager()->persist($country);
            $this->getEntityManager()->flush();
            $this->addMessage('success','Record updated successfully');
            return $this->redirect()->toRoute('route-name', array('controller'=>'country','action' => 'list'));
        }
        $view=new ViewModel();
        $view->setVariable('countryForm', $form);
        return $view;
    }
    
    public function saveAction()
    {
        $form = new CountryForm('countryForm');
        $country = new Country();
        $form->bind($country);
        if ($this->request->isPost()) {
            
            $country->setName($this->getRequest()->getPost('name'));
            $country->setCode($this->getRequest()->getPost('code'));
            $country->setStatus($this->getRequest()->getPost('status'));
            $country->setCurrencyCode($this->getRequest()->getPost('currencyCode'));

            $this->getEntityManager()->persist($country);
            $this->getEntityManager()->flush();
            $this->addMessage('success','Record created successfully');
            return $this->redirect()->toRoute('route-name', array('controller'=>'country','action' => 'list'));
        }
        $view=new ViewModel();
        $view->setTemplate("admin/country/create.phtml");
        $view->setVariable('form', $form);
        return $view;
    }
    
    public function deleteAction()
    {
        if($this->redirection){
            return $this->redirect()->toRoute('admin-dashboard');
        }else{
            $id = $this->params()->fromRoute('id');
            $this->getEntityManager()->getRepository('\Application\Entity\City')->deleteCitites($id);
            $country = $this->getEntityManager()->find('\Application\Entity\Country', $id);
            $this->getEntityManager()->remove($country);
            $this->getEntityManager()->flush();
            $this->addMessage('success','Record deleted successfully');
            return $this->redirect()->toRoute('route-name', array('controller'=>'country','action' => 'list'));  
        }
  
    }
    public function addSuccessMessage($message) { $this->addMessage('success',$message); }
    public function addErrorMessage($message) { $this->addMessage('error',$message); }
    protected function addMessage($type,$message) { 
        $this->flashMessenger()->setNamespace($type)->addMessage(array($type => $message));
    
    }
}

