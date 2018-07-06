<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NativeBuilder;
use JRC\binn\core\Size;
/**
 * Description of TextBuilder
 *
 * @author jaredclemence
 */
class TextBuilder extends NativeBuilder {
    public function make(){
        $data = $this->getData();
        $string = $this->convertDataToString($data);
        return $string;
    }

    public function convertDataToString($data) {
        $string = $data;
        $lastCharIndex = strlen( $data ) - 1;
        $lastChar = $data[ $lastCharIndex ];
        if( ord($lastChar) == 0 ){
            $string = substr( $data, 0, $lastCharIndex );
        }
        return $string;
    }
    
    public function write($subtype, $nativeString) {
        $sizeBytes = $this->determineSizeBytes( $nativeString );
        $nullByte = "\x00";
        $binnContainer = $subtype . $sizeBytes . $nativeString . $nullByte;
        return $binnContainer;
    }

    private function determineSizeBytes($nativeString) {
        $sizeValue = strlen( $nativeString );
        $size = new Size();
        $size->setValue( $sizeValue );
        $sizeBytes = $size->getByteString();
        return $sizeBytes;
    }

}
