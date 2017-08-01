<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;

/**
 * Description of FilterByRole
 *
 * @author mohamedelalem
 */
class FilterByRole extends Filter {
    
    public function init($colValue, $next) {
        $this->colName = "role";
        parent::init($colValue, $next);
        return $this;
    }
    
    public function filter($qb) {
        $alias = $qb->getRootAlias();
        $roles = $this->getRoles($this->colValue);
        $qb = $qb->andWhere($qb->expr()->in("$alias.$this->colName", $roles));
        $qb = $this->next->filter($qb);
        return $qb;
    }
    
    private function getRoles($colValue) {
        $roles = [];
        $i = 0;
        while($colValue > 0) {
            $roles[] = ($colValue & -$colValue);
            $colValue &= ~($colValue & -$colValue);
        }
        return $roles;
    }
}
