<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Helpers;

/**
 * Description of PaginatorService
 *
 * @author mohamedelalem
 */
class PaginatorHelper {
    
    public function getPages($query, $itemPerPage) {
        $alias = $query->getRootAlias();
        $recordCount = $this->getCount($query);
        return ceil($recordCount / $itemPerPage);
    }
    
    public function getCount($query) {
        $alias = $query->getRootAlias();
        return $query->add("select", $query->expr()->count("$alias.id"))->getQuery()->getSingleScalarResult();
    }
    
    public function getResult($query, $page, $itemsPerPage) {
        return $query->setFirstResult(($page - 1) * $itemsPerPage)->setMaxResults($itemsPerPage)->getQuery()->getArrayResult(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);
    }
}
