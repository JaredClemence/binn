<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\core\BinnFactory;
use JRC\binn\core\ContainerElement;
use JRC\binn\core\BinnReader;
use JRC\binn\core\KeyValue;

/**
 * Description of KeyValueByteGenerator
 *
 * @author jaredclemence
 */
abstract class KeyValueByteGenerator {

    private $counter;
    
    public function __construct() {
        $this->counter = -1;
    }

    /**
     * Do not use. Made public for testing purposes only.
     */
    abstract protected function makeKeyString($key) : string;
    
    public function generateByteString( $key, $value ) : string {
        $keyString = $this->makeKeyString( $key );
        $valueString = $this->makeValueString( $value );
        return $keyString . $valueString;
    }

    protected function makeValueString($value) {
        $factory = new BinnFactory();
        $binnContainer = $factory->blindWrite($value);
        unset($factory);
        return $binnContainer;
    }
    
    /**
     * This method receives a data string that is already truncated.
     * The expectation is that the first bytes will represent the key, then a series 
     * of bytes will represent the value. That value will end either at the beginning of the 
     * next data structure or the end of the data string.
     * @param type $truncatedString
     */
    public function readNextKeyValuePair( $truncatedString ) : ContainerElement {
        $keyBytes = $this->extractKeyBytes( $truncatedString );
        $remainingBytes = substr( $truncatedString, $keyBytes->getLength() );
        $data = $this->readData( $remainingBytes );
        return new ContainerElement($keyBytes, $data);
    }

    abstract protected function extractKeyBytes($truncatedString) : KeyValue;

    private function readData($remainingBytes) {
        $reader = new BinnReader();
        $nextContainer = $reader->read($remainingBytes);
        return $nextContainer;
    }

    protected function incrementCounter() {
        $this->counter++;
    }

    protected function getCurrentCounter() {
        return $this->counter;
    }

}
