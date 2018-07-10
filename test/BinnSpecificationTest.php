<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\core\BinaryStringAtom;
use JRC\binn\BinnSpecification;

require_once realpath(__DIR__ . '/../autoload.php');

/**
 * Description of BinnSpecificationTest
 *
 * @author jaredclemence
 */
class BinnSpecificationTest extends TestCase {

    /**
     * This tests the instructions provided in the README for reading data.
     */
    public function testRead() {
        //1. Load binn data (received from sender or read from file) into a variable
        $binnContainerString = "\xE2\x08\x01\x03one\x01"; //BINN data is a binary string
        //2. Initialize the main interface 
        $binnSpecification = new \JRC\binn\BinnSpecification();

        //3. Call the write method.
        $nativePHPdata = $binnSpecification->read($binnContainerString);

        //4. use output in code
        $result = $nativePHPdata->one; //true

        $this->assertEquals(true, $result, "The read test fails in the README instructions.");
    }

    /**
     * This tests the instructions provided in the README for reading data.
     */
    public function testWrite() {
        //1. Put data into a container class (array or object) with public attributes.
        $a = new stdClass();
        $a->one = 1;
        $a->two = 2;
        $a->three = new stdClass();
        $a->three->cat = "cat";
        $a->three->dog = "dog";

        //2. Initialize the main interface 
        $binnSpecification = new \JRC\binn\BinnSpecification();

        //3. Call the write method.
        $output = $binnSpecification->write($a);

        //4. Store output or transmit output to a recipient.
        
        $expectation = "\xe2\x2c\x03\x03\x6f\x6e\x65\x20\x01\x05\x74\x68\x72\x65\x65\xe2\x17\x02\x03\x63\x61\x74\xa0\x03\x63\x61\x74\x00\x03\x64\x6f\x67\xa0\x03\x64\x6f\x67\x00\x03\x74\x77\x6f\x20\x02";
        $hexOutput = BinaryStringAtom::createHumanReadableHexRepresentation($output);
        $hexExpectation = BinaryStringAtom::createHumanReadableHexRepresentation($expectation);
        $this->assertEquals( $hexExpectation, $hexOutput, "The write test fails in the README instructions.");
    }
    
    /**
     * Test for rare failure case when an ASCII number string is used as the container size.
     * 
     * The BinnSpec failed on previous versions when a container had a size of 55, because 
     * 55 translates to the character "7", which the code accidentally interpreted as user error 
     * and converted to "\x07". This truncated the length of the data provided.
     */
    public function testSizeStringIsAsciiNumberValueCase(){
        $header = new stdClass();
        $header->{"created_by"} = "Jared Clemence";
        $header->{"created_on"} = "2018-07-29";
        
        $binnSpec = new BinnSpecification();
        $binary = $binnSpec->write($header);
        $genericObj = $binnSpec->read( $binary );
        
        foreach( $header as $attribute=>$value ){
            $this->assertObjectHasAttribute( $attribute, $genericObj, "The generic object should have the attribute `$attribute`, but it appears to be missing." );
            $this->assertEquals( $value, $genericObj->{$attribute}, "The generic object should have the same value for the attribute `$attribute`." );
        }
    }
    
    public function testEmptyListReads(){
        $listOfEmptyLists = "\xe0\x09\x02\xe0\x03\x00\xe0\x03\x00";
        $binnSpec = new BinnSpecification();
        $data = $binnSpec->read($listOfEmptyLists);
        $this->assertTrue( is_array( $data ), "The data is an array." );
        $this->assertEquals( 2, count( $data ), "The data has two elements." );
        $this->assertTrue( is_array( $data[0] ), "The first element of data is an array." );
        $this->assertTrue( is_array( $data[1] ), "The second element of data is an array." );
        $this->assertEquals( 0, count( $data[0] ), "The first element of data is an empty array." );
        $this->assertEquals( 0, count( $data[1] ), "The second element of data is an empty array." );
    }

}
