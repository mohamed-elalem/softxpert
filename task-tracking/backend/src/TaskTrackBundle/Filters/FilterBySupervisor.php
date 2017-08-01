<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterBySupervisor
 *
 * @author mohamedelalem
 */
class FilterBySupervisor extends Filter {
    
    public function init($colValue, $next) {
        $this->colName = "supervisor";
        parent::init($colValue, $next);
        return $this;
    }
    
    public function filter($qb) {
        $alias = $qb->getRootAlias();
        return $this->next->filter(
                $qb->andWhere("$alias.$this->colName = :$this->colName")
                )->setParameter($this->colName, $this->colValue);
    }

}
