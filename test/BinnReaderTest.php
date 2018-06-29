<?php


use PHPUnit\Framework\TestCase;

require_once realpath( __DIR__ . '/../autoload.php' );

use JRC\binn\BinnReader;
use JRC\binn\StorageType;
use JRC\binn\BinnContainer;
use JRC\binn\BinaryStringAtom;
/**
 * Description of BinnReaderTest
 *
 * @author jaredclemence
 */
class BinnReaderTest extends TestCase {
    /**
     * 
     * @param type $byteString
     * @param type $expectedContainer
     * @dataProvider provideTestCases
     */
    public function testStringParsing( $byteString, $expectedContainer ){
        $reader = new BinnReader();
        $container = $reader->read($byteString);
        $this->assertTypeEquals( $container, $expectedContainer );
        $this->assertSizeEquals( $container, $expectedContainer );
        $this->assertEquals( $expectedContainer, $container );
    }
    
    private function assertTypeEquals( $container, $expectedContainer ){
        $expectedByteRepresentation = BinaryStringAtom::createHumanReadableHexRepresentation($expectedContainer->type, 1);
        $containerByteRepresentation = BinaryStringAtom::createHumanReadableHexRepresentation($container->type, 1);
        $this->assertEquals( $expectedByteRepresentation, $containerByteRepresentation, "The type values of the container do not match." );
    }
    
    private function assertSizeEquals( $container, $expectedContainer ){
        $expectedByteRepresentation = BinaryStringAtom::createHumanReadableHexRepresentation($expectedContainer->size, 1);
        $containerByteRepresentation = BinaryStringAtom::createHumanReadableHexRepresentation($container->size, 1);
        $this->assertEquals( $expectedByteRepresentation, $containerByteRepresentation, "The size values of the container do not match." );
        $this->assertTrue( is_string( $container->size ), "The container has a byteString in the size variable." ); 
        $this->assertTrue( is_string( $expectedContainer->size ), "The expected container has a byteString in the size variable." ); 
    }
    
    public function provideTestCases(){
        return [
            "NULL"=> $this->makeTestCaseNull(),
            "Uint8"=>$this->makeTestCaseUint8(),
            "Uint16"=>$this->makeTestCaseUint16(),
            "Uint32"=>$this->makeTestCaseUint32(),
            "Uint64"=>$this->makeTestCaseUint64(),
            "Text"=>$this->makeTestCaseString(),
            "Blob"=>$this->makeTestCaseBlob(),
            "List"=>$this->makeTestCaseList(),
            "Map"=>$this->makeTestCaseMap(),
            "Object"=>$this->makeTestCaseObject()
        ];
    }

    private function makeTestCaseNull() {
        $container = new BinnContainer();
        $container->setType( StorageType::NOBYTES| "\x00"  );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

    private function makeTestCaseUint8() {
        $container = new BinnContainer();
        $container->setType( StorageType::BYTE| "\x00"  );
        $container->setData( hex2bin("FF") );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

    private function makeTestCaseUint16() {
        $container = new BinnContainer();
        $container->setType( StorageType::WORD | "\x00" );
        $container->setData( hex2bin("FED8") );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

    private function makeTestCaseUint32() {
        $container = new BinnContainer();
        $container->setType( StorageType::DWORD| "\x00"  );
        $container->setData( hex2bin("FED8AB01") );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

    private function makeTestCaseUint64() {
        $container = new BinnContainer();
        $container->setType( StorageType::DWORD| "\x00"  );
        $container->setData( hex2bin("FED8AB01FED8AB01") );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }
    
    private function makeTestCaseString(){
        $data = "pirate" . hex2bin("00");
        $container = new BinnContainer();
        $container->setType( StorageType::STRING | "\x00" );
        $container->setSize( 2 + strlen( $data ) ); 
        $container->setData( $data );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

    public function makeTestCaseBlob() {
        $data = "pirate" . hex2bin("00");
        $container = new BinnContainer();
        $container->setType( StorageType::BLOB | "\x00" );
        $container->setSize( 2 + strlen( $data ) ); 
        $container->setData( $data );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

    public function makeTestCaseList() {
        $data = hex2bin( "207b41fe38400315" );
        $container = new BinnContainer();
        $container->setType( StorageType::CONTAINER| "\x00"  );
        $container->setSize( hex2bin("0B") );
        $container->setCount( hex2bin("03"));
        $container->setData( $data );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

    public function makeTestCaseObject() {
        $data = hex2bin( "05" ) . "hello" . hex2bin( "a005") . "world" . hex2bin( "00" );
        $container = new BinnContainer();
        $container->setType( StorageType::CONTAINER| "\x02"  );
        $container->setSize( hex2bin("11") );
        $container->setCount( hex2bin("01"));
        $container->setData( $data );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

    public function makeTestCaseMap() {
        $data = hex2bin( "00000001a003" ) . "add" . hex2bin( "0000000002e0090241cfc7401a85");
        $container = new BinnContainer();
        $container->setType( StorageType::CONTAINER | "\x01" );
        $container->setSize( hex2bin("1A") );
        $container->setCount( hex2bin("02") );
        $container->setData( $data );
        $byteString = $container->getByteString();
        return [ $byteString, $container ];
    }

}
