<?php

namespace JRC\binn;

/**
 * Description of StorageType
 *
 * @author jaredclemence
 */
class StorageType {
    const NOBYTES = "\x00";
    const BYTE = "\x20"; //1 byte
    const WORD = "\x40"; //2 bytes
    const DWORD = "\x60"; //4 bytes
    const QWORD = "\x80"; //8 bytes
    const STRING = "\xA0";
    const BLOB = "\xC0";
    const CONTAINER = "\xE0";
}
