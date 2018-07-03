<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\DateTimeBuilder as Builder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 *  @author jaredclemence
 */
class DateBuilderTest extends TestCase {
    /**
     * @dataProvider provideReadTestCases
     */
    public function testRead( $data, $expectedTimeFormat ){
        $builder = new Builder();
        $builder->read( "", $data );
        $dateTime = $builder->make();
        $zone = new DateTimeZone("UTC");
        $dateTime->setTimeZone( $zone );
        $format = "Y-m-d";
        $this->assertInstanceOf( DateTime::class, $dateTime );
        $this->assertEquals( $expectedTimeFormat, $dateTime->format( $format ) );
    }
    public function provideReadTestCases(){
        return [
            ["2018-12-31\x00", "2018-12-31"],
            ["1922-05-28\x00", "1922-05-28"]
        ];
    }
}
