<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\ArrayBuilder;
use JRC\binn\builders\ListKeyValueGenerator;

/**
 * @author jaredclemence
 */
class ListBuilder extends ArrayBuilder {
    public function make(){
        return parent::make();
    }

    protected function getKeyValueGenerator(): KeyValueByteGenerator {
        return new ListKeyValueGenerator();
    }

}
