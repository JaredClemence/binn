<?php

use PHPUnit\Framework\TestCase;
use JRC\binn\core\BinaryStringAtom;
use JRC\binn\BinnSpecification;
use Faker\Factory;

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
        $this->assertEquals($hexExpectation, $hexOutput, "The write test fails in the README instructions.");
    }

    /**
     * Test for rare failure case when an ASCII number string is used as the container size.
     * 
     * The BinnSpec failed on previous versions when a container had a size of 55, because 
     * 55 translates to the character "7", which the code accidentally interpreted as user error 
     * and converted to "\x07". This truncated the length of the data provided.
     */
    public function testSizeStringIsAsciiNumberValueCase() {
        $header = new stdClass();
        $header->{"created_by"} = "Jared Clemence";
        $header->{"created_on"} = "2018-07-29";

        $binnSpec = new BinnSpecification();
        $binary = $binnSpec->write($header);
        $genericObj = $binnSpec->read($binary);

        foreach ($header as $attribute => $value) {
            $this->assertObjectHasAttribute($attribute, $genericObj, "The generic object should have the attribute `$attribute`, but it appears to be missing.");
            $this->assertEquals($value, $genericObj->{$attribute}, "The generic object should have the same value for the attribute `$attribute`.");
        }
    }

    public function testSpecificFailure() {
        $obj = $this->makeProblemObject();
        $binnSpec = new \JRC\binn\BinnSpecification();
        $binn = $binnSpec->write($obj);
        $result = $binnSpec->read($binn);
        $this->assertInstanceOf(stdClass::class, $result);
    }

    private function makeProblemObject() {
        $obj = new stdClass();
        $obj->uid = "396695e64b47b6de6069029adaa04f4720180725155836";
        $obj->uid_history = [
            $this->makeHistory($obj->uid)
        ];
        $obj->name = "Ritchie, Mya";
        $obj->name_history = [
            $this->makeHistory($obj->name)
        ];
        $obj->lastName = "Ritchie";
        $obj->lastName_history = [
            $this->makeHistory($obj->lastName)
        ];
        $obj->firstName = "Mya";
        $obj->firstName_history = [
            $this->makeHistory($obj->firstName)
        ];
        $obj->dob = new \DateTime("2008-08-24 19:55:48", new \DateTimeZone("America/Los_Angeles"));
        $secondDate = new \DateTime("2018-07-25 15:58:36", new \DateTimeZone("America/Los_Angeles"));
//        $obj->dob_history = [ 
//            $this->makeHistory($obj->dob),
//            $this->makeHistory($secondDate)
//        ];
        $obj->visitDate = $secondDate;
//        $obj->visitDate_history = [
//            $this->makeHistory($secondDate),
//            $this->makeHistory($obj->dob)
//        ];
        $obj->dov = $secondDate;
//        $obj->dov_history = [
//            $this->makeHistory($secondDate),
//            $this->makeHistory($obj->dob)
//        ];
        return $obj;
    }

    private function makeHistory($value) {
        $history = new stdClass();
        $history->date = new \DateTime("now");
        $history->value = $value;
        return $history;
    }

    /**
     * Problems have been noticed in the 1.0.1-alpha release dealing with the conversion of 
     * large objects. This test ensures that large objects with lengthy keys are written and read 
     * correctly.
     * 
     * @param type $object
     * @dataProvider provideLargeObjectCases
     */
    public function testLargeObjectConversion($object, $expectError) {
        //$this->markTestSkipped();
        $error = null;
        $spec = new BinnSpecification();
        try {
            $string = $spec->write($object);
            $newObj = $spec->read($string);
            $keys = get_object_vars($object);
            foreach ($object as $key => $value) {
                if( is_array( $newObj ) ){
                    $this->assertArrayHasKey($key, $newObj, "The new object has an attribute set for the key value of '$key'.");
                    $this->assertEquals($value, $newObj[$key], "The value that is restored equals the value that was stored.");
                }else{
                    $this->assertObjectHasAttribute($key, $newObj, "The new object has an attribute set for the key value of '$key'.");
                    $this->assertEquals($value, $newObj->$key, "The value that is restored equals the value that was stored.");
                }
            }
        } catch (\Exception $e) {
            $error = $e;
        }
        if ($expectError) {
            $this->assertNotNull($error, "An error was expected during test.");
        } else {
            $this->assertNull($error, "No error is generated during test.");
        }
    }

    public function provideLargeObjectCases() {
        return [
            'Lengthy Keys' => [$this->addLengthyKeys(new stdClass(), 255), false],
            'Lengthy Keys With Error' => [$this->addLengthyKeys(new stdClass(), 256), true],
            'Big Numbers' => [$this->addBigNumericIndices(new stdClass(), 4), false],
            'Big Numbers With Error' => [$this->addBigNumericIndices(new stdClass(), 5), true],
        ];
    }

    public function addLengthyKeys(stdClass $object, $length = 255) {
        $factory = Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $key = "";
            while (strlen($key) < $length) {
                $key .= $factory->randomLetter;
            }
            $object->$key = implode(" ", $factory->words(50));
        }
        return $object;
    }

    public function addBigNumericIndices(stdClass $object, $byteLength) {
        $factory = Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $binary = "";
            while (strlen($binary) < $byteLength) {
                $number = rand(1, 255);
                $char = chr($number);
                $binary .= $char;
            }
            $hex_string = bin2hex($binary);
            if (strlen($hex_string) % 2 == 1) {
                $hex_string = "0" . $hex_string;
            }
            $bigInt = hexdec($hex_string);
            $object->$bigInt = implode(" ", $factory->words(55));
        }
        return $object;
    }
    
    /**
     * Binn Spec must handle the storing of null elements.
     */
    public function testNullElements(){
        $object = new stdClass();
        $object->one = "1";
        $object->two = null;
        $object->three = [ null, null ];
        
        $spec = new BinnSpecification();
        $binaryString = $spec->write( $object );
        $restoredObject = $spec->read( $binaryString );
        
        $this->assertTrue( is_object( $restoredObject ), "Restored object is an object." );
        $this->assertObjectHasAttribute( "one", $restoredObject, "All attributes are set on the restored object." );
        $this->assertObjectHasAttribute( "two", $restoredObject, "All attributes are set on the restored object." );
        $this->assertObjectHasAttribute( "three", $restoredObject, "All attributes are set on the restored object." );
        $this->assertEquals( $object->one, $restoredObject->one, "The non-null value is restored correctly." );
        $this->assertNull( $restoredObject->two, "The null value is null as expected." );
        $this->assertEquals( [null,null], $restoredObject->three, "The null array returns a null array with two elements." );
    }
    
    /**
     * Binn Spec must handle the writing and reading of empty arrays.
     */
    public function testEmptyList(){
       $list = [];
       $binnSpec = new JRC\binn\BinnSpecification();
       $binary = $binnSpec->write($list);
       $this->assertEquals( "e0 03 00", BinaryStringAtom::createHumanReadableHexRepresentation($binary), "The Binn specification writes the empty object in the correct Binn Format." );
       $obj = $binnSpec->read($binary);
       $this->assertEquals( [], $obj, "The Binn specificatoin restores the binary value to an empty array correctly." );
    }

}
