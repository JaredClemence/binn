<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\ListKeyValueGenerator;
use JRC\binn\builders\MapKeyValueGenerator;
use JRC\binn\builders\ObjectKeyValueGenerator;
use JRC\binn\core\BinaryStringAtom;
use Faker\Factory;

require_once realpath(__DIR__ . '/../autoload.php');

/**
 * Description of KeyValueByteGeneratorTest
 *
 * @author jaredclemence
 */
class KeyValueByteGeneratorTest extends TestCase {

    /**
     * 
     * @param type $byteString
     * @param type $expectation
     * @dataProvider provideByteStringToIntCases
     */
    public function testIntegerConversion( $byteString, $expectation ){
        $int = $this->convertByteStringToBigInteger($byteString);
        $this->assertEquals( $expectation, $int );
    }
    
    public function provideByteStringToIntCases(){
        return [
            [hex2bin("00"),0],
            [hex2bin("01"),1],
            [hex2bin("FF"),255],
            [hex2bin("0100"),256],
            [hex2bin("0101"),257],
            [hex2bin("0102"),258],
            [hex2bin("01FF"),511],
            [hex2bin("01FFFFFFFF"),8589934591],
            [hex2bin("FFFFFFFFFF"),1099511627775],
        ];
    }
    
    
    /**
     * 
     * @param type $byteString
     * @param type $shouldFail
     * @dataProvider mapKeyProvider
     */
    public function testListKeys($byteString, $shouldFail) {
        $shouldFail = false; //the list keys never fail --- we are using the same provider for map keys however, so we overwrite this.
        $list = new TestList();
        $output = $list->_makeKey($byteString);
        $this->assertEquals("",$output, "The list keys should be empty strings in EVERY CASE!");
    }

    /**
     * 
     * @param type $byteString
     * @param type $shouldFail
     * @depends testIntegerConversion
     * @dataProvider mapKeyProvider
     */
    public function testMapKeys($byteString, $shouldFail) {
        $list = new TestMap();
        $error = null; $output = null; $numberValue = null;
        try {
            $numberValue = $this->convertByteStringToBigInteger( $byteString );
            $output = $list->_makeKey($numberValue);
        } catch (\Exception $e) {
            $error = $e;
        }
        if( $shouldFail == true ){
            $this->assertNotNull( $error, "The MAP generator should produce an error for this key string." );
        }else{
            $this->assertEquals( 4, strlen( $output ), "The output is always 4 bytes long." );
            $this->assertEquals($numberValue, $this->convertByteStringToBigInteger($output), "The numeric value of the key should match the provided index.");
            $this->assertNull( $error, "The MAP generator should NOT produce an error for this key string." );
        }
    }
    
    public function mapKeyProvider(){
        $testValues = $this->provideByteStringToIntCases();
        foreach( $testValues as &$case ){
            if( strlen( $case[0] ) <= 4 ){
                while( strlen( $case[0] ) < 4 ){
                    $case[0] = "\x00" . $case[0];
                }
                $case[1] = false;
            }else{
                $case[1] = true;
            }
        }
        return $testValues;
    }

    /**
     * @param type $keyValue
     * @param type $expectation
     * @param type $shouldFail
     * @dataProvider objectKeyTestCases
     */
    public function testObjectKeys($keyValue, $expectation, $shouldFail) {
        $list = new TestObject();
        $error = null; $output = null;
        try {
            $output = $list->_makeKey($keyValue);
        } catch (\Exception $e) {
            $error = $e;
        }
        if( $shouldFail == true ){
            $this->assertNotNull( $error, "The OBJECT generator should produce an error for this key string." );
        }else{
            $this->assertEquals($expectation, $output, "The key string should match expectation.");
            $this->assertNull( $error, "The OBJECT generator should NOT produce an error for this key string." );
        }
    }
    
    public function objectKeyTestCases(){
        $faker = Factory::create();
        $longKey = "";
        while(strlen( $longKey ) < 255 ){
            $longKey .= $faker->randomLetter;
        }
        return [
            [ "", null, true ],
            [ "short_key", "\x09short_key", false ],
            [ $longKey, "\xFF$longKey", false ],
            [ $longKey . "a", null, true ]
        ];
    }
    
    /**
     * Test for bug correction in external system.
     * 
     * Current alpha version has following problem in external system:
     * 
     * Substring used is: 0b 61 70 74 5f 63 6f 6e 74 61 63 74 e0 03 00 13 61 70 74 5f 63 6f 6e
     * The relevant portion is: 0b 61 70 74 5f 63 6f 6e 74 61 63 74 e0 03 00,
     * but the null byte 00 in e0 03 00 is being converted to 80, which is creating a 
     * binn container that has the count of 80 13 61 70. Naturally, there is no data to support this large count, and 
     * an "infinite" loop is created from which the reader cannot recover.
     * 
     * In this test, we must assert that this resolves without altering the byte.
     */
    public function testObjectKeyFailureCase1(){
        $substring = "0b 61 70 74 5f 63 6f 6e 74 61 63 74 e0 03 00 13 61 70 74 5f 63 6f 6e";
        $asciiHex = str_replace(" ", "", $substring);
        $binary = hex2bin($asciiHex);
        $this->assertEquals( $substring, BinaryStringAtom::createHumanReadableHexRepresentation($binary), "The test starts with a correct binary string representation." );
        $objectGenerator = new TestObject();
        $nextPair = $objectGenerator->readNextKeyValuePair($binary);
        /* @var $nextPair ContainerElement */
        $this->assertEquals( "0b 61 70 74 5f 63 6f 6e 74 61 63 74", BinaryStringAtom::createHumanReadableHexRepresentation($nextPair->keyBytes.""), "The next key is correctly identified" );
        $this->assertEquals( "e0 03 00", BinaryStringAtom::createHumanReadableHexRepresentation($nextPair->dataBytes.""), "The next data string is correctly identified without modification." );
    }

    private function convertByteStringToBigInteger($byteString) {
        return hexdec( bin2hex( $byteString ) );
    }

}

/**
 * Exposes protected methods for testing.
 */
class TestList extends ListKeyValueGenerator {

    public function _makeKey($key) {
        return $this->makeKeyString($key);
    }

}

/**
 * Exposes protected methods for testing.
 */
class TestMap extends MapKeyValueGenerator {

    public function _makeKey($key) {
        return $this->makeKeyString($key);
    }

}

/**
 * Exposes protected methods for testing.
 */
class TestObject extends ObjectKeyValueGenerator {

    public function _makeKey($key) {
        return $this->makeKeyString($key);
    }

}
