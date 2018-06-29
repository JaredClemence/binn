<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\FixedResponseBuilder;
/**
 * NullBuilder provides values for the NOBYTES null type.
 *
 * @author jaredclemence
 */
class NullBuilder extends FixedResponseBuilder {
    public function __construct() {
        $this->setFixedResponse( null );
    }
}
