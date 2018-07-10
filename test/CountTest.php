<?php

use PHPUnit\Framework\TestCase;

require_once realpath(__DIR__ . '/../autoload.php');

use JRC\binn\core\Count;

/**
 * Description of CountTest
 *
 * @author jaredclemence
 */
class CountTest extends TestCase {
    public function testZeroCountToString(){
        $count = new Count();
        $count->setByteString("\x00");
        $data = $count->getByteString();
        $this->assertEquals( "\x00", $data, "The count string produces a one byte output indicating zero length." );
    }
    
    public function testEmptyCountToString(){
        $count = new Count();
        $count->setByteString("");
        $data = $count->getByteString();
        $this->assertEquals( "", $data, "The count string produces a one byte output indicating zero length." );
    }
}
