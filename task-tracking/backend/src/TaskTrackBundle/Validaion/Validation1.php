<?php

namespace TaskTrackBundle\Validation;

class Validation implements EmailValidationInterface, 
                            LengthValidationInterface, 
                            MaxValidationInterface, 
                            MinValidationInterface, 
                            NotNullValidationInterface, 
                            NotBlankValidationInterface {
    
    
    public function checkEmail($input) {
        
    }

    public function checkLength($input) {
        
    }

    public function checkMax($input) {
        
    }

    public function checkMin($input) {
        
    }

    public function checkNotBlank($input) {
        
    }

    public function checkNotNull($input) {
        
    }

}

