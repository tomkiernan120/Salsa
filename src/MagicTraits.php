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
			// XXX: Possibly need to remove this option? - Tom Kiernan <tkiernan120@gmail.com>
	  	// if( $name == "currentOptions" ){
	  	// 	$this->parseOptions( $value );
	  	// }
	  }

	  public function parseOptions(){

			if( isset( $this->currentOptions ) ){
				if( gettype( $this->currentOptions ) == "object" ){
					call_user_func( $this->currentOptions );
				}
				else if( is_array( $this->currentOptions ) ){
	
					if( isset( $this->currentOptions["controller"] ) ){
						$controller = new $this->currentOptions["controller"];
						$this->returndata = $controller->{$this->currentOptions["method"]}( $this->currentOptions["method"] );
					}
				}
				else if( is_string( $this->currentOptions ) ){
					if( is_dir( "./templates" ) ){
						if( is_file( "./templates/{$this->currentOptions}.php" ) ){
							echo file_get_contents( "./templates/{$this->currentOptions}.php" );
						}
					}
				}
			}
	  }

}