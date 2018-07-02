<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\DecimalBuilder;
/**
 * Description of FloatBuilder
 *
 * @author jaredclemence
 */
class FloatBuilder extends DecimalBuilder {
    public function __construct() {
        parent::__construct(4);
    }
}
