# binn
A PHP implementation of Binn formatted strings for standard communication of data.

The Binn Specification ([visible here](https://github.com/liteserver/binn/blob/master/spec.md)) is one of many standardized binary formats for data storage and transfer.

The specification linked above hints at using 2's compliment formatting for numbers. More research must be done to learn how dates should be formatted. This readme will be updated with current information as the library grows.

Binary representation is important for encryption and signing. The Binn format provides a determinitistic method of ensuring that an object has the same exact binary representation before a cryptographic signature is verified or a hash is generated.
