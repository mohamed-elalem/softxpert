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
            if ($pairs[1] >= 0) {
                $this->adjList[$pairs[0]][] = $pairs[1];
                $this->adjListTranspose[$pairs[1]][] = $pairs[0];
            } 
            else {
                if(! count($this->adjList[$pairs[0]])) {
                    $this->adjList[pairs[0]] = [];
                }
            }
        }

        parent::initialize($this->adjList);
    }

    public function run() {
        $valid = true;
        foreach ($this->adjList as $parent => $children) {
            if (! array_key_exists($parent, $this->state)) {
                $this->state[$parent] = parent::WHITE;
                
                $valid &= parent::dfs($this->adjList, $parent);
            }
        }
//        if ($valid) {
//            $s = parent::getLowestPossibleVertex();
//            parent::clear();
//            parent::dfs($this->adjList, $s, true);
//        } else {
//            throw new Exception("This graph is cyclic");
//        }
        return $valid;
    }

    public function getStronglyConnectedComponents() {
        $cycles = [];
        $n = count($this->stack);
        parent::clear();
        for ($i = $n - 1; $i >= 0; $i--) {
            $parent = $this->stack[$i];
            if ($this->state[$parent] == parent::WHITE) {
                $cycles[] = parent::dfs($this->adjListTranspose, $parent, false, 0, true);
            }
        }
        return $cycles;
    }

}
