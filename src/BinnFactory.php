<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;
use JRC\binn\NativeFactory;
/**
 * Description of BinnFactory
 *
 * @author jaredclemence
 */
class BinnFactory {
    public function blindWrite( $data ){
        $subtype = $this->determineContainerSubType( $data );
        return $this->writeDataAsSubtype( $data, $subtype );
    }

    public function writeDataAsSubtype($data, $subtype) {
        $builder = $this->selectBuilderBySubtype( $subtype );
        $binnString = $builder->write( $subtype, $data );
        return $binnString;
    }

    private function selectBuilderBySubtype($subtype) {
        $factory = new NativeFactory();
        return $factory->selectBuilderBySubtype($subtype);
    }

}
