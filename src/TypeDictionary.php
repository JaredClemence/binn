<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn;
use JRC\binn\Type;
use JRC\binn\StorageType;

const SHORT_SUB_TYPE = 0;
const LONG_SUB_TYPE = 1;
/**
 * Description of TypeDictionary
 *
 * @author jaredclemence
 */
class TypeDictionary {
    static public $NULL;
    static public $TRUE;
    static public $FALSE;
    static public $UINT8;
    static public $INT8;
    static public $UINT16;
    static public $INT16;
    static public $UINT32;
    static public $INT32;
    static public $FLOAT;
    static public $UINT64;
    static public $INT64;
    static public $DOUBLE;
    static public $TEXT;
    static public $DATETIME;
    static public $DATE;
    static public $TIME;
    static public $DECIMALSTR;
    static public $BLOB;
    static public $LIST;
    static public $MAP;
    static public $OBJECT;
    
    static public function initialize(){
        self::$NULL = new Type(StorageType::$NOBYTES, SHORT_SUB_TYPE, 0);
        self::$TRUE = new Type(StorageType::$NOBYTES, SHORT_SUB_TYPE, 1);
        self::$FALSE = new Type(StorageType::$NOBYTES, SHORT_SUB_TYPE, 2);
        self::$UINT8 = new Type(StorageType::$BYTE, SHORT_SUB_TYPE, 0 );
        self::$INT8 = new Type(StorageType::$BYTE, SHORT_SUB_TYPE, 1);
        self::$UINT16 = new Type(StorageType::$WORD, SHORT_SUB_TYPE, 0);
        self::$INT16 = new Type(StorageType::$WORD, SHORT_SUB_TYPE, 1);
        self::$UINT32 = new Type(StorageType::$DWORD, SHORT_SUB_TYPE, 0);
        self::$INT32 = new Type(StorageType::$DWORD, SHORT_SUB_TYPE, 1);
        self::$FLOAT = new Type(StorageType::$DWORD, SHORT_SUB_TYPE, 2);
        self::$UINT64 = new Type(StorageType::$QWORD, SHORT_SUB_TYPE, 0);
        self::$INT64 = new Type(StorageType::$QWORD, SHORT_SUB_TYPE, 1);
        self::$DOUBLE = new Type(StorageType::$QWORD, SHORT_SUB_TYPE, 2);
        self::$TEXT = new Type(StorageType::$STRING, SHORT_SUB_TYPE, 0);
        self::$DATETIME = new Type(StorageType::$STRING, SHORT_SUB_TYPE, 1);
        self::$DATE = new Type(StorageType::$STRING, SHORT_SUB_TYPE,2);
        self::$TIME = new Type(StorageType::$STRING, SHORT_SUB_TYPE,3);
        self::$DECIMALSTR = new Type( StorageType::$STRING, SHORT_SUB_TYPE,4);
        self::$BLOB = new Type(StorageType::$BLOB, SHORT_SUB_TYPE, 0);
        self::$LIST = new Type(StorageType::$CONTAINER, SHORT_SUB_TYPE, 0 );
        self::$MAP = new Type(StorageType::$CONTAINER, SHORT_SUB_TYPE,1);
        self::$OBJECT= new Type(StorageType::$CONTAINER, SHORT_SUB_TYPE, 2);
    }
}
