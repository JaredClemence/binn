<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\DateTimeBuilder as Builder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 *  @author jaredclemence
 */
class DateTimeBuilderTest extends TestCase {
    /**
     * @dataProvider provideReadTestCases
     */
    public function testRead( $data, $expectedTimeFormat ){
        $builder = new Builder();
        $builder->read( "", $data );
        $dateTime = $builder->make();
        $zone = new DateTimeZone("GMT");
        $dateTime->setTimeZone( $zone );
        $format = "Y-m-d h:i:sP";
        $this->assertInstanceOf( DateTime::class, $dateTime );
        $this->assertEquals( $expectedTimeFormat, $dateTime->format( $format ) );
    }
    public function provideReadTestCases(){
        return [
            ["2018-12-31 00:00:00-08\x00", "2018-12-31 08:00:00+00:00"],
            ["1922-05-28 23:59:00-08\x00", "1922-05-29 07:59:00+00:00"]
        ];
    }
}
