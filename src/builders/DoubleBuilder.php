<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\DecimalBuilder;
/**
 * Description of DoubleBuilder
 *
 * @author jaredclemence
 */
class DoubleBuilder extends DecimalBuilder {
    public function __construct() {
        parent::__construct(8);
    }
}
