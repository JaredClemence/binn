<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\NativeFactory;

require_once realpath(__DIR__ . '/../autoload.php');

/**
 * Description of NativeFactoryTest
 *
 * @author jaredclemence
 */
class NativeFactoryTest extends TestCase {

    private $factory;

    public function setUp() {
        $this->factory = new NativeFactory();
    }

    public function provideNoByteTestCases() {
        return [
            "Null" => ["\x00", null],
            "True" => ["\x01", true],
            "False" => ["\x02", false],
        ];
    }
    
    /**
     * @param type $byteStringSource
     * @param type $expectedResult
     * @dataProvider provideNoByteTestCases
     */
    public function testNoByteConversion($byteStringSource, $expectedResult) {
        $result = $this->factory->read($byteStringSource);
        $this->assertEquals($expectedResult, $result);
    }

    public function provideOneByteTestCases() {
        return [
            "UInt8 Test Positive" => ["\x20\x6B", 107],
            "Int8 Test Negative" => ["\x21\xEB", -21],
            "Int8 Test Positive" => ["\x21\x6B", 107],
        ];
    }
    /**
     * @param type $byteStringSource
     * @param type $expectedResult
     * @dataProvider provideOneByteTestCases
     */
    public function testOneByteConversions($byteStringSource, $expectedResult) {
        $result = $this->factory->read($byteStringSource);
        $this->assertEquals($expectedResult, $result);
    }

    public function provideTwoByteTestCases() {
        return [
            "UInt16"        => ["\x40\x4A\xEB", 19179],
            "Int16 Test 0"  => ["\x41\xCA\xEB", -13589],
            "Int16 Test 1"  => ["\x41\x4A\xEB", 19179],
            "Int16 Test 2"  => ["\x41\xfe\x38", -456], //test taken from specification example
        ];
    }

    /**
     * @param type $byteStringSource
     * @param type $expectedResult
     * @dataProvider provideTwoByteTestCases
     */
    public function testTwoByteConversions($byteStringSource, $expectedResult) {
        $result = $this->factory->read($byteStringSource);
        $this->assertEquals($expectedResult, $result);
    }

    public function provideFourByteTestCases() {
        return [
            "UInt32 Positive" => ["\x60\x22\xCF\x8A\xE8", 584026856],
            "Int32 Negative" => ["\x61\xA2\xCF\x8A\xE8", -1563456792],
            "Int32 Positive" => ["\x61\x22\xCF\x8A\xE8", 584026856],
            "Float Test 1" => ["\x62\xBA\xEF\xAA\xE0", -1.8285177648067500000000E-03],
            "Float Test 2" => ["\x62\x71\xEF\xAA\xE0", 2.3735517614953000000000E+30],
        ];
    }

    /**
     * @param type $byteStringSource
     * @param type $expectedResult
     * @dataProvider provideFourByteTestCases
     */
    public function testFourByteConversions($byteStringSource, $expectedResult) {
        $result = $this->factory->read($byteStringSource);
        $this->assertEquals($expectedResult, $result);
    }

    public function provideEightByteTestCases() {
        return [
            "UInt64"        =>["\x80\x7C\xE6\x6C\x50\xE2\x84\x00\x00", 9000000000000000000],
            "Int64 Test 1"  =>["\x81\xFC\xE6\x6C\x50\xE2\x84\x00\x00", -223372036854775808],
            "Int64 Test 2"  =>["\x81\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF", -1],
            "Int64 Test 3"  =>["\x81\x00\x00\x00\x00\x00\x00\x00\x00", 0],
            "Double Test 1" =>["\x82\xBF\x07\x3D\x5C\x03\xAF\xC0\x07", -4.432618152350190000000000000000000000000000000E-05],
            "Double Test 2" =>["\x82\x58\xA7\x3D\x5C\x03\xAF\xC0\x07", 1.1720834858738245E+119],
        ];
    }

    /**
     * @param type $byteStringSource
     * @param type $expectedResult
     * @dataProvider provideEightByteTestCases
     */
     public function testEightByteConversions($byteStringSource, $expectedResult) {
        $result = $this->factory->read($byteStringSource);
        $this->assertEquals($expectedResult, $result);
    }
    
    public function provideContainerConversions(){
        return [
            "LIST 1" =>["\xE0\x09\x02\xA0\x01a\x00\x20\x7B",["a",123]],
            "MAP 1" => ["\xE1\x11\x02\x00\x00\x00\x02\xA0\x01a\x00\x00\x00\x00\x05\x20\x7B",[2=>"a",5=>123]],
            "OBJECT"=> ["\xE2\x28\x03\x0Bjargonizing\x41\xFE\x38\x05jumpy\x40\x03\x15\x0Aschnozzles\x20\x7B", json_decode("{\"jargonizing\":-456,\"jumpy\":789,\"schnozzles\":123}")]
        ];
    }
    
    /**
     * @dataProvider provideContainerConversions
     */
    public function testContainerConversion($byteStringSource, $expectedResult ){
        $result = $this->factory->read($byteStringSource);
        $this->assertEquals($expectedResult, $result);
    }

}
