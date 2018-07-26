<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\ArrayBuilder;
use JRC\binn\core\ObjectContainerKey;

/**
 * @author jaredclemence
 */
class ListBuilder extends ArrayBuilder {
    private $curKey;
    
    public function make(){
        $this->resetCurKey();
        return parent::make();
    }
    /**
     * Each list element size depends on the sub-type only. (No keys)
     *     value: size depends on sub-type
     * 
     * @param type $data
     * @param type $lastPosition
     * @return array [ substring, indexToNextStartPosition ]
     */
    protected function extractKey($data, $lastPosition) : ObjectContainerKey {
        $keyData = new ObjectContainerKey();
        
        $this->incrementCurKey();
        $nextPosition = $lastPosition;
        $keyData->setKey($this->getCurKey());
        return $keyData;
    }

    private function resetCurKey() {
        $this->curKey = -1;
    }

    private function incrementCurKey() {
        $this->curKey++;
    }

    private function getCurKey() {
        return $this->curKey;
    }

    protected function convertKeyToKeyByteString($key) {
        //list uses no keys
        return "";
    }

}
