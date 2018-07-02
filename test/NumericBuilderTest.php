<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\IntBuilder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of NumericBuilderTest
 *
 * @author jaredclemence
 */
class NumericBuilderTest extends TestCase {
    /**
     * 
     * @param type $byte
     * @param type $carry
     * @param type $expectedCarry
     * @param type $expectedByte
     * @dataProvider providerByteIncrementerTests
     */
    public function testByteIncrementerFunction( $byte, $carry, $expectedCarry, $expectedByte ){
        $builder = new IntBuilder(8);
        $resultByte = $builder->addOneToBytePreserveCarry($byte, $carry);
        $this->assertEquals($expectedCarry, $carry, "The carry bit did not resolve to the expected result.");
        $this->assertEquals(decbin( ord($expectedByte ) ), decbin( ord($resultByte ) ), "The byte returned matches the expectation." );
    }
    public function providerByteIncrementerTests(){
        return [
            ["\x00", 0, 0, "\x00"],
            ["\x00", 1, 0, "\x01"],
            ["\x0F", 0, 0, "\x0F"],
            ["\x0F", 1, 0, "\x10"],
            ["\xFD", 1, 0, "\xFE"],
            ["\xFF", 0, 0, "\xFF"],
            ["\xFF", 1, 1, "\x00"]
        ];
    }
    
    /**
     * 
     * @param type $byteString
     * @param type $expectedIsNegative
     * @dataProvider provideIsNegativeTestCases
     */
    public function testIsNegativeFunction( $byteString, $expectedIsNegative ){
        $builder = new IntBuilder(8);
        $isNegative = $builder->isNegative($byteString);
        $this->assertEquals( $expectedIsNegative, $isNegative, "The isNegative value does not match the expected value." );
    }
    public function provideIsNegativeTestCases(){
        return [
            ["\x88",true],
            ["\x48",false],
            ["\xB0\x58",true],
            ["\x30\x58",false],
            ["\xB2\x5E\x30\x58",true],
            ["\x72\x5E\x30\x58",false],
            ["\xDD\xBB\xEC\xB7\x72\x5E\x30\x58",true],
            ["\x5D\xBB\xEC\xB7\x72\x5E\x30\x58",false],
        ];
    }
}
