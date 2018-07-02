<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\FloatBuilder;

require_once realpath( __DIR__ . '/../autoload.php' );
/**
 * Description of FloatBuilderTest
 *
 * @author jaredclemence
 */
class FloatBuilderTest extends TestCase{
    /**
     * @dataProvider provideTestCases
     */
    public function testRead($count, $data, $expectedDecimalValue){
        $builder = new FloatBuilder();
        $builder->read( $count, $data );
        $value = $builder->make();
        $this->assertEquals( $expectedDecimalValue, $value, "The float builder reads an unexpected value." );
    }
    public function provideTestCases(){
        return [
            [0,"\xBA\xEF\xAA\xE0",-1.8285177648067500000000E-03],
            [0,"\x71\xEF\xAA\xE0",2.3735517614953000000000E+30],
        ];
    }
}
