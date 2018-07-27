<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;

use JRC\binn\builders\ContainerBuilder;
use JRC\binn\builders\KeyValueByteGenerator;
use JRC\binn\builders\ObjectKeyValueGenerator;

use Exception;

/**
 * Description of ObjectBuilder
 *
 * @author jaredclemence
 */
class ObjectBuilder extends ContainerBuilder {
    protected function addElementAtKey(&$object, $key, $value) {
        $object->{$key} = $value;
    }

    protected function createEmptyContainer() {
        return new \stdClass();
    }
    
    protected function getKeyValueGenerator() : KeyValueByteGenerator {
        return new ObjectKeyValueGenerator();
    }
}
