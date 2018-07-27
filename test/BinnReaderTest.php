<?php

use PHPUnit\Framework\TestCase;

require_once realpath(__DIR__ . '/../autoload.php');

use JRC\binn\core\BinnReader;
use JRC\binn\core\StorageType;
use JRC\binn\core\BinnContainer;
use JRC\binn\core\BinaryStringAtom;

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
    public function testStringParsing($byteString, $expectedContainer) {
        $reader = new BinnReader();
        $container = $reader->read($byteString);
        $this->assertTypeEquals($container, $expectedContainer);
        $this->assertSizeEquals($container, $expectedContainer);
        $this->assertEquals($expectedContainer, $container);
    }

    private function assertTypeEquals($container, $expectedContainer) {
        $expectedByteRepresentation = BinaryStringAtom::createHumanReadableHexRepresentation($expectedContainer->type, 1);
        $containerByteRepresentation = BinaryStringAtom::createHumanReadableHexRepresentation($container->type, 1);
        $this->assertEquals($expectedByteRepresentation, $containerByteRepresentation, "The type values of the container do not match.");
    }

    private function assertSizeEquals($container, $expectedContainer) {
        $expectedByteRepresentation = BinaryStringAtom::createHumanReadableHexRepresentation($expectedContainer->size, 1);
        $containerByteRepresentation = BinaryStringAtom::createHumanReadableHexRepresentation($container->size, 1);
        $this->assertEquals($expectedByteRepresentation, $containerByteRepresentation, "The size values of the container do not match.");
        $this->assertTrue(is_string($container->size), "The container has a byteString in the size variable.");
        $this->assertTrue(is_string($expectedContainer->size), "The expected container has a byteString in the size variable.");
    }

    public function provideTestCases() {
        return [
            "NULL" => $this->makeTestCaseNull(),
            "Uint8" => $this->makeTestCaseUint8(),
            "Uint16" => $this->makeTestCaseUint16(),
            "Uint32" => $this->makeTestCaseUint32(),
            "Uint64" => $this->makeTestCaseUint64(),
            "Text" => $this->makeTestCaseString(),
            "Blob" => $this->makeTestCaseBlob(),
            "List" => $this->makeTestCaseList(),
            "Map" => $this->makeTestCaseMap(),
            "Object" => $this->makeTestCaseObject()
        ];
    }

    private function makeTestCaseNull() {
        $container = new BinnContainer();
        $container->setType(StorageType::NOBYTES | "\x00");
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    private function makeTestCaseUint8() {
        $container = new BinnContainer();
        $container->setType(StorageType::BYTE | "\x00");
        $container->setData("\xFF");
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    private function makeTestCaseUint16() {
        $container = new BinnContainer();
        $container->setType(StorageType::WORD | "\x00");
        $container->setData("\xFE\xD8");
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    private function makeTestCaseUint32() {
        $container = new BinnContainer();
        $container->setType(StorageType::DWORD | "\x00");
        $container->setData("\xFE\xD8\xAB\x01");
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    private function makeTestCaseUint64() {
        $container = new BinnContainer();
        $container->setType(StorageType::QWORD | "\x00");
        $container->setData("\xFE\xD8\xAB\x01\xFE\xD8\xAB\x01");
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    private function makeTestCaseString() {
        $data = "pirate" . "\x00";
        $container = new BinnContainer();
        $container->setType(StorageType::STRING | "\x00");
        $container->setSize(2 + strlen($data));
        $container->setData($data);
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    public function makeTestCaseBlob() {
        $data = "pirate" . "\x00";
        $container = new BinnContainer();
        $container->setType(StorageType::BLOB | "\x00");
        $container->setSize(2 + strlen($data));
        $container->setData($data);
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    public function makeTestCaseList() {
        $data = "\x20\x7b\x41\xfe\x38\x40\x03\x15";
        $container = new BinnContainer();
        $container->setType(StorageType::CONTAINER | "\x00");
        $container->setSize("\x0B");
        $container->setCount("\x03");
        $container->setData($data);
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    public function makeTestCaseObject() {
        $data = "\x05hello\xa0\x05world\x00";
        $container = new BinnContainer();
        $container->setType(StorageType::CONTAINER | "\x02");
        $container->setSize("\x11");
        $container->setCount("\x01");
        $container->setData($data);
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    public function makeTestCaseMap() {
        $data = "\x00\x00\x00\x01\xa0\x03add\x00\x00\x00\x00\x02\xe0\x09\x02\x41\xcf\xc7\x40\x1a\x85";
        $container = new BinnContainer();
        $container->setType(StorageType::CONTAINER | "\x01");
        $container->setSize("\x1A");
        $container->setCount("\x02");
        $container->setData($data);
        $byteString = $container->getByteString();
        return [$byteString, $container];
    }

    /**
     * We were assuming that data has a start but no end. Everything to the end of the binary string was grouped as data.
     * 
     * This is shown to be wrong in the list data type, which has a data string that has the following format [binncontainer]{1..*}
     * The last element of a BINN container is the [data], and there is no designation that separates the [data] from the next container.
     * 
     * The BinnReader needs to be able to identify the length of the [type][size][count][data] string, even when it is sandwiched BETWEEN other elements having the same pattern.
     * @dataProvider providerMixedDataStringReaderTestCases
     */
    public function testMixedDataStringReader($resultArray) {
        $byteString = \implode("", $resultArray );
        $reader = new BinnReader();
        $safety = 0;
        $currentIndex = 0;
        do{
            $container = $reader->readNext( $byteString );
            $extractedString = BinaryStringAtom::createHumanReadableHexRepresentation($container->getByteString());
            $expectedString = BinaryStringAtom::createHumanReadableHexRepresentation($resultArray[ $currentIndex ]);
            $this->assertEquals( $expectedString, $extractedString, "The result for index $currentIndex is not the expected result." );
            $byteString = \substr( $byteString, strlen( $container->getByteString() ) );
            $currentIndex++;
        }while( strlen( $byteString ) > 0 && $safety++ < 10 );
    }

    public function providerMixedDataStringReaderTestCases() {
        return [
            "No Byte Series" => [["\x00", "\x01", "\x02"]],
            "One Byte Series" => [["\x20\xFE", "\x21\x01", "\x21\x34"]],
            "Two Byte Series" => [["\x40\xFE\xAB", "\x41\xAB\x05", "\x40\x25\x33"]],
            "Four Byte Series" => [["\x60\xAB\x05\x00\x00", "\x61\xFF\xFE\xD5\xFF", "\x62\xAA\xBB\xCC\xBB", "\x61\xFF\xFE\xD5\xD5"]],
            "Eight Byte Series" => [
                [
                    "\x80\xAA\x01\x55\x01\xAB\xCC\xDD\x01",
                    "\x81\x01\x05\xFE\xAB\x00\x01\x02\xAA",
                    "\x82\xA1\x05\xDD\xAB\xBB\xA1\x02\x00",
                    "\x80\xA1\x05\xDD\xAB\xBB\xA1\x02\x00"
                ]
            ],
            "String Series"=>[
                [
                    "\xA1\x03ABC\x00",
                    "\xA2\x05FRANK\x00",
                    "\xA3\x07ABIGAIL\x00",
                    "\xA4\x08WARRANTS\x00",
                    "\xA1\x05ROGER\x00"
                ]
            ],
            "Blob Series"=>[
                [
                    "\xC0\x05FRANK\x00",
                    "\xC0\x08WARRANTS\x00"
                ]
            ],
            "Container"=>[
                [
                    "\xE0\x0B\x03\x20\x7B\x41\xFE\x38\x40\x03\x15",
                    "\xE1\x1A\x02\x00\x00\x00\x01\xA0\x03add\x00\x00\x00\x00\x02\xE0\x09\x02\x41\xCF\xC7\x40\x1A\x85",
                    "\xE2\x11\x01\x05hello\xA0\x05world\x00",
                    "\xE0\x0B\x03\x20\x7B\x41\xFE\x38\x40\x03\x15"
                ]
            ]
        ];
    }
    
    /**
     * This test verifies that ReadAll breaks a string of multiple byte strings into the corresponding components.
     * @dataProvider provideReadAllTestCases
     */
    public function testReadAll( $arrayOfBinaryContainerStrings ){
        $byteString = implode( "", $arrayOfBinaryContainerStrings );
        $reader = new BinnReader();
        $containers = $reader->readAll($byteString);
        $this->assertEquals( count( $arrayOfBinaryContainerStrings ), count( $containers ), "The method should return the same number of containers as the number of byte strings used to compose the submitted byte string." );
        foreach( $containers as $key=>$container ){
            $correspodingByteString = BinaryStringAtom::createHumanReadableHexRepresentation($arrayOfBinaryContainerStrings[ $key ]);
            $reconstructedByteString = BinaryStringAtom::createHumanReadableHexRepresentation($container->getByteString());
            $this->assertEquals( $correspodingByteString, $reconstructedByteString, "The result was expected to be $correspodingByteString at position $key, but $reconstructedByteString is returned." );
        }
    }
    public function provideReadAllTestCases(){
        return [
            [
                [
                "\x00",
                "\x01",
                "\x02",
                "\x20\xFF",
                "\x21\xEF",
                "\x20\x01"
                ]
            ]
        ];
    }
    
    /**
     * This is a test case that arises out of a bug in version 1.0-alpha.
     * 
     * The problem is discovered to be that the hex value of the size is "30", which is the string character "0".
     * 
     * When testing if a size is present, the statement if( $sizeString ) is being interpreted as if( "0" ), and PHP reads this as false,
     * so the size was not being used to determine the data length.
     * 
     * This has been fixed by changing the if statement so that size is now used as long as it is not null and is not an empty string.
     */
    public function testCase1(){
        $data = "e2 30 02 04 64 61 74 65 a1 19 32 30 31 38 2d 30 37 2d 32 35 20 32 33 3a 31 34 3a 32 34 2b 30 30 3a 30 30 00 05 76 61 6c 75 65 a0 03 4d 79 61 00";
        $compressedHex = str_replace(" ", "", $data);
        $binaryData = hex2bin($compressedHex);
        $reader = new BinnReader();
        $resultContainer = $reader->readNext($binaryData);
        /* @var $resultContainer BinnContainer */
        $expectedDataLong = "04 64 61 74 65 a1 19 32 30 31 38 2d 30 37 2d 32 35 20 32 33 3a 31 34 3a 32 34 2b 30 30 3a 30 30 00 05 76 61 6c 75 65 a0 03 4d 79 61 00";
        $this->assertEquals( $expectedDataLong, BinaryStringAtom::createHumanReadableHexRepresentation($resultContainer->data), "The data is successfully extracted." );
    }
    
    /**
     * An error coming from use of the version 1.0.1-alpha reveals that string containers with 4-byte sizes are incorrectly read.
     * 
     * The error that occurred in this case relates to the manner in which the key-value object was being set and used.
     * The value of the key was not being properly interpreted.
     * 
     * Fixed: 27 Jul 2018
     */
    public function testCase2(){
        $data = "a0 80 00 01 00 f5 89 14 e3 f1 31 85 a5 97 cc cb 0e 58 05 44 80 15 87 33 97 ad c4 5c c6 c7 58 5e 83 a3 42 d7 e3 9a b1 19 96 9f 4d 87 09 93 6b a0 03 24 b6 03 23 9f 7a ee f4 07 04 36 20 d8 3e 05 9d f7 f6 39 b4 44 9d df 56 a9 d7 54 24 72 1a d8 66 66 59 09 6c 99 e8 e5 47 71 ad 37 59 3a 3a 08 ef e4 77 1b 3d 11 63 dd 61 aa 89 d0 e3 e6 0c dd 5d b4 c1 60 12 80 5b 27 10 54 91 b2 c0 17 9d 5a c1 5e 50 53 95 9e 05 e4 06 dd a4 1f ee 34 2a 68 55 54 96 16 9f 95 5c b8 e4 9d d3 9a a0 af c1 b9 64 01 bc 7e 51 1e 08 b1 fd 31 54 39 69 c0 83 42 8c bd e8 f7 08 9a f9 d3 9a 2a de a8 b0 0b ef 4c 2a a8 b4 0a 66 a8 c1 53 a3 dc 76 29 0d 51 12 21 1c 3c 0b ee 2c b2 ef d5 a5 0b eb 2d 1b 07 6a 51 0b f7 94 a2 99 dd a5 b1 a4 dc 7e ea 48 af 04 54 40 20 75 67 8b df 17 c1 61 45 56 d5 a8 9b b7 d7 f5 d3 c8 99 42 00";
        $compressedHex = str_replace(" ", "", $data);
        $binaryData = hex2bin($compressedHex);
        $reader = new BinnReader();
        $resultContainer = $reader->readNext($binaryData);
        $output = $resultContainer->getByteString();
        assert( $output == $binaryData );
        $size = strlen( $output );
        $lastChar = $output[ $size - 1 ];
        $this->assertEquals( "\x00", $lastChar, "The last character of the string selection is a null byte." );
    }
    
    /**
     * An error coming from use of the version 1.0.1-alpha reveals that string containers with 4-byte sizes are incorrectly read.
     * 
     * This test builds off of test case 2.
     * This test case previously failed, because the object was using the string length as the key length, so instead of extracting 1 or 4 bytes for a size string, it would offset by 10 bytes and then extract 10 bytes, this shift 
     * caused text values to contain non-ascii binary, and caused future elements to have incorrect key-value formatting.
     * 
     * Further complications were observed in an if statement that was looking for an indication that a size field was 4 bytes long (indicated by a 1 bit in the most-significant spot). The flag check of "\x80" & $char produced "\x00" as the result, which was 
     * interpreted by PHP as a string of length 1, which evaluates to true... all size fields were assumed to be 4-bytes (incorrectly)
     * 
     * Fixed: 27 Jul 2018
     */
    public function testCase3(){
        $data = "e0 3d 01 e2 3a 02 04 64 61 74 65 a1 19 32 30 31 37 2d 30 31 2d 31 39 20 31 33 3a 34 31 3a 34 39 2d 30 38 3a 30 30 00 05 76 61 6c 75 65 e2 10 02 04 64 69 61 73 20 42 03 73 79 73 20 7d";
        $compressedHex = str_replace(" ", "", $data);
        $binaryData = hex2bin($compressedHex);
        $reader = new BinnReader();
        $resultContainer = $reader->readNext($binaryData);
        $factory = new \JRC\binn\core\NativeFactory();
        $result = $factory->read($binaryData);
        /* @var $resultContainer BinnContainer */
        $expectedDataLong = "e2 3a 02 04 64 61 74 65 a1 19 32 30 31 37 2d 30 31 2d 31 39 20 31 33 3a 34 31 3a 34 39 2d 30 38 3a 30 30 00 05 76 61 6c 75 65 e2 10 02 04 64 69 61 73 20 42 03 73 79 73 20 7d";
        $this->assertEquals( $expectedDataLong, BinaryStringAtom::createHumanReadableHexRepresentation($resultContainer->data), "The data is successfully extracted." );
    }
    
    public function testEmptyStringShortSizeReading(){
        $binary = "\xa0\x00\x00";
        $reader = new BinnReader();
        $container = $reader->read($binary);
        $this->assertEquals( "\xa0", $container->type, "The type is 0xA0" );
        $this->assertEquals( "\x00", $container->size, "The size is 0x00" );
        $this->assertEquals( "\x00", $container->data, "The data is 0x00" );
    }

}
