<?php

namespace Salsa;

/**
 * summary
 */
trait MagicTraits
{
    /**
     * summary
     */
    public function __set( $name, $value )
    {
    	var_dump( $name );
	  	if( $name == "currentOptions" ){
	  		$this->parseOptions( $value );
	  	}
	  }

	  public function parseOptions( $options ){
	  	var_dump( $options );
	  }

}