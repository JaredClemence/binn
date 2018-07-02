<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JRC\binn\builders;

/**
 * Description of NativeBuilder
 *
 * @author jaredclemence
 */
abstract class NativeBuilder {

    static $registry = "";

    public static function register( $fullyQualifiedType, $builderInstance ){
        self::initializeRegistry();
        self::addToRegistry( $fullyQualifiedType, $builderInstance );
    }
    
    public final function read( $count, $data ){
        $this->setCount( $count );
        $this->setData( $data );
    }
    
    protected final function setCount( $count ){
        $this->count = $count;
    }
    protected final function setData( $data ){
        $this->data = $data;
    }
    protected final function getData(){
        return $this->data;
    }
    
    public function make(){
        
    }

    public static function initializeRegistry() {
        if( is_array( self::$registry ) == false ){
            self::$registry = [];
        }
    }

    public static function addToRegistry($fullyQualifiedType, $builderInstance) {
        self::$registry[$fullyQualifiedType] = $builderInstance;
    }
    
    public static function getRegisteredBuilder( $fullyQualifiedType ) {
        self::runConfigurationFile();
        return self::getBuilder( $fullyQualifiedType );
    }

    private static function runConfigurationFile() {
        if( !is_array( self::$registry ) || count( self::$registry ) == 0 ){
            $pathToConfig = realpath( __DIR__ . '/../../config/builders.php' );
            require_once $pathToConfig;
        }
    }

    private static function getBuilder($fullyQualifiedType) : NativeBuilder {
        $builder = null;
        if( isset( self::$registry[ $fullyQualifiedType ] ) ){
            $builder = self::$registry[ $fullyQualifiedType ];
        }else if(isset( self::$registry[ "\x00" ] )){
            //if no builder exists for the provided type but NullBuilder is loaded, return the NullBuilder
            $builder = self::$registry[ "\x00" ];
        }
        return $builder;
    }

}