<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\FloatBuilder;
use JRC\binn\core\NativeFactory;
use JRC\binn\core\BinaryStringAtom;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of DecimalBuilderTest
 *
 * @author jaredclemence
 */
class DecimalBuilderTest extends TestCase {
    /**
     * 
     * @param type $subtype
     * @param type $value
     * @param type $hexResult
     * @dataProvider provideWriteTestCases
     */
    public function testWrite( $subtype, $value, $hexResult ){
        $factory = new NativeFactory();
        $builder = $factory->selectBuilderByRegisterredSubtype($subtype);
        $result = $builder->write( $subtype, $value );
        $result = BinaryStringAtom::createHumanReadableHexRepresentation($result);
        $hexResult = BinaryStringAtom::createHumanReadableHexRepresentation($hexResult);
        $this->assertEquals( $hexResult, $result );
    }
    public function provideWriteTestCases(){
        return [
//            "DOUBLE (1)"=>["\x82",195023E10,"\x82\x43\x1b\xb6\xe5\x39\x85\x70\x00"],
        //    "DOUBLE (2)"=>["\x82",-4.432618152350190000000000000000000000000000000E-05,"\xBF\x07\x3D\x5C\x03\xAF\xC0\x07"],
            "FLOAT (1)"=>["\x62",1950230027173888,"\x62\x58\xdd\xb7\x2a"],
//            "FLOAT (2)"=>["\x62",-4.432618152350190000000000000000000000000000000E-05, "\x62\xB8\x39\xEA\xE0"],
        ];
    }
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
        $expectedString = BinaryStringAtom::createHumanReadableBinaryRepresentation($expectedMask);
        $maskString = BinaryStringAtom::createHumanReadableBinaryRepresentation($mask);
        $this->assertEquals( $expectedString, $maskString, "The front sided mask generator failed to produce the expected result." );
    }
    public function provideFrontSideMaskCases(){
        return [
            [0,1,"\x00"],
            [4,1,"\xF0"],
            [8,1,"\xFF"],
            [10,2,"\xFF\xC0"],
            [12,2,"\xFF\xF0"],
            [12,4,"\xFF\xF0\x00\x00"],
            [9,4,"\xFF\x80\x00\x00"],
        ];
    }
    
    /**
     * @dataProvider provideMantissaTestCases
     */
    public function testExtractMantissa( $data, $expectedMantissa ){
        $builder = new FloatBuilder();
        $mantissa = $builder->extractMantissa( $data );
        $this->assertEquals(BinaryStringAtom::createHumanReadableHexRepresentation($expectedMantissa),BinaryStringAtom::createHumanReadableHexRepresentation($mantissa));
    }
    public function provideMantissaTestCases(){
        return [
            ["\xBA\xEF\xAA\xE0","\x00\x6F\xAA\xE0"]
        ];
    }
}
