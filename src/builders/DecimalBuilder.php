<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\NumericBuilder;
/**
 * Description of DecimalBuilder
 *
 * @author jaredclemence
 */
class DecimalBuilder extends NumericBuilder {
    private $signBitLength;
    private $exponentBitLength;
    private $mantissaBitLength;
    
    public function __construct( $bytes ){}
}
