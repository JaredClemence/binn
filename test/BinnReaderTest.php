<?php

use PHPUnit\Framework\TestCase;

require_once realpath(__DIR__ . '/../autoload.php');

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
                    "\xA1\x06ABC\x00",
                    "\xA2\x08FRANK\x00",
                    "\xA3\x0AABIGAIL\x00",
                    "\xA4\x0BWARRANTS\x00",
                    "\xA1\x08ROGER\x00"
                ]
            ],
            "Blob Series"=>[
                [
                    "\xC0\x08FRANK\x00",
                    "\xC0\x0BWARRANTS\x00"
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

}
