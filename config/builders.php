<?php

use JRC\binn\builders\NativeBuilder;
use JRC\binn\builders\NullBuilder;
use JRC\binn\builders\BooleanBuilder;
use JRC\binn\builders\UnsignedIntBuilder;
use JRC\binn\builders\IntBuilder;
use JRC\binn\builders\DecimalBuilder;
use JRC\binn\builders\TextBuilder;
use JRC\binn\builders\DateTimeBuilder;
use JRC\binn\builders\DateBuilder;
use JRC\binn\builders\TimeBuilder;
use JRC\binn\builders\ArrayBuilder;
use JRC\binn\builders\ObjectBuilder;

if (!defined("NativeBuildersRegistered")) {
    define("NativeBuildersRegistered", 1);

    NativeBuilder::register("\x00", new NullBuilder());
    NativeBuilder::register("\x01", new BooleanBuilder(true));
    NativeBuilder::register("\x02", new BooleanBuilder(false));
    NativeBuilder::register("\x20", new UnsignedIntBuilder());
    NativeBuilder::register("\x21", new IntBuilder());
    NativeBuilder::register("\x40", new UnsignedIntBuilder());
    NativeBuilder::register("\x41", new IntBuilder());
    NativeBuilder::register("\x60", new UnsignedIntBuilder());
    NativeBuilder::register("\x61", new IntBuilder());
    NativeBuilder::register("\x62", new DecimalBuilder());
    NativeBuilder::register("\x80", new UnsignedIntBuilder());
    NativeBuilder::register("\x81", new IntBuilder());
    NativeBuilder::register("\x82", new DecimalBuilder());
    NativeBuilder::register("\xA0", new TextBuilder());
    NativeBuilder::register("\xA1", new DateTimeBuilder());
    NativeBuilder::register("\xA2", new DateBuilder());
    NativeBuilder::register("\xA3", new TimeBuilder());
    NativeBuilder::register("\xA4", new TextBuilder());
    NativeBuilder::register("\xC0", new TextBuilder());
    NativeBuilder::register("\xE0", new ArrayBuilder());
    NativeBuilder::register("\xE1", new ArrayBuilder());
    NativeBuilder::register("\xE2", new ObjectBuilder());
    
    //add custom objects here
}
