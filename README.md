# binn
A PHP implementation of Binn formatted strings for standard communication of data.

The BINN Specification ([visible here](https://github.com/liteserver/binn/blob/master/spec.md)) is one of many standardized binary formats for data storage and transfer. It is useful for storing data in a way that can be read by 
any system, unlike the native PHP serialize method which is only natively readable by PHP itself. Many languages have BINN implementations already, which makes this extremely useful for communicating data-sets between different systems.

The specification linked above hints at using 2's compliment formatting for numbers. More research must be done to learn how dates should be formatted. This readme will be updated with current information as the library grows.

Binary representation is important for encryption and signing. The BINN format provides a deterministic method of ensuring that an object has the same exact binary representation before a cryptographic signature is verified or a hash is generated.
