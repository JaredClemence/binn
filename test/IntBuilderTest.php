<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\IntBuilder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of IntBuilderTest
 *
 * @author jaredclemence
 */
class IntBuilderTest {
    /**
     * @param type $size
     * @param type $count
     * @param type $data
     * @param type $expectedValue
     * @dataProvider provideTestCases
     */
    public function testSignedInt( $size, $count, $data, $expectedValue ){
        $builder = new IntBuilder($size);
        $builder->read( $count, $data );
        $value = $builder->make();
        $this->assertEquals( $expectedValue, $value, "The value produced matches the expected value." );
    }
    public function provideTestCases(){
        return [
            [1,0,"\xEB",-21],
            [1,0,"\x6B",107],
            [2,0,"\xCA\xEB",-13589],
            [2,0,"\x4A\xEB",19179],
            [4,0,"\xA2\xCF\x8A\xE8",-1563456792],
            [4,0,"\x22\xCF\x8A\xE8",584026856],
            //Unable to maximize use of bit field due to average system limitations. 64-bit unsigned ints are not able to have all bits set. 9E18 is the max.
            [8,0,"\x7C\xE6\x6C\x50\xE2\x84\x00\x00", 9000000000000000000],
            [8,0,"\xFC\xE6\x6C\x50\xE2\x84\x00\x00", -223372036854775809],
            [8,0,"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF", 1],
            [8,0,"\x00\x00\x00\x00\x00\x00\x00\x00", 0],
        ];
    }
}
