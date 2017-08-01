<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of Filter
 *
 * @author mohamedelalem
 */
abstract class Filter implements EntityInterface {
    protected $next;
    protected $colName;
    protected $colValue;
    
    public function init($colValue, $next) {
        $this->colValue = $colValue;
        $this->next = $next;
    }
}
