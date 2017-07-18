<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Graphs;

/**
 * Description of DepthFirstSearch
 *
 * @author mohamedelalem
 */
abstract class Graph implements StronglyConnectedComponents {
    private $discovered;
    private $adjList;
    private $explored;
    private $time;
    protected $state;
    protected $stack;
    private $dp;
    private $depth;
    private $parent;
    private $vertices;
    const WHITE = 0;
    const GRAY = 1;
    const BLACK = 2;
    
    public function initialize($adjList) {
        $this->discovered = [];
        $this->explored = [];
        $this->time = 0;
        $this->state = [];
        $this->stack = [];
        $this->adjList = $adjList;
        $this->dp = [];
        
        foreach($adjList as $parent => $children) {
            foreach($children as $child) {
                $dp[$parent][$child] = 0;
                $dp[$child][$parent] = 0;
            }
        }
//        $this->dp = new SplFixedArray($numOfVertices);
//        for($i = 0; $i < $numOfVertices; $i++) {
//            $this->dp[$i] = 0;
//        }
        $this->depth = [];
    }
    
    public abstract function setup($setup);
    
    public abstract function run();
    
    public function clear() {
        foreach($this->discovered as $vertex => $time) {
            $this->discovered[$vertex] = 0;
            $this->explored[$vertex] = 0;
            $this->state[$vertex] = self::WHITE;
        }
        $this->time = 0;
    }
    
    /**
     * This method is responsible for traversing the graph with depth first search strategy
     * and check whether this graph is cyclic or not and return strongly connected components if reuqests
     * @param type $u
     * @return boolean true if the graph is valid | false if the graph is invalid
     */
    
    public function dfs($adjList, $parent, $setDepth = false, $depth = 0, $returnComponents = false) {
        $this->discovered[$parent] = $this->time++;
        $this->state[$parent] = static::GRAY;
        $this->depth[$parent] = $depth;
        $valid = true;
        $cycle = [$parent];
        
        if($setDepth) {
            $this->depth[$parent] = $depth;
        }
        dump($parent);
        if(isset($adjList[$parent])) {
            foreach($adjList[$parent] as $idx => $neighbor) {
                
                if(! isset($this->state[$neighbor]) || $this->state[$neighbor] == self::WHITE) {
                    if(! $returnComponents) {
                        $this->parent[$neighbor] = $parent;
                        $valid &= $this->dfs($adjList, $neighbor, $setDepth, $depth + 1);
                    }
                    else {
                        $cycle = array_merge($cycle, $this->dfs($adjList, $neighbor, $setDepth, $depth + 1, $returnComponents));
                    }
                }
                else if($parent != $neighbor && $this->state[$neighbor] == static::GRAY) {
                    $valid = false;
                }
            }
        }
        $this->explored[$parent] = $this->time++;
        $this->state[$parent] = static::BLACK;
        if(! $returnComponents) {
            $this->stack[] = $parent;
            return $valid;
        }
        else {
            return $cycle;
        }
    }
    
    /**
     * This method will be responsible of finding the lowest possible vertex
     * Assuming that there's a single component ( not necessary traversed )
     */
    
    public function getLowestPossibleVertex() {
        $n = count($this->stack);
        $maxDepth = -1;
        $vertex = -1;
        for($i = $n - 1; $i >= 0; $i--) {
            $parent = $this->stack[$i];
            $dp[$parent] = 0;
//            dump($parent);
//            foreach($this->adjList[$parent] as $child) {
//                $this->dp[$parent] = max($this->dp[$parent], $this->dp[$child] + 1);
//                if($this->dp[$parent] > $maxDepth) {
//                    $maxDepth = $this->dp[$parent];
//                    $vertex = $parent;
//                }
//            }
        }
        die();
        return $vertex;
    }
    
    public function getAdjList() {
        return $this->adjList;
    }
    
    public function getTopoSort() {
        return $this->stack;
    }
    
    protected function isVertex($vertex) {
        return isset($this->vertices[$vertex]);
    }
    
    protected function setVertex($vertex) {
        $this->vertices[$vertex] = true;
    }
}
