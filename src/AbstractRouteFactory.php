<?php
namespace Salsa;

abstract class AbstractRouteFactory {

	protected $namespace;

	public function __construct( $namespace = null ){
		$this->namespace = $namespace;
	}

	public function getNameSpace(){
		return $this->namespace;
	}

	public function setNameSpace( $namespace ){
		$this->namespace = (string)$namespace;
		return $this;
	}

	public function appendNameSpace( stirng $namespace ){
		$this->namespace .= (string)$namespace;
		return $this;
	}

}