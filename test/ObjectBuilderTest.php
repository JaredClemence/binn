<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\ObjectBuilder as Builder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of ListBuilderTest
 *
 * @author jaredclemence
 */
class ObjectBuilderTest extends TestCase {
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
        $this->assertTrue( is_object( $result ), "The builder produces an array as output." );
        foreach( $expectedResult as $key=>$data ){
            $this->assertObjectHasAttribute( $key, $result, "The result data is missing expected data for the key '$key'." );
            $this->assertEquals( $data, $result->{$key}, "The result data does not match the expected data." );
        }
        foreach( $result as $key=>$data ){
            $this->assertArrayHasKey( $key, $expectedResult, "The result data has unexpected data for the key '$key'." );
        }
    }
    public function provideSimpleReadTestCases(){
        return [
            ["\x00", "", []],
            ["\x01", "\x01A\x20\x7B", ["A"=>123]],
            ["\x02", "\x03Cat\x41\xFE\x38\x03Dog\x20\x7B", ["Cat"=>-456, "Dog"=>123]],
            ["\x03", "\x0Aschnozzles\x20\x7B\x0Bjargonizing\x41\xFE\x38\x05jumpy\x40\x03\x15", ["schnozzles"=>123,"jargonizing"=>-456,"jumpy"=>789]]
        ];
    }
}
