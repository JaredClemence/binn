<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;

use JRC\binn\BinnContainer;
use JRC\binn\BinnReader;
use JRC\binn\builders\NativeBuilder;
use JRC\binn\Type;

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
        $builder = NativeBuilder::getRegisteredBuilder($type);
        return $builder;
    }

}
