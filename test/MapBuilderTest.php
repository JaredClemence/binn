<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\MapBuilder as Builder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of ListBuilderTest
 *
 * @author jaredclemence
 */
class MapBuilderTest extends TestCase {
    /**
     * @param type $dataCount
     * @param type $binaryString
     * @param type $expectedResult
     * @dataProvider provideSimpleReadTestCases
     */
    public function testSimpleRead( $dataCount, $binaryString, $expectedResult ){
        //$this->markTestIncomplete();
        $builder = new Builder();
        $builder->read( $dataCount, $binaryString );
        $result = $builder->make();
        $this->assertTrue( is_array( $result ), "The builder produces an array as output." );
        foreach( $expectedResult as $key=>$data ){
            $this->assertArrayHasKey( $key, $result, "The result data is missing expected data for the key '$key'." );
            $this->assertEquals( $data, $result[$key], "The result data does not match the expected data." );
        }
        foreach( $result as $key=>$data ){
            $this->assertArrayHasKey( $key, $expectedResult, "The result data has unexpected data for the key '$key'." );
        }
    }
    public function provideSimpleReadTestCases(){
        return [
            ["\x00", "", []],
            ["\x01", "\x00\x00\x00\x04\x20\x7B", [4=>123]],
            ["\x02", "\x00\x00\x00\x05\x41\xFE\x38\x00\x00\x00\x06\x20\x7B", [5=>-456, 6=>123]],
            ["\x03", "\x00\x00\x00\x01\x20\x7B\x00\x00\x00\x03\x41\xFE\x38\x00\x00\x00\x05\x40\x03\x15", [1=>123,3=>-456,5=>789]]
        ];
    }
}
