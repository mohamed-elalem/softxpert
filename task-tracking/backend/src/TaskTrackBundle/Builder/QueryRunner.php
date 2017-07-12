<?php

namespace TaskTrackBundle\Builder;
use Doctrine\ORM\QueryBuilder;

class QueryRunner implements QueryTemplateInterface {
    
    protected $query;
    protected $container;
    
    public function runQuery() {
        
        return $query->getQuery()->getResults();
    }
    
    public function flush() {
        $query = null;
    }

    public function execute() {
        $results = $this->runQuery();
        $this->flush();
        return $results;
    }
    
    public function setContainer($container) {
        $this->container = $container;
    }

}
