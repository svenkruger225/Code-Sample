<?php
namespace admin\Form;

use Zend\Form\Form;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;

class profileForm extends Form implements ObjectManagerAwareInterface
{

     public function __construct()
    {
        
        parent::__construct('profileForm');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal contact-form');
        $this->setAttribute('id','contact-form');
        /*Your name Info*/
        
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'placeholder' => 'Password',
                'class'=> 'form-control',
                'tabindex' =>'14'
            ),
             'options' => array(
                 'label' => 'Password',
                 'id' => 'password',
                 'placeholder' => '**********'
             )
        ));
        $this->add(array(
            'name' => 'retype',
            'attributes' => array(
                'type' => 'password',
                'placeholder' => 'Re-Enter Password',
                'class'=> 'form-control',
                'tabindex' =>'16'
            ),
            'options' => array(
                'label' => 'Re-Enter Password'                
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