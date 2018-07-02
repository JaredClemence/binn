<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\DoubleBuilder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of DoubleBuilderTest
 *
 * @author jaredclemence
 */
class DoubleBuilderTest extends TestCase {
    /**
     * @dataProvider provideTestCases
     */
    public function testRead($count, $data, $expectedDecimalValue){
        $builder = new DoubleBuilder();
        $builder->read( $count, $data );
        $value = $builder->make();
        $this->assertEquals( $expectedDecimalValue, $value, "The float builder reads an unexpected value." );
    }
    public function provideTestCases(){
        return [
            [0,"\xBF\x07\x3D\x5C\x03\xAF\xC0\x07",-4.432618152350190000000000000000000000000000000E-05],
            [0,"\x58\xA7\x3D\x5C\x03\xAF\xC0\x07",1.1720834858738245E+119],
        ];
    }
}
