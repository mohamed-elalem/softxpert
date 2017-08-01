<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterByBetween
 *
 * @author mohamedelalem
 */
class FilterByBetween extends Filter {
    private $colValueMin;
    private $colValueMax;
    
    public function initBetween($colValueMin, $colValueMax, $next) {
        $this->colValueMin = $colValueMin;
        $this->colValueMax = $colValueMax;
        $this->next = $next;
        
    }

    public function filter($qb) {
        $alias = $qb->getRootAlias();
        $qb = $qb->andWhere($qb->expr()->between("$alias.$this->colName", ":min", ":max"));
        $qb = $this->next->filter($qb);
        $qb = $qb->setParameter("min", $this->colValueMin)
                ->setParameter("max", $this->colValueMax);
        
        return $qb;
    }

}
