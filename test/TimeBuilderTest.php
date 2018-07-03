<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\builders\DateTimeBuilder as Builder;

require_once realpath( __DIR__ . '/../autoload.php' );

/**
 *  @author jaredclemence
 */
class TimeBuilderTest extends TestCase {
    /**
     * @dataProvider provideReadTestCases
     */
    public function testRead( $data, $expectedTimeFormat ){
        $builder = new Builder();
        $builder->read( "", $data );
        $dateTime = $builder->make();
        $zone = new DateTimeZone("UTC");
        $dateTime->setTimeZone( $zone );
        $format = "h:i:sP";
        $this->assertInstanceOf( DateTime::class, $dateTime );
        $this->assertEquals( $expectedTimeFormat, $dateTime->format( $format ) );
    }
    public function provideReadTestCases(){
        return [
            ["00:00:00-0800\x00", "08:00:00+0000"],
            ["23:59:00-0800\x00", "07:59:00+0000"]
        ];
    }
}
