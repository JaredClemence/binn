<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\NativeBuilder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of NativeFactoryTest
 *
 * @author jaredclemence
 */
class NativeBuilderTest extends TestCase {
    /**
     * 
     * @param type $type
     * @param type $result
     * @dataProvider provideNoByteTypeCases
     */
    public function testNoByteBuilders( $type, $result ){
        $builder = NativeBuilder::getRegisteredBuilder($type);
        /* @var $builder NativeBuilder */
        $builder->read("", "");
        $builderResult = $builder->make();
        $this->assertEquals( $result, $builderResult, "The result does not match the expected value." );
    }
    public function provideNoByteTypeCases(){
        return [
            "NULL"=>["\x00", null],
            "TRUE"=>["\x01", true],
            "FALSE"=>["\x02", false]
        ];
    }
    
    public function testUnregisteredBuilder(){
        $builder = NativeBuilder::getRegisteredBuilder("\xB0\x5B");
        /* @var $builder NativeBuilder */
        $builder->read("", "");
        $builderResult = $builder->make();
        $this->assertNull( $builderResult, "The unregistered builder type returns a NullBuilder." );
    }
    
    /**
     * @param type $type
     * @param type $data
     * @param type $result
     * @dataProvider provideStringBuilderCases
     */
    public function testStringBuilderTypes( $type, $data, $result ){
        $builder = NativeBuilder::getRegisteredBuilder($type);
        $builder->read( "", $data );
        $stringOutput = $builder->make();
        $this->assertEquals( $result, $stringOutput, "The string result does not match the expected output." );
    }
    
    public function provideStringBuilderCases(){
        return [
            "TEXT"=>["\xA0","This is a long sentence!\x00","This is a long sentence!"],
            "DECIMALSTRING"=>["\xA4","This is a long sentence!\x00","This is a long sentence!"],
            "BLOB"=>["\xC0","-154.192341512341125341109234871235123401982340\x00","-154.192341512341125341109234871235123401982340"],
        ];
    }
}
