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
class MapBuilder extends ArrayBuilder {
    
    /**
     * Each mapped element has a key and a value.
     *     key: big-endian DWORD (4 bytes)
     *     value: size depends on sub-type
     * 
     * @param type $data
     * @param type $lastPosition
     * @return array [ substring, indexToNextStartPosition ]
     */
    protected function extractNextDataString($data, $lastPosition) {
        
    }

    protected function extractKey($data, $lastPosition) {
        
    }

}
