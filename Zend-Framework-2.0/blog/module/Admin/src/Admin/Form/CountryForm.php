<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Csrf;

class CountryForm extends Form {

   public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-row-seperated');
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Country Name',
                'class'=> 'form-control',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Country Name'                
            )
        ));

        $this->add(array(
            'name' => 'code',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Country Code',
                'class'=> 'form-control',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Country Code',
            )
        ));
        $this->add(array(
            'name' => 'currencyCode',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type Currency Code',
                'class'=> 'form-control',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Currency Code',
            )
        ));
        $this->add(array( 
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status',
            'attributes' =>  array(
                'id' => 'country_status', 
                'class'=> 'form-control',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Country Status',
                'options' => array(
                    'active' => 'Active',
                    'inactive' => 'Inactive',
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
}