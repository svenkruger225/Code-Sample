<?php
namespace Admin\Form\Filter;

use Zend\InputFilter\InputFilter;

class NewsletterFilter extends InputFilter {

    public function __construct(){
        
        $this->add(array(
            'name' => 'subject',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            )
          ));
        $this->add(array(
            'name' => 'content',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            )
          ));
    }
}