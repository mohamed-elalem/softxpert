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

    /**
     * Initializes the Adjacency List and its transpose along with useful information that may be used in the future.
     * Extra information stored...
     * inward -> In-degree of a vertex
     * discovered -> the start time of a vertex exploration
     * explored -> the end time of a vertex exploration
     * state -> The current state of a vertex (white -> untouched, gray -> visited, black -> finished)
     * @param type $edgeList
     */
    
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
    
    /**
     * Checks whether there's a cycle or not in the provided graph
     * by performing a single depth first search 
     * @return boolean
     */

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
    
    /**
     * Another Depth first search used to get the strongly connected components
     * by traversing the transpose of the original adjacency list
     * @param 2D array $adjList
     * @param integer $parent
     * @return 2D array
     */
    
    public function getStronglyConnectedComponents($adjList, $parent) {
        // Calling of discovery time of a vertex 
        $this->discovered[$parent] = $this->time++;
        $this->state[$parent] = static::GRAY;
        $cycle = [$parent];
        
        /**
         * Traversing all the neighbors of current parent
         */
        foreach($adjList[$parent] as $idx => $neighbor) {

            /**
             * Recursively build the cycle vector
             */
            if($this->state[$neighbor] == self::WHITE) {
                $cycle = array_merge($cycle, $this->getStronglyConnectedComponents($adjList, $neighbor));
            }

        }
        // Declaring that this node is fully explored
        $this->explored[$parent] = $this->time++;
        $this->state[$parent] = static::BLACK;
        
        
        return $cycle;
    }
    
    /**
     * Gets the invalid parts of the provided graph
     * by performing a single depth first search
     * and return independent components
     * @return 2D array
     */

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
