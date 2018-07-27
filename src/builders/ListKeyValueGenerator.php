<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\KeyValueByteGenerator;
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
}
