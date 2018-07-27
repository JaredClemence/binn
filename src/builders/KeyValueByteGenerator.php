<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;

/**
 * Description of KeyValueByteGenerator
 *
 * @author jaredclemence
 */
abstract class KeyValueByteGenerator {
    /**
     * Do not use. Made public for testing purposes only.
     */
    abstract protected function makeKeyString($key) : string;
    
    public function generateByteString( $key, $value ) : string {
        $keyString = $this->makeKeyString( $key );
        $valueString = $this->makeValueString( $value );
        return $keyString . $valueString;
    }

    public function makeValueString($value) {
        
    }

}
