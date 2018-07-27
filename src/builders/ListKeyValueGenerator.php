<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\KeyValueByteGenerator;
use JRC\binn\core\KeyValue;
/**
 * Description of ListKeyValueGenerator
 *
 * @author jaredclemence
 */
class ListKeyValueGenerator extends KeyValueByteGenerator{
    protected function makeKeyString($key): string {
        //lists do not have keys. Just a series of well-formed values.
        return "";
    }

    protected function extractKeyBytes($truncatedString) : KeyValue {
        $this->incrementCounter();
        $curValue = $this->getCurrentCounter();
        $keyValue = new KeyValue("", "", $curValue);
        return $keyValue;
    }

}
