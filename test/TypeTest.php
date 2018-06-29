<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\StorageType;
use JRC\binn\Type;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of TypeTest
 *
 * @author jaredclemence
 */
class TypeTest extends TestCase{
    /**
     * 
     * @param type $expectedString
     * @dataProvider provideTestCases
     */
    public function testBinaryString( $expectedString ){
        $type = new Type();
        $type->setByteString($expectedString);
        $this->assertEquals( $expectedString, $type->getByteString(), "The byte string matches the input" );
    }
    public function provideTestCases(){
        return [
            "Blob"=>[StorageType::BLOB],
            "Byte"=>[StorageType::BYTE],
            "Container"=>[StorageType::CONTAINER],
            "Dword"=>[StorageType::DWORD],
            "Object (container)"=>[StorageType::CONTAINER | "\x02"],
        ];
    }
    /**
     * 
     * @param type $typeString
     * @param type $expectedLength
     * @dataProvider provideDefaultByteLengthCases
     */
    public function testDefaultByteLength( $typeString, $expectedLength ){
        $type = new Type();
        $type->setByteString($typeString);
        $this->assertEquals( $expectedLength, $type->getDefaultDataByteLength(), "The type returns the correct default length for the storage type." );
    }
    public function provideDefaultByteLengthCases(){
        return [
            "Blob"=>[StorageType::BLOB, -1],
            "Byte"=>[StorageType::BYTE, 1],
            "Container"=>[StorageType::CONTAINER, -1],
            "Dword"=>[StorageType::DWORD, 4],
            "NOBYTES"=>[StorageType::NOBYTES, 0],
            "QWORD"=>[StorageType::QWORD, 8],
            "STRING"=>[StorageType::STRING, -1],
            "WORD"=>[StorageType::WORD, 2],
        ];
    }
}
