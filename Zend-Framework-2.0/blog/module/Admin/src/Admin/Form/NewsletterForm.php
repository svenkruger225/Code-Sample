<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Csrf;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;

class NewsletterForm extends Form implements ObjectManagerAwareInterface
{
    protected $objectManager;
     public function __construct(ObjectManager $objectManager)
    {
        $this->setObjectManager($objectManager);
        parent::__construct('cityForm');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-row-seperated');
        
        $this->add(array( 
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'exam_id',
            'attributes' =>  array(
                'id' => 'exam_id', 
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Select Exam',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => 'Application\Entity\Exams',
                'property'       => 'title',
            ),
        ));
        $this->add(array(
            'name' => 'subject',
            'attributes' => array(
                'type' => 'text',
                'class'=> 'form-control',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Email Subject'                
            )
        ));
        $this->add(array(
            'name' => 'content',
            'attributes' => array(
                'type' => 'textarea',
                'id' => 'content',
                'class'=> 'form-control',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'content'                
            )
        ));

         $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'class'=>'btn btn-info',
            ),
        ));
    }
     public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
 
        return $this;
    }
 
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}