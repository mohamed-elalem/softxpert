<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TaskTrackBundle\Filters;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Description of Factory
 *
 * @author mohamedelalem
 */
class Factory {
    public function getInstance($id) {
        
        if($id == "user_id") {
            return new FilterByUser();
        }
        else if($id == "duration") {
            return new FilterByDuration();
        }
        else if($id == "description") {
            return new FilterByDescription();
        }
        else if($id == "created_at") {
            return new FilterByCreationDate();
        }
        else if($id == "updated_at") {
            return new FilterByUpdateDate();
        }
        else if($id == "title") {
            return new FilterByTitle();
        }
        else if($id == "seconds") {
            return new FilterBySeconds();
        }
        else if($id == "score") {
            return new FilterByScore();
        }
        else if($id == "done") {
            return new FilterByDone();
        }
        else if($id == "supervisor") {
            return new FilterBySupervisor();
        }
        else if($id == "challenge") {
            return new FilterByChallenge();
        }
        else if($id == "name") {
            return new FilterByName();
        }
        else if($id == "username") {
            return new FilterByUsername();
        }
        else if($id == "email") {
            return new FilterByEmail();
        }
        else if($id == "role") {
            return new FilterByRole();
        }
        else if($id == "user") {
            return new FilterByUser();
        }
        else if($id == "in_progress") {
            return new FilterByInProgress();
        }
        else {
            throw new Exception("Invalid filter id provided => '$id'");
        }
    }
    
    public function getFilters($parameters, $filter = null) {
        if(is_null($filter)) {
            $filter = new EntityConcrete;
        }
        foreach($parameters as $key => $value) {
            if($key[0] == $key[1] && $key[1] == "_") {
                $colName = substr($key, 2);
                $colMinValue = $parameters[$colName . "_min"];
                $colMaxValue = $parameters[$colName . "_max"];
                $filter = $this->getInstance($colName)->initBetween($colMinValue, $colMaxValue, $filter);
            }
            else if($key[0] == "_") {
                $colName = substr($key, 1);
                $colValue = $parameters[$colName];
                $filter = $this->getInstance($colName)->init($colValue, $filter);
            
            }
        }
        return $filter;
    }
}
