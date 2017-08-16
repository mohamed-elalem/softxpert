<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\DisjointSets;

/**
 * Description of newPHPClass
 *
 * @author mohamedelalem
 */
class UnionFindDisjointSet {
    private $p, $rank;
    
    public function init($set) {
        foreach($set as $src => $dest) {
            if(! isset($p[$src])) {
                $p[$src] = $src;
                $rank[$src] = 0;
            }
            if(! isset($p[$dest])) {
                $p[$dest] = $dest;
                $rank[$dest] = 0;
            }
            $this->union($src, $dest);
        }
    }
    
    public function findSet($i) {
        if($p[i] == $i) {
            return $i;
        }
        $p[$i] = $this->findSet($p[$i]);
        return $p[$i];
    }
    
    public function isSameSet($i, $j) {
        return $this->findSet($i) === $this->findSet($j);
    }
    
    public function union($i, $j) {
        if(! $this->isSameSet($i, $j)) {
            $x = $this->findSet($i);
            $j = $this->findSet($j);
            
            if($this->rank[$x] > $this->rank[$y]) {
                $this->p[$y] = x;
            }
            else {
                $this->p[$x] = $y;
                if($this->rank[$x] == $this->rank[$y]) {
                    $this->rank[$y]++;
                }
            }
            return true;
        }
        return false;
    }
}
