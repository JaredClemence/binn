<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;

/**
 * BooleanBuilder provides values for True and False BinnContainers.
 *
 * @author jaredclemence
 */
class BooleanBuilder extends FixedResponseBuilder {
    public function __construct( $boolResult ) {
        $this->setFixedResponse( $boolResult );
    }
}
