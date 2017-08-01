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
    protected $discovered;
    protected $explored;
    private $time;
    protected $state;
    protected $stack;
    private $topoSort;
    protected $depth;
    protected $parent;
    private $vertices;
    protected $inward;
    
    /**
     * State colors
     */
    
    const WHITE = 0;
    const GRAY = 1;
    const BLACK = 2;
    
    public function initialize() {
        $this->time = 0;
        $this->stack = [];
        $this->topoSort = [];
    }
    
    public abstract function setup($edgeList);
    
    public abstract function checkForCycles();
    public abstract function getCycles();
    
    /**
     * Reset stored information to perform additional operations
     */
    
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
     * and check whether this graph is cyclic or not.
     * @param matrix Adjacency matrix that represents the graph
     * @param integer $u
     * @return boolean true if the graph is valid | false if the graph is invalid
     */
    
    public function checkGraphValidity($adjList, $parent) {
        // Calling of discovery time of a vertex 
        $this->discovered[$parent] = $this->time++;
        $this->state[$parent] = static::GRAY;
        $valid = true;
        
        /**
         * Traversing all the neighbors of current parent
         */
        foreach($adjList[$parent] as $idx => $neighbor) {

            /**
             * if neighbor has never been visited before then we explore it
             * else if this neighbor is being explored and returned back to it then it's either of two cases:
             * first case is a back edge (direct) u -> v & v -> u this is an invalid state
             * second case is a back edge (cycle) u -> w & w -> v & v -> u this is an invalid state
             * for the curious wondering why not just else
             * there are other two cases forward edge and cross edge which they are totally legit
             */

            if($this->state[$neighbor] === self::WHITE) {
                $this->parent[$neighbor] = $parent;
                $valid &= $this->checkGraphValidity($adjList, $neighbor);
            }
            else if($this->state[$neighbor] === static::GRAY) {
                $valid = false;
                break;
            }

        }
        // Declaring that this node is fully explored
        $this->explored[$parent] = $this->time++;
        $this->state[$parent] = static::BLACK;
        
        // Incase of a cycle we store the vertices inside stack to get cycles
        $this->stack[] = $parent;
        
        return $valid;
    }
    
    public abstract function getStronglyConnectedComponents($adjList, $parent);
    
    /**
     * Generated topological sort favoring nearest tasks first (Kahn algorithm)
     * @param type $adjList
     */

    
    public function topologicalSort($adjList) {
        $q = new \SplQueue();
        
        /**
         * Inserting vertices with 0 in degree into queue
         */
        
        foreach($adjList as $parent => $children) {
            if($this->inward[$parent] == 0) {
                $q->enqueue($parent);
                $this->depth[$parent] = 0;
            }
        }
        
        
        while(! $q->isEmpty()) {
            /*
             * Getting the queue head and exploring its children
             */
            $u = $q->dequeue();
            $this->topoSort[] = $u;
            foreach($adjList[$u] as $v) {
                /*
                 * At each iteration decrease in degree each child with 1
                 * if it became 0 we consider it to explore next
                 */
                $this->inward[$v]--;
                if($this->inward[$v] == 0) {
                    $q->enqueue($v);
                    $this->depth[$v] = $this->depth[$u] + 1;
                    $this->parent[$v] = $u;
                }
            }
        }
    }
    
    public function getTopoSort() {
        return $this->topoSort;
    }
    
    public function getTaskPriority() {
        $priority = [];
        foreach($this->topoSort as $task) {
            $priority[] = [$task, $this->depth[$task]];
        }
        return $priority;
    }
    
    protected function isVertex($vertex) {
        return isset($this->vertices[$vertex]);
    }
    
    protected function setVertex($vertex) {
        $this->vertices[$vertex] = true;
    }
}
