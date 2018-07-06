<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\core\BinnContainer;
use JRC\binn\core\StorageType;

require_once realpath( __DIR__ . '/../autoload.php' );
/**
 * Description of BinnContainerTest
 *
 * @author jaredclemence
 */
class BinnContainerTest extends TestCase {
    public function testGeneratesByteString(){
        $expectedSize = 2 + 4 + 1;
        $container = new BinnContainer();
        $container->setType( (StorageType::CONTAINER) | "\x02" );
        $container->setSize( "\x07" );
        $container->setCount( "\x05" );
        $container->setData( "abc" . "\x00");
        
        $byteString = $container->getByteString();
        $this->assertEquals( $expectedSize, strlen( $byteString ), "The string output has a proper binary length" );
    }
    
    public function testConvertsTypeIntoCorrectByteLength(){
        $expectedSize = 1;
        $container = new BinnContainer();
        $container->setType( (StorageType::NOBYTES) );
        $byteString = $container->getByteString();
        $this->assertEquals( $expectedSize, strlen( $byteString ), "The string output has a proper binary length" );
    }
}
