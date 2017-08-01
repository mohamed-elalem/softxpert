<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterByUpdateDate
 *
 * @author mohamedelalem
 */
class FilterByUpdateDate extends FilterByBetween {

    public function initBetween($colValueMin, $colValueMax, $next) {
        $startDate = new \DateTime($colValueMin);
        $startDate->add(new \DateInterval("P1D"));
        $startDate = $startDate->format("Y-m-d");
        
        $endDate = new \DateTime($colValueMax);
        $endDate = $endDate->format("Y-m-d");
        
        parent::initBetween($colValueMin, $colValueMax, $next);
        
        $this->colName = "updated_at";
        return $this;
    }
}
