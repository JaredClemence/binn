<?php

namespace JRC\binn\core;

use JRC\binn\core\BinnContainer;
use JRC\binn\core\BinnReader;
use JRC\binn\builders\NativeBuilder;

/**
 * This factory makes native PHP objects using a BinnContainer as the source
 *
 * @author jaredclemence
 */
class NativeFactory {

    public function read($byteString) {
        $binnContainer = $this->parseString($byteString);
        try{
            $count = $binnContainer->getCount();
            $data = $binnContainer->getData();
            $builder = $this->selectBuilder($binnContainer);
            $builder->read($count, $data);
            return $builder->make();
        }catch( \Exception $exception ){
            echo "\n\nUnable to process container:\n";
            $binnContainer->dumpHex();
            
            throw $exception;
        }
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
