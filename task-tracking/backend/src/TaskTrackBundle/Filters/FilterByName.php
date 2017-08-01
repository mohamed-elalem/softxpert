<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterByName
 *
 * @author mohamedelalem
 */
class FilterByName extends Filter {
    
    public function init($colValue, $next) {
        $this->colName = "name";
        parent::init($colValue, $next);
        return $this;
    }
    
    public function filter($qb) {
        $alias = $qb->getRootAlias();
        $qb = $qb->andWhere($qb->expr()->like("lower($alias.$this->colName)", ":$this->colName"));
        $qb = $this->next->filter($qb);
        $qb->setParameter($this->colName, $this->colValue);
        return $qb;
    }

}
