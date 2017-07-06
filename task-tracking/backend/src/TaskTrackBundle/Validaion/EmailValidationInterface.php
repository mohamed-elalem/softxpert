<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Validation;

/**
 *
 * @author mohamedelalem
 */
interface EmailValidationInterface {
    public function checkEmail($input);
}
