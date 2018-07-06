<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\TextBuilder;
use JRC\binn\core\BinaryStringAtom;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 * This test encapsulates tests for multiple string container types.
 * 
 * In PHP the Blob type and the String type are the same.
 *
 * @author jaredclemence
 */
class StringContainerTest extends TestCase{
    /**
     * 
     * @param string $dataString A null terminated string.
     * @param string $expectedStringOutput The string without the null byte at the end.
     * @dataProvider provideTestCasesForStringConversion
     */
    public function testStringConvesion( $dataString, $expectedStringOutput ){
        $builder = new TextBuilder();
        $builder->read( 0, $dataString );
        $stringResult = $builder->make();
        $hexResult = BinaryStringAtom::createHumanReadableHexRepresentation($stringResult);
        $expectedHexResult = BinaryStringAtom::createHumanReadableHexRepresentation($expectedStringOutput);
        $this->assertEquals( $expectedHexResult, $hexResult, "The hex codes do not match for the output and the expected output.");
    }
    public function provideTestCasesForStringConversion(){
        return [
            ["world\x00", "world"],
            ["add\x00", "add"]
        ];
    }
}
