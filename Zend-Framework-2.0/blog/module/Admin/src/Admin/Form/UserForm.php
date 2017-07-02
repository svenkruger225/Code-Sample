<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Csrf;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserForm extends Form implements ObjectManagerAwareInterface
{
    protected $objectManager;
     public function __construct(ObjectManager $objectManager)
    {
        $this->setObjectManager($objectManager);
        parent::__construct('userForm');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-row-seperated');
        
        $this->add(array(
            'name' => 'user_name',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Name',
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'User Name'                
            )
        ));
        $this->add(array(
            'name' => 'emailId',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Email Id',
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Email Id'                
            )
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'placeholder' => 'Type Passeord',
                'class'=> 'form-control',
            ),
             'options' => array(
                 'label' => 'Password',
                 'id' => 'password',
                 'placeholder' => '**********'
             )
        ));
        $this->add(array(
            'name' => 'address',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Address',
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'address'                
            )
        ));
        $this->add(array(
            'name' => 'location',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Location',
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Location'                
            )
        ));
        $this->add(array(
            'name' => 'Phone_no',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Phone No',
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Phone No'                
            )
        ));
        $this->add(array(
            'name' => 'learning_mode',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Learning Mode',
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Learning Mode'                
            )
        ));

        $this->add(array( 
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'city_name',
            'attributes' =>  array(
                'id' => 'city_name', 
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'City Name',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => 'Application\Entity\City',
                'property'       => 'name',
                'empty_option'   => '--- please choose ---'
            ),
        )); 
        $this->add(array( 
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'group_name',
            'attributes' =>  array(
                'id' => 'city_name', 
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Group Name',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => 'Application\Entity\Group',
                'property'       => 'name',
                'empty_option'   => '--- please choose ---'
            ),
        )); 
        $this->add(array( 
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'category_name',
            'attributes' =>  array(
                'id' => 'city_name', 
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Category Name',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => 'Application\Entity\Category',
                'property'       => 'name',
                'empty_option'   => '--- please choose ---'
            ),
        ));
        
        $this->add(array( 
            'type' => 'Zend\Form\Element\Select',
            'name' => 'country_status',
            'attributes' =>  array(
                'id' => 'country_status', 
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'User Status',
                'options' => array(
                    '0' => 'Inactive',
                    '1' => 'Active',
                    
                ),
            ),
        ));
        $this->add(array(
            'name' => 'requirment',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'placeholder' => 'Type Requirment',
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Requirment'                
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