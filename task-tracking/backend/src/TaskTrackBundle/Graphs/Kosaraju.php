<?php

namespace TaskTrackBundle\Graphs;

use Symfony\Component\Config\Definition\Exception\Exception;

class Kosaraju extends Graph {

    private $adjList;
    private $adjListTranspose;
    private $maxVertex;

    public function __construct() {
        $this->adjList = [];
        $this->adjListTranspose = [];
        $this->maxVertex = 0;
    }

    public function setup($edgeList) {
        
        foreach ($edgeList as $idx => $pairs) {
            $u = $pairs[0];
            $v = $pairs[1];
            if(! isset($this->adjList[$u])) {
                $this->adjList[$u] = [];
                $this->adjListTranspose[$u] = [];
                $this->inward[$u] = 0;
                $this->explored[$u] = 0;
                $this->discovered[$u] = 0;
                $this->state[$u] = parent::WHITE;
            }
            if(! isset($this->adjList[$v])) {
                $this->adjList[$v] = [];
                $this->adjListTranspose[$v] = [];
                $this->inward[$v] = 0;
                $this->explored[$v] = 0;
                $this->discovered[$v] = 0;
                $this->state[$v] = parent::WHITE;
            }
            $this->adjList[$u][] = $v;
            $this->adjListTranspose[$v][] = $u;
            $this->inward[$v]++;
        }

        parent::initialize($this->adjList);
    }

    public function checkForCycles() {
        $valid = true;
        foreach ($this->adjList as $parent => $children) {
            if ($this->state[$parent] == parent::WHITE) {
                $this->state[$parent] = parent::WHITE;
                
                $valid &= parent::checkGraphValidity($this->adjList, $parent);
            }
        }
        return $valid;
    }

    public function getCycles() {
        $cycles = [];
        $n = count($this->stack);
        parent::clear();
        for ($i = $n - 1; $i >= 0; $i--) {
            $parent = $this->stack[$i];
            if ($this->state[$parent] == parent::WHITE) {
                $cycles[] = parent::getStronglyConnectedComponents($this->adjListTranspose, $parent, false, 0, true);
            }
        }
        return $cycles;
    }
    
    function getAdjList() {
        return $this->adjList;
    }

    function getAdjListTranspose() {
        return $this->adjListTranspose;
    }


}
