<?php
namespace Salsa;

class Route {
	
	protected $callback;
	protected $path;
	protected $method;
	protected $count_match;
	protected $name;

	public function __construct( $callback, $path = null, $method = null, $count_match = true, $name = null ){
		$this->setCallback( $callback );
		$this->setPath( $path );
		$this->setMethod( $method );
		$this->setCountMatch( $count_match );
		$this->setName( $name );
	}

	public function getCallback(){
		return $this->callback;
	}

	public function setCallback( $callback ){
		if( !is_callable( $callback ) ){
			throw new Exception('Expected callable, got ' . gettype( $callback ));
		}
		$this->callback = $callback;
		return $this;
	}

	public function getPath(){
		return $this->path;
	}

	public function setPath( $path ){
		$this->path = (string)$path;
		return $this;
	}

	public function getMethod(){
		return $this->method();
	}

	public function setMethod( $method ){
		if( null !== $method && !is_array( $method ) && !is_string( $method ) ){
			throw new Exception( 'Expected an array or string or null' );
		}

		$this->method = $method;

		return $this;
	}


	public function getCountMatch(){
		return $this->count_match;
	}

	public function setCountMatch( $count_match ){
		$this->count_match = (bool)$count_match;
	}

	public function getName(){
		return $this->name;
	}

	public function setName( $name ){
		if( null !== $name ){
			$this->name = string( $name );
		}
		else {
			$this->name = $name;
		}
		return $this;
	}








}