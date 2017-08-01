<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterByTitle
 *
 * @author mohamedelalem
 */
class FilterByTitle extends Filter {

    public function init($colValue, $next) {
        $this->colName = "title";
        parent::init($colValue, $next);
        return $this;
    }
    
    public function filter($qb) {
        $alias = $qb->getRootAlias();
        $qb = $qb->where($qb->expr()->like("lower($alias.$this->colName)", ":$this->colName"));
        
        $qb = $this->next->filter($qb);
        $qb = $qb->setParameter($this->colName, "%" . strtolower($this->colValue) . "%");
        return $qb;
    }
}
