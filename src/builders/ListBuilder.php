<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\ArrayBuilder;
/**
 * @author jaredclemence
 */
class ListBuilder extends ArrayBuilder {
    /**
     * Each list element size depends on the sub-type only. (No keys)
     *     value: size depends on sub-type
     * 
     * @param type $data
     * @param type $lastPosition
     * @return array [ substring, indexToNextStartPosition ]
     */
    protected function extractKey($data, $lastPosition) {
        
    }

}
