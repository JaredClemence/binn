<?php

use JRC\binn\builders\NativeBuilder;
use JRC\binn\builders\NullBuilder;
use JRC\binn\builders\BooleanBuilder;
use JRC\binn\builders\UnsignedIntBuilder;
use JRC\binn\builders\IntBuilder;
use JRC\binn\builders\DecimalBuilder;
use JRC\binn\builders\DateTimeBuilder;
use JRC\binn\builders\DoubleBuilder;
use JRC\binn\builders\FloatBuilder;
use JRC\binn\builders\TimeBuilder;
use JRC\binn\builders\ArrayBuilder;
use JRC\binn\builders\ObjectBuilder;
use JRC\binn\builders\TextBuilder;
use JRC\binn\builders\ListBuilder;
use JRC\binn\builders\MapBuilder;

if (!defined("NativeBuildersRegistered")) {
    define("NativeBuildersRegistered", 1);

    NativeBuilder::register("\x00", function(){ return new NullBuilder(); } );
    NativeBuilder::register("\x01", function(){ return new BooleanBuilder(true); });
    NativeBuilder::register("\x02", function(){ return new BooleanBuilder(false); });
    NativeBuilder::register("\x20", function(){ return new UnsignedIntBuilder(1); });
    NativeBuilder::register("\x21", function(){ return new IntBuilder(1); });
    NativeBuilder::register("\x40", function(){ return new UnsignedIntBuilder(2); });
    NativeBuilder::register("\x41", function(){ return new IntBuilder(2); });
    NativeBuilder::register("\x60", function(){ return new UnsignedIntBuilder(4); });
    NativeBuilder::register("\x61", function(){ return new IntBuilder(4); });
    NativeBuilder::register("\x62", function(){ return new FloatBuilder(); });
    NativeBuilder::register("\x80", function(){ return new UnsignedIntBuilder(8); });
    NativeBuilder::register("\x81", function(){ return new IntBuilder(8); });
    NativeBuilder::register("\x82", function(){ return new DoubleBuilder(); });
    NativeBuilder::register("\xA0", function(){ return new TextBuilder(); });
    NativeBuilder::register("\xA1", function(){ return new DateTimeBuilder(); } );
    NativeBuilder::register("\xA2", function(){ return new DateTimeBuilder(); } );
    NativeBuilder::register("\xA3", function(){ return new DateTimeBuilder(); } );
    NativeBuilder::register("\xA4", function(){ return new TextBuilder(); });
    NativeBuilder::register("\xC0", function(){ return new TextBuilder(); });
    NativeBuilder::register("\xE0", function(){ return new ListBuilder(); });
    NativeBuilder::register("\xE1", function(){ return new MapBuilder(); });
    NativeBuilder::register("\xE2", function(){ return new ObjectBuilder(); });
}
