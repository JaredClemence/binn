<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\UnsignedIntBuilder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of UnsignedIntTest
 *
 * @author jaredclemence
 */
class UnsignedIntTest extends TestCase {
    /**
     * 
     * @param type $size
     * @param type $count
     * @param type $data
     * @param type $expectedValue
     * @dataProvider provideTestCases
     */
    public function testUnsignedInt( $size, $count, $data, $expectedValue ){
        $uint = new UnsignedIntBuilder($size);
        $uint->read( $count, $data );
        $value = $uint->make();
        $this->assertEquals( $expectedValue, $value, "The value produced matches the expected value." );
    }
    public function provideTestCases(){
        return [
            [1,0,"\xEB",235],
            [1,0,"\x7B",123], //test taken from specification example
            [2,0,"\xCA\xEB",51947],
            [2,0,"\x03\x15",789], //test taken from specification example
            [4,0,"\xA2\xCF\x8A\xE8",2731510504],
            //Unable to maximize use of bit field due to average system limitations. 64-bit unsigned ints are not able to have all bits set. 9E18 is the max.
            [8,0,"\x7C\xE6\x6C\x50\xE2\x84\x00\x00", 9000000000000000000],
        ];
    }
    public function testFailureOf64BitMax(){
        $data = "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF";
        $expectedValue = 18446744073709551615;
        $uint = new UnsignedIntBuilder(8);
        $uint->read( 0, $data );
        $value = $uint->make();
        $this->assertNotEquals( $expectedValue, $value, "A 64 bit unsigned integer should equal $expectedValue, but most systems are limited to 9e18. This test confirms that the limitation exists on any given system. If this test fails, then the system has the ability to utilize all bits of the 64-bit unsigned integer value." );
    }
}
