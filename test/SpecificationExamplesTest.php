<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of SpecificationExamplesTest
 *
 * @author jaredclemence
 * @link https://github.com/liteserver/binn/blob/master/spec.md Specification that provides tests below
 */
class SpecificationExamplesTest extends TestCase {
    /**
     * 
     * @param type $byteString
     * @param type $expectedStringLength
     * @param type $expectedResult
     * @dataProvider provideTestCases
     */
    public function testReaderUsingSpecificationExamples( $byteString, $expectedStringLength, $expectedResult ){
        //this test cases function runs before the tests begin
        //first we verify that the byte string matches the expected length by the test specification
        $byteStringLength = strlen( $byteString );
        $this->assertEquals( $expectedStringLength, $byteStringLength, "The test case provides a byte string of an incorrect length. Bad test definition." );
    }
    public function provideTestCases() {
        return [
            "Simple json data" => $this->makeSimpleObjectData(),
            "A list of 3 integers" => $this->makeSimpleArrayData(),
            "A list inside a map" => $this->makeListInsideMapData(),
            "A list of objects" => $this->makeListOfObjects()
        ];
    }

    private function makeSimpleObjectData() {
        $parts = [
            hex2bin("e2"), // [type] object ( container )
            hex2bin("11"), // [size] container total size
            hex2bin("01"), // [count] key/value pairs
            hex2bin("05") . "hello", //key (appears to be a string length and a string
            hex2bin("A0"), // [type] = string
            hex2bin("05"), // [size]
            "world" . hex2bin("00")
        ];
        $byteString = $this->convertPartsToByteString($parts);
        $expectedSize = 17;
        $expectedResult = json_decode("{\"hello\":\"world\"}");
        return [$byteString, $expectedSize, $expectedResult];
    }

    private function makeSimpleArrayData() {
        $parts = [
            hex2bin("e0"), // [type] list (container)
            hex2bin("0b"), // [size] container total size
            hex2bin("03"), // [count] items
            hex2bin("20"), // [type] = uint8
            hex2bin("7B"), // [data] = (123)
            hex2bin("41"), // [type] = int16
            hex2bin("fe") . hex2bin("38"), // [data] (-456) [uses 2's compliment]
            hex2bin("40"), // [type] = uint16
            hex2bin("03") . hex2bin("15") // [data] (789)
        ];
        $byteString = $this->convertPartsToByteString($parts);
        $expectedSize = 11;
        $expectedResult = json_decode("[123,-456,789]");
        return [$byteString, $expectedSize, $expectedResult];
    }

    private function makeListInsideMapData() {
        $parts = [
            hex2bin("e1"), // [type] map (container)
            hex2bin("1a"), // [size] container total size
            hex2bin("02"), // [count] key/value pairs
            hex2bin("00") . hex2bin("00") . hex2bin("00") . hex2bin("01"), // key  (Note this is a test for a small size stored in a 4 byte string)
            hex2bin("a0"), // [type] = string
            hex2bin("03"), // [size]
            "add" . hex2bin("00"), // [data] (null terminated)
            hex2bin("00") . hex2bin("00") . hex2bin("00") . hex2bin("02"), // key  (Note this is a test for a small size stored in a 4 byte string)
            hex2bin("e0"), // [type] list (container)
            hex2bin("09"), // [size] container total size
            hex2bin("02"), // [count] items
            hex2bin("41"), // [type] = int16
            hex2bin("cf") . hex2bin("c7"), // [data] (-12345)
            hex2bin("40"), // [type] = uint16
            hex2bin( "1a" ) . hex2bin("85") // [data] (6789)
        ];
        $byteString = $this->convertPartsToByteString($parts);
        $expectedSize = 26;
        $expectedResult = [1 => "add", 2 => [-12345, 6789]];
        return [$byteString, $expectedSize, $expectedResult];
    }

    private function makeListOfObjects() {
        $parts = [
            hex2bin("e0"), // [type] list (container)
            hex2bin("2b"), // [size] container total size
            hex2bin("02"), // [count] items
            hex2bin("e2"), // [type] object (container)
            hex2bin("14"), // [size] container total size
            hex2bin("02"), // [count] key/value pairs
            hex2bin("02") . "id", // key
            hex2bin("20"), // [type] uint8
            hex2bin("01"), // [data] (1)
            hex2bin("04") . "name", //key
            hex2bin("a0"), // [type] = string
            hex2bin("04"), // [size]
            "John" . hex2bin("00"), // [data] (null terminated)
            hex2bin("e2"), // [type] = object
            hex2bin("14"), // [size] container total size
            hex2bin("02"), // [count] key/value pairs
            hex2bin("02") . "id", // key
            hex2bin("20"), // [type] = uint8
            hex2bin("02"), // [count] key/value pairs
            hex2bin("04") . "name", // key
            hex2bin("a0"), // [type] = string
            hex2bin("04"), // [size]
            "Eric" . hex2bin("00") // [data] (null terminated)
        ];
        $byteString = $this->convertPartsToByteString($parts);
        $expectedSize = 43;
        $expectedResult = json_decode("[ {\"id\": 1, \"name\": \"John\"}, {\"id\": 2, \"name\": \"Eric\"} ]");
        return [$byteString, $expectedSize, $expectedResult];
    }

    private function convertPartsToByteString($parts) {
        $byteString = "";
        foreach ($parts as $part) {
            $byteString .= $part;
        }
        return $byteString;
    }

}
