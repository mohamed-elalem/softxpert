<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Graphs;

/**
 *
 * @author mohamedelalem
 */
interface StronglyConnectedComponents {
    public function run();
    public function getStronglyConnectedComponents();
}
