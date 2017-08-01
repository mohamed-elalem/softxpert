<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterByDescription
 *
 * @author mohamedelalem
 */
class FilterByDescription extends Filter {

    public function init($colValue, $next) {
        $this->colName = "description";
        parent::init($colValue, $next);
        return $this;
    }
    
    
    public function filter($qb) {
        $qb = $qb->andWhere($qb->expr()->like("lower($alias.$this->colName)", ":$this->colName"));
        $this->next->filter($qb);
        $qb = $qb->setParameter($this->colName, $this->colValue);
        return $qb;
    }

}
