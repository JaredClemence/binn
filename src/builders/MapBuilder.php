<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\ArrayBuilder;
use JRC\binn\core\BinnNumber;

/**
 * @author jaredclemence
 */
class MapBuilder extends ArrayBuilder {
    protected function extractKey($data, $lastPosition) {
        $substring = substr( $data, $lastPosition );
        $keyLength = 4;
        $keyString = substr( $substring, 0, $keyLength );
        $nextPosition = $lastPosition + $keyLength;
        $binnNumber = new BinnNumber();
        $binnNumber->setByteString($keyString);
        $key = $binnNumber->getValue();
        unset( $binnNumber );
        unset( $keyLength );
        unset( $keyString );
        unset( $substring );
        return [$key, $nextPosition];
    }

    protected function convertKeyToKeyByteString($key) {
        $byteString = chr( $key );
        while( strlen( $byteString ) < 4 ){
            $byteString = "\x00" . $byteString;
        }
        return $byteString;
    }

}
