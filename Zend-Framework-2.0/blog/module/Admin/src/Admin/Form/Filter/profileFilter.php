<?php
namespace admin\Form\Filter;

use Zend\InputFilter\InputFilter;

class profileFilter extends InputFilter {

    public function __construct(){
        
        $isEmpty = \Zend\Validator\NotEmpty::IS_EMPTY;
        $invalidEmail = \Zend\Validator\EmailAddress::INVALID_FORMAT;
        $NOT_MATCH = \Zend\Validator\Regex::NOT_MATCH;
        $NOT_SAME = \Zend\Validator\Identical::NOT_SAME;
        $this->add(array(
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            $isEmpty => 'Password can not be empty.'
                        )
                    )
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 6,
                    ),
                ),
            )
        ));
        $this->add(array(
            'name' => 'retype',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            $isEmpty => 'Please retype your password.'
                        )
                    )
                ),
                 array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                        'messages' => array(
                            $NOT_SAME => 'Please make sure the passwords you entered are identical.' 
                        ),
                    ),
                ),
            )
        ));
        
     
    }
}