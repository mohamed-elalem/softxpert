<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterByInProgress
 *
 * @author mohamedelalem
 */
class FilterByInProgress extends Filter {
    
    public function init($colValue, $next) {
        $this->colName = "in_progress";
        parent::init($colValue, $next);
        return $this;
    }
    
    public function filter($qb) {
        $alias = $qb->getRootAlias();
        $qb = $qb->andWhere("$alias.$this->colName = :$this->colName");
        $this->next->filter($qb);
        $qb = $qb->setParameter($this->colName, $this->colValue);
        return $qb;
    }
}
