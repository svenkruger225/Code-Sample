<?php
/**
 * Class: CategoryController
 *
 * @package  Controller
 * @author   Arslan Ali <shayansolutions@gmail.com>
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Category;
use Application\Entity\CountryCategory;
use DoctrineExtensions\NestedSet\Config;
use DoctrineExtensions\NestedSet\Manager;

class CategoryController extends AbstractActionController
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
     * list category
     * @return ViewModel
     */
    
    public function listAction()
    {
        $listCategories = $this->getEntityManager()->getRepository('Application\Entity\Category')->getCategoryList();
        return new ViewModel(array('categoriesList'=>$listCategories));
    }
    /**
     * create category
     * @return ViewModel
     */
    public function createAction()
    {
        $category = new Category();
        $form = $category->getForm();
        $form->setAttribute('class', 'form-horizontal form-row-seperated');
        $form->bind($category);
         
        //$categories = $this->getEntityManager()->getRepository('Application\Entity\Category')->getOptionsForSelect();
        $categories = $this->getEntityManager()->getRepository('Application\Entity\Category')->getCategoriesForSelect();
        
        $form->get("root")->setValueOptions($categories);
        
        $view=new ViewModel();
        $view->setVariables(array("form"=>$form));
        return $view;
    }
    
    /**
     * edit category
     * @return ViewModel
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $category = $this->getEntityManager()->find('\Application\Entity\Category', $id);
        $form = $category->getForm();
        $form->setAttribute('class', 'form-horizontal form-row-seperated');
        $form->bind($category);
        
        $categories = $this->getEntityManager()->getRepository('Application\Entity\Category')->getCategoriesForSelect();
        
        $form->get("root")->setValueOptions($categories);
        if($category->getRootValue()){
            $form->get("root")->setValue($category->getRootValue());
        }
        $view=new ViewModel();
        $view->setVariables(array("form"=>$form));
        return $view;
    }
    /**
     * save category
     * @return ViewModel
     */
    public function saveAction()
    {
        $config = new Config($this->getEntityManager(), 'Application\Entity\Category');
        $nsm = new Manager($config);
        
        $id = $this->params()->fromPost('id',0);
        $mode = "edit";
        
        $msg='Record updated successfully';
        $errorMsg='Error in updating record';
        $category = $this->getEntityManager()->find('Application\Entity\Category', $id);
        if(!$category instanceof Category) {
            $category = new Category();
            $mode = "create";
            $msg='Record created successfully';
            $errorMsg='Error in saving record';
        }
        $form = $category->getForm();
        $form->bind($category);
        
        $categories = $this->getEntityManager()->getRepository('Application\Entity\Category')->getCategoriesForSelect();
        
        $form->get("root")->setValueOptions($categories);
        $listCountry = $this->getEntityManager()->getRepository('Application\Entity\Country')->findAll();
        
        if ($this->request->isPost()) {
            $parentId=$this->getRequest()->getPost('root');
            $parentCategory = $this->getEntityManager()->find('\Application\Entity\Category', $parentId);
            $data = $this->getRequest()->getPost();
            $form->setData($data);
             if ($form->isValid()){
                if($parentId==$id){
                    $category->setName($this->getRequest()->getPost('name'));
                    $category->setRootValue($parentId);
                }
                else if(empty($parentCategory)) //root category
                {
                    $category = new Category();
                    $category->setName($this->getRequest()->getPost('name'));
                    $root = $nsm->createRoot($category);
                   
                }
                else
                {
                    $node = $nsm->wrapNode($parentCategory);
                    $node->addChild($category);                    
                }
                
                $this->getEntityManager()->persist($category);
                $this->getEntityManager()->flush();
                
                $this->addMessage('success',$msg);
                return $this->redirect()->toRoute('route-name', array('controller'=>'category','action' => 'list'));
            }
            else{
                $this->addMessage('error',$errorMsg);
            }
        }
        $form->setAttribute('class', 'form-horizontal form-row-seperated');
        $view=new ViewModel();
        $view->setTemplate("admin/category/".$mode.".phtml");
        $view->setVariables(array("form"=>$form,'countries'=>$listCountry));
        return $view;
    }
    /**
     * delete category
     * @return ViewModel
     */
    
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $category = $this->getEntityManager()->getRepository('\Application\Entity\Category')->remove($id);
        $this->addMessage('success','Record deleted successfully');
        return $this->redirect()->toRoute('route-name', array('controller'=>'category','action' => 'list'));    
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

