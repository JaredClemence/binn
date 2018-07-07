# binn
A PHP implementation of Binn formatted strings for standard communication of data.

The BINN Specification ([visible here](https://github.com/liteserver/binn/blob/master/spec.md)) is one of many standardized binary formats for data storage and transfer. It is useful for storing data in a way that can be read by 
any system, unlike the native PHP serialize method which is only natively readable by PHP itself. Many languages have BINN implementations already, which makes this extremely useful for communicating data-sets between different systems.

The specification linked above hints at using 2's compliment formatting for numbers. More research must be done to learn how dates should be formatted. This readme will be updated with current information as the library grows.

Binary representation is important for encryption and signing. The BINN format provides a deterministic method of ensuring that an object has the same exact binary representation before a cryptographic signature is verified or a hash is generated.

## Main interface for working with BinnContainers.

For the most basic interactions and the quickest start, please use the class 
`\JRC\binn\BinnSpecification()`. This class provides a simple user interface for 
encoding BinnObjects and decoding them again.

In this implementation, any "Object" subtypes are always returned as a stdClass 
implementation with public attributes. All "List" and "Map" types are returned as
arrays. Similarly, any array with a string index is written as an Object to conform 
with the Binn specification.  Container types are always written with keys in 
alphabetical order. This is not part of a standard BinnSpecification, but it ensures 
that the byte order is always deterministic, which means that the binary output 
of these processes can be used for encryption and decryption without surprises. (An output 
can be verified against a signature, for example).

Due to limitations of the PHP language, some defaults have been selected for 
writing blindly (letting the system auto detect container subtypes). For example, 
PHP does not handle 64-bit decimals accurately, so this specification defaults to 
the FLOAT type over DOUBLES. Additionally, \DateTime objects are automatically 
set to the DATETIME subtype rather than the DATE or the TIME subtypes.

Future implementations will allow you to construct a container using specific 
subtypes and data.

## Reading Binn Data
    
    //1. Load binn data (received from sender or read from file) into a variable
    $binnContainerString = "\xE2\x08\x01\x03one\x01"; //BINN data is a binary string

    //2. Initialize the main interface 
    $binnSpecification = new \JRC\binn\BinnSpecification();

    //3. Call the write method.
    $nativePHPdata = $binnSpecification->read( $binnContainerString );

    //4. use output in code
    var_dump( $nativePHPdata->one ); //outputs: true

## Writing Binn Data

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
    $output = $binnSpecification->write( $a );

    //4. Store output or transmit output to a recipient.
    