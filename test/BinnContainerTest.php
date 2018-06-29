<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use PHPUnit\Framework\TestCase;
use JRC\binn\BinnContainer;
use JRC\binn\StorageType;

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
