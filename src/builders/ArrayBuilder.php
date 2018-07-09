<?php

namespace JRC\binn\builders;
use JRC\binn\builders\ContainerBuilder;
/**
 * ArrayBuilder reads and writes lists and map container types.
 *
 * @author jaredclemence
 */
abstract class ArrayBuilder extends ContainerBuilder {
    protected function addElementAtKey( &$object, $key, $value){
        $object[ $key ] = $value;
    }

    protected function createEmptyContainer(){
        return [];
    }
}
