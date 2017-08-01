<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterByDuration
 *
 * @author mohamedelalem
 */
class FilterByDuration extends FilterByBetween {
    public function initBetween($colValueMin, $colValueMax, $next) {
        $this->colName = "duration";
        parent::initBetween($colValueMin, $colValueMax, $next);
        return $this;
    }
}
