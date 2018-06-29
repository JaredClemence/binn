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
        $builderResult = $builder->make();
        $this->assertNull( $builderResult, "The unregistered builder type returns a NullBuilder." );
    }
}
