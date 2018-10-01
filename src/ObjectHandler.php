<?php 

namespace Salsa;

/**
 * summary
 */
class ObjectHandler
{
    
	private $object;

	public function __construct( Object $object )
	{
		$this->setObject( $object );
		$this->call();
	}

	// setter
	public function setObject( $object )
	{
		$this->object = $object;
	}

	public function getObject()
	{
		return $this->object;
	}


	public function call()
	{
		// var_dump( getttype( $this->object ) );
	}

}