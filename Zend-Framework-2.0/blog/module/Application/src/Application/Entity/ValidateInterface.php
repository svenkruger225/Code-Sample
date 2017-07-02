<?php
namespace Application\Entity;

interface ValidateInterface {
    /**
     * Do somekind of validation and return true is is valid or array of
     * validation rule breaks.
     * 
     * @param boolean $unsetInvalid Nullify invalid data
     * 
     * @return mixed boolean | array
     */
    public function validate($unsetInvalid = false); 
}
