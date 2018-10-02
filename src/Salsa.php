<?php

namespace Salsa;

class Salsa 
{
	private $config;
	private $baseRoute;

	private $routes;

	private $currentRoute;
	private $currentMethod;

	private $method;

	private $handler;

	public function __construct( array $config = array() )
	{
		$this->setConfig( $config );
		if( isset( $this->config["baseRoute"] ) ){
			$this->setBaseRoute($this->config["baseRoute"]);
		}
	}

	public function setConfig( $config = array() )
	{
		$this->config = $config;
		return $this;
	}

	public function getConfig( $name = null )
	{
		if( !$name || !is_string( $name ) ){
			return $this->name;
		}
		else if( is_string( $name ) ){
			return isset( $this->config[$name] ) ? $this->config[$name] : false;
		}
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function setMethod( string $method )
	{
		$this->method = $method;
	}

	public function setBaseRoute( string $base = "" )
	{
		$this->baseRoute = strtolower($base);
	}

	public function getBaseRoute()
	{
		return $this->baseRoute;
	}

	public function setCurrentRoute()
	{
		$this->currentRoute = str_replace( $this->getBaseRoute(), "", strtolower($_SERVER["REQUEST_URI"] ) );
		if( $this->currentRoute != "/" ){
			$this->currentRoute = rtrim( $this->currentRoute, "/" );
		} 
		return $this->currentRoute;
	}

	public function getCurrentRoute()
	{
		return $this->currentRoute;
	}

	public function setHandler( $handler = null )
	{
		if( $handler ){
				$this->handler = is_array($handler) ? $handler[0] : $handler;	
				var_dump( $this->handler );
		}
	}

	public function getHandler()
	{
		return $this->handler;
	}

	public function addRoute( $route, $params, $method = "GET|POST|PUT|DELETE" )
	{
		$this->routes[strtolower($route)][$method] = $params;
	}

	public function run()
	{
		$route = $this->setCurrentRoute();
		$methods = explode( "|", @array_keys( @$this->routes[$route] )[0]);
		
		var_dump( array_values( $this->routes[$route] )  );

		$this->setHandler( array_values($this->routes[$route]) );

		if( in_array( $_SERVER["REQUEST_METHOD"], $methods ) ){
			$this->setmethod( $_SERVER["REQUEST_METHOD"] );
		}

		if( $this->getHandler() ) {
			$this->process();
		}
		else {
			$this->error();
		}
	}

	public function error( $errorMessage = "404 - Page not found", $code = 404 ) // TODO: could add a hook in here to allow for plugins
	{
		http_response_code( $code );
		echo $errorMessage;
		exit;
	}

	public function process()
	{
		if( isset( $this->handler ) ){
			$type = gettype($this->handler);
			var_dump( $type );
			if( is_object( $type ) && is_callable( $type ) ){
				call_user_func_array( $type, @$params = array() );
			}
		}
	}

}
