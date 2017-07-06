<?php

namespace TaskTrackBundle\Helpers;

class Helper {
    
    public function getFormErrors(\Symfony\Component\Form\Form $form)
    {
        $errors = array();

        // Global
        foreach ($form->getErrors(true) as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        // Fields
        foreach ($form as $child /** @var Form $child */) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }
        
        
        return $errors;
    }

}