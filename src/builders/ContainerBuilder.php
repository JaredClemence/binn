<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NativeBuilder;
/**
 * Description of ContainerBuilder
 *
 * @author jaredclemence
 */
abstract class ContainerBuilder extends NativeBuilder {
    public function make(){
        $data = $this->getData();
        $count = $this->getCount();
        $lastPosition = 0;
        $object = $this->createEmptyContainer();
        for( $i = 0; $i < $count; $i++ ){
            list( $key, $lastPosition ) = $this->extractKey( $data, $lastPosition );
            list($nextDataString, $lastPosition) = $this->extractNextDataString( $data, $lastPosition );
            $value = $this->parseNextDataString( $nextDataString );
            $this->addElementAtKey( $object, $key, $value );
        }
    }
    
    abstract protected function extractKey($data, $lastPosition);

    abstract protected function addElementAtKey( &$object, $key, $value);

    abstract protected function createEmptyContainer();
    
    /**
     * Reads a 
     * It is assumed that the lastPosition points to the first byte of a type definition.
     */
    protected function extractNextDataString($data, $lastPosition){
        list( $typeString, $nextElement ) = $this->identifyType( $data, $lastPosition );
        return [ $substring, $nextIndex ];
    }

    protected function parseNextDataString($nextDataString) {
        
    }

}
