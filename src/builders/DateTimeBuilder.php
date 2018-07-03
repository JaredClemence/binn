<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;
use JRC\binn\builders\TextBuilder;

/**
 * Description of DateTimeBuilder
 *
 * @author jaredclemence
 */
class DateTimeBuilder extends TextBuilder {
    public function make(){
        $timeString = parent::make();
        $dateTime = new \DateTime( $timeString );
        return $dateTime;
    }
}
