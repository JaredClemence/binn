<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NativeBuilder;
use JRC\binn\core\NativeFactory;
use JRC\binn\core\BinnReader;
use JRC\binn\core\Count;
use JRC\binn\core\Size;
use JRC\binn\core\BinnFactory;

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
    
    public function write($subtype, $nativeData) {
        $countByte = $this->getCountByte( $nativeData );
        $binnData = $this->createBinnDataStringForContainerType( $nativeData );
        $sizeByte = $this->getSizeByte( $countByte, $binnData );
        $binnContainer = $subtype . $sizeByte . $countByte . $binnData;
        return $binnContainer;
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
    
    private function getCountByte( $mixedData ){
        $countValue = $this->getElementCount( $mixedData );
        $count = new Count(1);
        $count->setValue( $countValue );
        return $count->getByteString();
    }
    
    private function getSizeByte( $countByte, $binnData ){
        $assumedSizeByteLength = 1;
        $sizeByte = $this->getSizeValue($countByte, $binnData, $assumedSizeByteLength);
        if( strlen( $sizeByte ) > 1 ){
            $actualSizeByteLength = strlen( $sizeByte );
            $sizeByte = $this->getSizeValue($countByte, $binnData, $actualSizeByteLength);
        }
        return $sizeByte;
    }
    
    private function getSizeValue( $countByte, $binnData, $sizeLength = 1 ){
        $typeLength = 1;
        $countLength = strlen( $countByte );
        $dataLength = strlen( $binnData );
        
        $estimatedSize = $typeLength + $sizeLength + $countLength + $dataLength;
        
        $size = new Size();
        $size->setValue($estimatedSize);
        return $size->getByteString();
    }
    
    
    private function getElementCount( $mixedData ){
        if( is_array( $mixedData ) ){
            return count( $mixedData );
        }
        else if( is_object( $mixedData ) ){
            $objVars = get_object_vars($mixedData);
            return count( $objVars );
        }
        //Error
    }
    
    abstract protected function convertKeyToKeyByteString( $key );
    abstract protected function getDataFromObject( $mixed, $key );
    abstract protected function getOrderedKeyArray( $mixed );


    private function createBinnDataStringForContainerType( $nativeData ){
        $dataByteString = "";
        $keys = $this->getOrderedKeyArray($nativeData);
        foreach( $keys as $key ){
            $value = $this->getDataFromObject( $nativeData, $key );
            $keyString = $this->convertKeyToKeyByteString( $key );
            $dataString = $this->convertDataToDataString( $value );
            $elementString = $keyString . $dataString;
            $dataByteString .= $elementString;
        }
        return $dataByteString;
    }
    
    private function convertDataToDataString( $value ){
        $factory = new BinnFactory();
        $binnContainer = $factory->blindWrite($value);
        unset( $factory );
        return $binnContainer;
    }
}
