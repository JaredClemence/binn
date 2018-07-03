<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\BooleanBuilder;
use JRC\binn\builders\NullBuilder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * Description of NoByteBuilderTest
 *
 * @author jaredclemence
 */
class NoByteBuilderTest extends TestCase{
    public function testTrueRead(){
        $builder = new BooleanBuilder(true);
        $result = $builder->make();
        $this->assertTrue( $result );
    }
    public function testFalseRead(){
        $builder = new BooleanBuilder(false);
        $result = $builder->make();
        $this->assertFalse( $result );
    }
    public function testNullRead(){
        $builder = new NullBuilder();
        $result = $builder->make();
        $this->assertNull( $result );
    }
}
