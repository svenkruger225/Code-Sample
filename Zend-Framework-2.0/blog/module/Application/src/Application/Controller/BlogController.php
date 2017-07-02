<?php
/**
 * Class: BlogController
 *
 * @package  Controller
 * @author   Arslan Ali <shayansolutions@gmail.com>
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Blog;

class BlogController extends AbstractActionController
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
     * list blog
     * @return ViewModel
     */
    public function listAction()
    {
        $list = $this->getEntityManager()->getRepository('Application\Entity\Blog')->findAll();
        return new ViewModel(array('list'=>$list));
    }
    /**
     * create blog
     * @return ViewModel
     */

    public function createAction()
    {
        $blog = new Blog();
        $form = $blog->getForm();
        $form->setAttribute('class', 'form-horizontal form-row-seperated');
        $form->bind($blog);
         
        $categories = $this->getEntityManager()->getRepository('Application\Entity\Category')->getOptionsForSelect();
        $form->get("category")->setValueOptions($categories);

        $view=new ViewModel();
        $view->setVariables(array("form"=>$form));
        return $view;
    }
    /**
     * edit blog
     * @return ViewModel
     */
    
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $blog = $this->getEntityManager()->find('\Application\Entity\Blog', $id);
        $form = $blog->getForm();
        $form->setAttribute('class', 'form-horizontal form-row-seperated');
        $form->bind($blog);
        $categories = $this->getEntityManager()->getRepository('Application\Entity\Category')->getOptionsForSelect();
        $form->get("category")->setValueOptions($categories);
        
        $categories=array();
        foreach($blog->getCategory() as $category){
            $categories[]=$category->getId();
        }
        $form->get('category')->setValue($categories);
        
        $view=new ViewModel();
        $view->setVariables(array("form"=>$form));
        return $view;
    }
    /**
     * save blog
     * @return ViewModel
     */
    
    public function saveAction()
    {
        $id = $this->params()->fromPost('id',0);
        $mode = "edit";
        
        $msg='Record updated successfully';
        $errorMsg='Error in updating record';
        $blog = $this->getEntityManager()->find('Application\Entity\Blog', $id);
        if(!$blog instanceof Blog) {
            $blog = new Blog();
            $mode = "create";
            $msg='Record created successfully';
            $errorMsg='Error in saving record';
        }
        $form = $blog->getForm();
        $form->bind($blog);
        $categories = $this->getEntityManager()->getRepository('Application\Entity\Category')->getOptionsForSelect();
        $form->get("category")->setValueOptions($categories);
        
        if ($this->request->isPost()) {
            if($mode=="edit"){
                $blog->removeAllCategory();
                $this->getEntityManager()->persist($blog);
                $this->getEntityManager()->flush();
            }
            $data = $this->getRequest()->getPost();
            $form->setData($data);
             if ($form->isValid()){
                //Save Category blog relations
                foreach ($data['category'] as $category)
                {
                   $blog->addCategory($this->getEntityManager()->find('Application\Entity\Category',$category));
                }
                $this->getEntityManager()->persist($blog);
                $this->getEntityManager()->flush();
               
                $this->addMessage('success',$msg);
                return $this->redirect()->toRoute('route-name', array('controller'=>'blog','action' => 'list'));
            }
            else{
                $this->addMessage('error',$errorMsg);
            }
        }
        $form->setAttribute('class', 'form-horizontal form-row-seperated');
        $view=new ViewModel();
        $view->setTemplate("admin/blog/".$mode.".phtml");
        $view->setVariable('form', $form);
        return $view;
    }
    /**
     * delete blog
     * @return ViewModel
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $blog = $this->getEntityManager()->find('\Application\Entity\Blog',$id);
        $this->getEntityManager()->remove($blog);
        $this->getEntityManager()->flush();
        $this->addMessage('success','Record deleted successfully');
        return $this->redirect()->toRoute('route-name', array('controller'=>'blog','action' => 'list'));    
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

