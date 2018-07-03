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

if (!defined("NativeBuildersRegistered")) {
    define("NativeBuildersRegistered", 1);

    NativeBuilder::register("\x00", new NullBuilder());
    NativeBuilder::register("\x01", new BooleanBuilder(true));
    NativeBuilder::register("\x02", new BooleanBuilder(false));
    NativeBuilder::register("\x20", new UnsignedIntBuilder(1));
    NativeBuilder::register("\x21", new IntBuilder(1));
    NativeBuilder::register("\x40", new UnsignedIntBuilder(2));
    NativeBuilder::register("\x41", new IntBuilder(2));
    NativeBuilder::register("\x60", new UnsignedIntBuilder(4));
    NativeBuilder::register("\x61", new IntBuilder(4));
    NativeBuilder::register("\x62", new FloatBuilder());
    NativeBuilder::register("\x80", new UnsignedIntBuilder(8));
    NativeBuilder::register("\x81", new IntBuilder(8));
    NativeBuilder::register("\x82", new DoubleBuilder());
    NativeBuilder::register("\xA0", new TextBuilder());
    NativeBuilder::register("\xA1", new DateTimeBuilder());
    NativeBuilder::register("\xA2", new DateTimeBuilder());
    NativeBuilder::register("\xA3", new DateTimeBuilder());
    NativeBuilder::register("\xA4", new TextBuilder());
    NativeBuilder::register("\xC0", new TextBuilder());
    NativeBuilder::register("\xE0", new ArrayBuilder());
    NativeBuilder::register("\xE1", new ArrayBuilder());
    NativeBuilder::register("\xE2", new ObjectBuilder());
    
    //add custom objects here
}
