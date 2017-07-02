<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Csrf;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CityForm extends Form implements ObjectManagerAwareInterface
{
    protected $objectManager;
     public function __construct(ObjectManager $objectManager)
    {
        $this->setObjectManager($objectManager);
        parent::__construct('cityForm');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-row-seperated');
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type City Name',
                'class'=> 'form-control',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'City Name'                
            )
        ));

        $this->add(array( 
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'country_id',
            'attributes' =>  array(
                'id' => 'country_name', 
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Country Name',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => 'Application\Entity\Country',
                'property'       => 'name',
                'is_method' => true,
                'find_method' => array(
                    'name' => 'findBy',
                    'params' => array(
                        'criteria' => array('status' => 'active'),
                    ),
                ),
            ),
        )); 
        
        $this->add(array( 
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status',
            'attributes' =>  array(
                'id' => 'country_status', 
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Country Status',
                'options' => array(
                    'inactive' => 'Inactive',
                    'active' => 'Active',
                    
                ),
            ),
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