<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;
use JRC\binn\BinnNumber;
/**
 * Size and Count have the same Binary Settings. This class exists only to make the code more readable
 * by the specs.
 * 
 * Note: The size value includes the total count. This means that the type byte and the size byte itself count for 2, then the number of bytes that follow.
 * 
 * @author jaredclemence
 */
class Size extends BinnNumber {
}
