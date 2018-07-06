<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\core\NativeFactory;
use JRC\binn\core\BinnFactory;
use JRC\binn\core\BinaryStringAtom;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Tests the conversion of native objects to BinnContainer strings.
 *
 * @author jaredclemence
 */
class BinnFactoryTest extends TestCase {
    /**
     * @param type $nativeElement
     * @param type $expectedByteString
     * @dataProvider provideWriteTestCases
     */
    public function testTheTestCases( $nativeElement, $expectedByteString ){
        $factory = new NativeFactory();
        $factoryResult = $factory->read($expectedByteString);
        $this->assertEquals( $nativeElement, $factoryResult, "The test case expectation does not produce the native element provided." );
    }
    /**
     * This tests the blind writing of native objects into BinnStrings.
     * 
     * Note, some native types require additional input from the user. For example, it is not possible to 
     * differentiate when a \DateTime object is being used as a DATETIME, DATE, or a TIME object. Furthermore, 
     * STRING and BLOB in PHP are both strings. FLOAT and DOUBLE are not differentiated. For these reasons, we 
     * we make certain assumptions in this base class. We only write DATETIME, STRING, and DOUBLE containers.
     * 
     * Additional functionality should be added in future versions so that classes can be written purposefully by 
     * adding specific types to a BinnContainer.
     * 
     * @param type $nativeElement
     * @param type $expectedByteString
     * @dataProvider provideWriteTestCases
     */
    public function testBlindWrite( $nativeElement, $expectedByteString ){
        $factory = new BinnFactory();
        $byteString = $factory->blindWrite($nativeElement);
        $humanReadableByteString = BinaryStringAtom::createHumanReadableHexRepresentation($byteString);
        $humanReadableExpectedString = BinaryStringAtom::createHumanReadableHexRepresentation($expectedByteString);
        $this->assertEquals( $humanReadableExpectedString, $humanReadableByteString, "The BinnFactory writes the expected deterministic output." );
    }
    public function provideWriteTestCases(){
        return [
            "NULL"=>[ null, "\x00" ],
            "TRUE"=>[ true, "\x01" ],
            "FALSE"=>[ false, "\x02" ],
            "UINT8 (1)"=>[ 0x01, "\x20\x01" ],
            "UINT8 (2)"=>[ 0xFF, "\x20\xFF" ],
            "INT8 (1)"=>[ -1 * 0x01, "\x21\xFF" ],
            "INT8 (2)"=>[ -1 * 0x7F, "\x21\x81" ],
            "UINT16"=>[ 0xFFFF, "\x40\xFF\xFF" ],
            "INT16"=>[ -1 * 0x7FFF, "\x41\x80\x01"],
            "UINT32"=>[ 0xFFFFFFFF, "\x60\xFF\xFF\xFF\xFF" ],
            "INT32"=>[ -1 * 0x7FFFFFFF, "\x61\x80\x00\x00\x01" ],
            
            //PHP does not differentiate between Float and Double, all Floats will be written as Doubles
            //"FLOAT"=>[-1.8285177648067500000000E-03, "\x62\xBA\xEF\xAA\xE0"],
            //unable to use all 64 bits to represent an integer.
            
            "UINT64"=>[ 0x0FFFFFFFFFFFFFFF, "\x80\x0F\xFF\xFF\xFF\xFF\xFF\xFF\xFF" ],
            "INT64"=>[ -1 * 0x7FFFFFFFFFFFFFFF, "\x81\x80\x00\x00\x00\x00\x00\x00\x01" ],
//            "DOUBLE"=>[-4.432618152350190000000000000000000000000000000E-05,"\x82\xBF\x07\x3D\x5C\x03\xAF\xC0\x07"],
            
            //In PHP Blobs and strings are the same, so we will represent all BLOBS as strings
            
//            "STRING"=>["This is a test.", "\xA0\x0EThis is a test.\x00"],
//            "DATETIME"=> [ new \DateTime("2000-01-01 15:23:00-08:00"), "\xA1\x192000-01-01 15:23:00-08:00\x00"],
//            "DECIMALSTRING"=>["1253.1234151234150981237412394012390481751243", "\xA4\x2D1253.1234151234150981237412394012390481751243\x00" ],
//            "LIST (EMPTY)"=> [[], "\xE0\x03\x00"],
//            "LIST"=>[["a",123], "\xE0\x09\x02\xA0\x01a\x00\x20\x7B"],
//            "MAP"=>[[2=>"a",5=>123], "\xE1\x11\x02\x00\x00\x00\x02\xA0\x01a\x00\x00\x00\x00\x05\x20\x7B"],
//            "OBJECT"=>[json_decode("{\"jargonizing\":-456,\"jumpy\":789,\"schnozzles\":123}"),"\xE2\x28\x03\x0Bjargonizing\x41\xFE\x38\x05jumpy\x40\x03\x15\x0Aschnozzles\x20\x7B"],
        ];
    }
}
