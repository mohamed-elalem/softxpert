<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Service;
use Symfony\Component\Form\Form;
/**
 * Description of FormService
 *
 * @author mohamedelalem
 */
class FormService {
    
    private $form;
    private $data;
    
    public function init(Form $form, $data) {
        $this->form = $form;
        $this->data = $data;
        return $this;
    }
    
    public function isValid() {
        $this->form = $this->form->submit($this->data);
        dump($this->form);
        die();
        return $this->form->isValid();
    }
    
    public function getErrors() {
        $errors = array();
        
        foreach ($this->form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }
        foreach ($this->form->all() as $child) {
            if (!$child->isValid()) {
                
                $errors[$child->getName()] = $this->getErrors($child);
            }
        }
        return $errors;
    }
}
