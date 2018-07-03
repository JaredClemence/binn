<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\ListBuilder as Builder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of ListBuilderTest
 *
 * @author jaredclemence
 */
class ListBuilderTest extends TestCase {
    /**
     * @param type $dataCount
     * @param type $binaryString
     * @param type $expectedResult
     * @dataProvider provideSimpleReadTestCases
     */
    public function testSimpleRead( $dataCount, $binaryString, $expectedResult ){
        $this->markTestIncomplete();
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
            ["\x01", "\x20\x7B", [123]],
            ["\x02", "\x41\xFE\x38\x20\x7B", [-456, 123]],
            ["\x03", "\x20\x7B\x41\xFE\x38\x40\x03\x15", [123,-456,789]]
        ];
    }
}
