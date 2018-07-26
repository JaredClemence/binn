<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\ArrayBuilder;
use JRC\binn\core\BinnNumber;
use JRC\binn\core\ObjectContainerKey;

/**
 * @author jaredclemence
 */
class MapBuilder extends ArrayBuilder {
    protected function extractKey($data, $lastPosition) : ObjectContainerKey {
        $substring = substr( $data, $lastPosition );
        $keyValue = substr( $substring, 0, 4 );
        
        $keyData = new ObjectContainerKey();
        $keyData->setKeyValue($keyValue);
        
        $binnNumber = new BinnNumber();
        $binnNumber->setByteString($keyValue);
        $key = $binnNumber->getValue();
        $keyData->setKey( $key );
        
        unset( $binnNumber );
        unset( $keyLength );
        unset( $keyString );
        unset( $substring );
        
        return $keyData;
    }

    protected function convertKeyToKeyByteString($key) {
        $byteString = chr( $key );
        while( strlen( $byteString ) < 4 ){
            $byteString = "\x00" . $byteString;
        }
        return $byteString;
    }

}
