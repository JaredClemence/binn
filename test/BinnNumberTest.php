<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\BinnNumber;

require_once realpath( __DIR__ . '/../autoload.php' );
/**
 * Description of BinnNumberTest
 *
 * @author jaredclemence
 */
class BinnNumberTest extends TestCase {
    /**
     * 
     * @param type $byteString
     * @param type $size
     * @param type $expectedByteString
     * @dataProvider produceTestCases
     */
    public function testConversions( $byteString, $size, $expectedByteString ){
        $number = new BinnNumber();
        $number->setByteString($byteString);
        $this->assertEquals( $size, $number->getValue(), "The number value produced is the same as the one that is expected." );
        $this->assertEquals( $expectedByteString, $number->getByteString(), "The byte string produced is the same as the one that is expected." );
    }
    public function produceTestCases(){
        return [
            "No Size"=>["",0,""],
            "One-Byte Size"=>["\x5C", 92, "\x5C"],
            "Four-Byte Size"=>["\x80\x00\x01\xF4",500,"\x80\x00\x01\xF4"],
            "Four-Byte Small Size"=>["\x00\x00\x00\x5C",92,"\x5C"]
        ];
    }
}
