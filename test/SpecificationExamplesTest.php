<?php

use PHPUnit\Framework\TestCase;

require_once realpath( __DIR__ . '/../autoload.php' );

use JRC\binn\NativeFactory;

/**
 * This test implements the tests provided in the specification located at the link 
 * below.
 *
 * @author jaredclemence
 * @link https://github.com/liteserver/binn/blob/master/spec.md Specification 
 * that provides tests below
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
        
        $factory = new NativeFactory();
        $phpNativeObjects = $factory->read( $byteString );
        
        $this->assertEquals( $expectedResult, $phpNativeObjects, "The bin reader produces the expected object for the test case." );
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
            "\xe2", // [type] object ( container )
            "\x11", // [size] container total size
            "\x01", // [count] key/value pairs
            "\x05" . "hello", //key (appears to be a string length and a string
            "\xA0", // [type] = string
            "\x05", // [size]
            "world" . "\x00"
        ];
        $byteString = $this->convertPartsToByteString($parts);
        $expectedSize = 17;
        $expectedResult = json_decode("{\"hello\":\"world\"}");
        return [$byteString, $expectedSize, $expectedResult];
    }

    private function makeSimpleArrayData() {
        $parts = [
            "\xe0", // [type] list (container)
            "\x0b", // [size] container total size
            "\x03", // [count] items
            "\x20", // [type] = uint8
            "\x7B", // [data] = (123)
            "\x41", // [type] = int16
            "\xfe" . "\x38", // [data] (-456) [uses 2's compliment]
            "\x40", // [type] = uint16
            "\x03" . "\x15" // [data] (789)
        ];
        $byteString = $this->convertPartsToByteString($parts);
        $expectedSize = 11;
        $expectedResult = json_decode("[123,-456,789]");
        return [$byteString, $expectedSize, $expectedResult];
    }

    private function makeListInsideMapData() {
        $parts = [
            "\xe1", // [type] map (container)
            "\x1a", // [size] container total size
            "\x02", // [count] key/value pairs
            "\x00" . "\x00" . "\x00" . "\x01", // key  (Note this is a test for a small size stored in a 4 byte string)
            "\xa0", // [type] = string
            "\x03", // [size]
            "add" . "\x00", // [data] (null terminated)
            "\x00" . "\x00" . "\x00" . "\x02", // key  (Note this is a test for a small size stored in a 4 byte string)
            "\xe0", // [type] list (container)
            "\x09", // [size] container total size
            "\x02", // [count] items
            "\x41", // [type] = int16
            "\xcf" . "\xc7", // [data] (-12345)
            "\x40", // [type] = uint16
            "\x1a" . "\x85" // [data] (6789)
        ];
        $byteString = $this->convertPartsToByteString($parts);
        $expectedSize = 26;
        $expectedResult = [1 => "add", 2 => [-12345, 6789]];
        return [$byteString, $expectedSize, $expectedResult];
    }

    private function makeListOfObjects() {
        $parts = [
            "\xe0", // [type] list (container)
            "\x2b", // [size] container total size
            "\x02", // [count] items
            "\xe2", // [type] object (container)
            "\x14", // [size] container total size
            "\x02", // [count] key/value pairs
            "\x02" . "id", // key
            "\x20", // [type] uint8
            "\x01", // [data] (1)
            "\x04" . "name", //key
            "\xa0", // [type] = string
            "\x04", // [size]
            "John" . "\x00", // [data] (null terminated)
            "\xe2", // [type] = object
            "\x14", // [size] container total size
            "\x02", // [count] key/value pairs
            "\x02" . "id", // key
            "\x20", // [type] = uint8
            "\x02", // [count] key/value pairs
            "\x04" . "name", // key
            "\xa0", // [type] = string
            "\x04", // [size]
            "Eric" . "\x00" // [data] (null terminated)
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
