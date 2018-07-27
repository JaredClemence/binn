<?php

namespace JRC\binn\core;
use JRC\binn\core\BinnContainer;
use JRC\binn\core\KeyValue;

/**
 * Description of ContainerElement
 *
 * @author jaredclemence
 */
class ContainerElement {
    /* @var KeyValue */
    public $keyBytes;
    
    /* @var BinnContainer */
    public $dataBytes;

    public function __construct( KeyValue $keyBytes, BinnContainer $dataBytes ){
        $this->keyBytes = $keyBytes;
        $this->dataBytes = $dataBytes;
    }
    public function __toString() {
        return $this->keyBytes . $this->dataBytes;
    }
    public function getLength(){
        return strlen( $this . "" );
    }
    public function getIndexValue(){
        return $this->keyBytes->getIndexValue();
    }
    public function getDataString(){
        return $this->dataBytes->getByteString();
    }
}
