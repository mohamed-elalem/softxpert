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
        $recordCount =  $query->add("select", $query->expr()->count("$alias.id"))->getQuery()->getSingleScalarResult();
        return ceil($recordCount / $itemPerPage);
    }
    
    public function getResult($query, $page, $itemsPerPage) {

        return $query->setFirstResult(($page - 1) * $itemsPerPage)->setMaxResults($page * $itemsPerPage)->getQuery()->getArrayResult();
    }
}
