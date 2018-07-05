<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NativeBuilder;
use JRC\binn\NativeFactory;
use JRC\binn\BinnReader;
use JRC\binn\BinaryStringAtom;
use JRC\binn\Count;

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
            list( $nextDataString, $lastPosition ) = $this->extractNextDataString( $data, $lastPosition );
            $value = $this->convertContainerStringToNativeElement( $nextDataString );
            $this->addElementAtKey( $object, $key, $value );
        }
        return $object;
    }
    
    abstract protected function extractKey($data, $lastPosition);

    abstract protected function addElementAtKey( &$object, $key, $value);

    abstract protected function createEmptyContainer();
    
    /**
     * Reads a 
     * It is assumed that the lastPosition points to the first byte of a type definition.
     */
    protected function extractNextDataString($data, $lastPosition){
        $substring = substr( $data, $lastPosition );
        $reader = new BinnReader();
        $container = $reader->readNext($substring);
        $substring = $container->getByteString();
        $substringLength = strlen( $substring );
        $nextIndex = $lastPosition + $substringLength;
        unset( $reader );
        unset( $container );
        unset( $substringLength );
        return [ $substring, $nextIndex ];
    }

    private function convertContainerStringToNativeElement($nextDataString) {
        $factory = new NativeFactory();
        $value = $factory->read( $nextDataString );
        return $value;
    }

}
