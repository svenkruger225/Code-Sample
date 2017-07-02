<?php
namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;

class LoginForm extends Form {

    public function __construct($name) {
        
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'login-form');
        $this->add(array(
            'name' => 'email',
            'type' => 'text',
            'attributes' =>  array(
                'class'=> 'form-control placeholder-no-fix',
                'placeholder' => 'Username',
            ),
            'options' => array(
                'label' => 'Email',
                'id' => 'email',
                'placeholder' => 'example@example.com'             
            )
        ));

       $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' =>  array(
                'class'=> 'form-control placeholder-no-fix',
                'placeholder' => 'Password',
            ),
            'options' => array(
                'label' => 'Password',
                'id' => 'password',
                'placeholder' => '**********'
            )
       ));
       
       $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'loginCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
             
            'attributes' => array(
                'class'=> 'btn btn-info pull-right',
                'type' => 'submit',
                'value' => 'Submit',
            ),
        ));
    }
}