<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;

/**
 * Description of StorageType
 *
 * @author jaredclemence
 */
class StorageType {
    public static $NOBYTES = 0x00;
    public static $BYTE = 0x20; //1 byte
    public static $WORD = 0x40; //2 bytes
    public static $DWORD = 0x60; //4 bytes
    public static $QWORD = 0x80; //8 bytes
    public static $STRING = 0xA0;
    public static $BLOB = 0xC0;
    public static $CONTAINER = 0xE0;
}
