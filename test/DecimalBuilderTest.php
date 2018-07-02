<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\FloatBuilder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of DecimalBuilderTest
 *
 * @author jaredclemence
 */
class DecimalBuilderTest extends TestCase {
    /**
     * Floating point and decimal values have a natural error that occurs in conversion between bases.
     * On this test, we assert that the returned value is within a reasonable error of the expected value.
     * @dataProvider provideBinaryFractionsForConversion
     */
    public function testConvertBinaryFractionToDecimalFraction( $mantissaByteString, $exponentInt, $expectedDecimal ){
        $builder = new FloatBuilder();
        $decimal = $builder->convertBinaryFractionToDecimalFraction($mantissaByteString, $exponentInt);
        $error = abs( (( $expectedDecimal - $decimal ) * 100) / $expectedDecimal );
        $this->assertLessThan( 1e-6, $error, "The binary-fraction to decimal-fraction conversion produced an result that deviates from expectation by more than 1e-9 percent." );
    }
    public function provideBinaryFractionsForConversion(){
        return [
            ["\x20\x00\x00", -3, 0.15625],
            ["\x3E\x64\x5A",4,23.799],
        ];
    }
    /**
     * 
     * @param type $byteString
     * @param type $bitPosition
     * @dataProvider provideReadBitTestCases
     */
    public function testReadBit( $byteString, $bitPosition, $expectedValue ){
        $builder = new FloatBuilder();
        $bitValue = $builder->readBit($byteString, $bitPosition);
        $this->assertEquals( $expectedValue, $bitValue );
    }
    public function provideReadBitTestCases(){
        $byteString = "\x2E\x81\xCD\x26\x02\x2C\x4D\x69";
        return [
//            [$byteString, 0, 1],
//            [$byteString, 1, 0],
            [$byteString, 5, 1],
            [$byteString, 7, 0],
            [$byteString, 17, 0],
            [$byteString, 18, 1],
            [$byteString, 34, 1],
            [$byteString, 35, 0],
            [$byteString, 55, 1],
        ];
    }
    /**
     * @dataProvider provideFindByteNumberTestCases
     */
    public function testFindByteNumberForBitPosition( $bitPosition, $expectedValue ){
        $builder = new FloatBuilder();
        $bytePosition = $builder->findByteNumberForBitPosition($bitPosition);
        $this->assertEquals( $expectedValue, $bytePosition );
    }
    public function provideFindByteNumberTestCases(){
        return [
            [ 0, 0],
            [ 1, 0],
            [ 5, 0],
            [ 7, 0],
            [17, 2],
            [18, 2],
            [34, 4],
            [35, 4],
            [55, 6],
        ];
    }
    
    /**
     * @dataProvider provideFrontSideMaskCases
     */
    public function testFrontSideMask( $maskLength, $byteLength, $expectedMask ){
        $builder = new FloatBuilder();
        $mask = $builder->makeFrontSideMask($maskLength, $byteLength);
        $this->assertEquals( decbin( $expectedMask ), decbin( $mask ), "The front sided mask generator failed to produce the expected result." );
    }
    public function provideFrontSideMaskCases(){
        return [
            [0,1,"\x00"],
            [4,1,"\xF0"],
            [8,1,"\xFF"],
            [10,2,"\xFF\xC0"],
            [12,2,"\xFF\xF0"],
            [12,4,"\xFF\xF0\x00\x00"],
        ];
    }
}
