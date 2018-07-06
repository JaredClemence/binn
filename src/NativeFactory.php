<?php

namespace JRC\binn;

use JRC\binn\BinnContainer;
use JRC\binn\BinnReader;
use JRC\binn\builders\NativeBuilder;

/**
 * This factory makes native PHP objects using a BinnContainer as the source
 *
 * @author jaredclemence
 */
class NativeFactory {

    public function read($byteString) {
        $binnContainer = $this->parseString($byteString);
        $count = $binnContainer->getCount();
        $data = $binnContainer->getData();
        $builder = $this->selectBuilder($binnContainer);
        $builder->read($count, $data);
        return $builder->make();
    }

    public function parseString($byteString): BinnContainer {
        $reader = new BinnReader();
        $container = $reader->read($byteString);
        return $container;
    }

    public function selectBuilder(BinnContainer $binnContainer) : NativeBuilder {
        $type = $binnContainer->getType();
        return $this->selectBuilderByRegisterredSubtype($type);
    }
    
    public function selectBuilderByRegisterredSubtype( $type ){
        $builder = NativeBuilder::getRegisteredBuilder($type);
        return $builder;
    }

}
