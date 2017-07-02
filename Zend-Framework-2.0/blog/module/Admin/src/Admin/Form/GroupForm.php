<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Csrf;

class GroupForm extends Form {

   public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-row-seperated');
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Group Name',
                'required' => 'required',
                'class'=> 'form-control',
            ),
            'options' => array(
                'label' => 'Group Name'                
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
}