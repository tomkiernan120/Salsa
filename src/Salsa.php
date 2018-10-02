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
    return $this;
	}

	public function setBaseRoute( string $base = "" )
	{
		$this->baseRoute = strtolower($base);
    return $this;
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
		}
	}

	public function getHandler()
	{
		return $this->handler;
	}

  public function setParams( $params = array() )
  {
    $this->params = $params;
    return $this;
  }

  public function getParams()
  {
    return $this->params;
  }

	public function addRoute( $route, $params, $method = "GET|POST|PUT|DELETE" )
	{
		$this->routes[strtolower($route)][$method] = $params;
    return $this;
	}

	public function run()
	{
    // TODO: tidy up routing big style!!
    // FIX: really need to stop supressing warnings
		$route = $this->setCurrentRoute();
		$methods = explode( "|", @array_keys( @$this->routes[$route] )[0]);
		
		$this->setHandler( @array_values($this->routes[$route]) );

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

  public function getRegex( $pattern )
  {
    if( preg_match( '/[^-:\/_{}()a-zA-Z\d]/', $pattern ) ){
      return false; // invalid
    }

    // turn "(/)" int "/?"
    $pattern = preg_replace( '#\(/\)#', '/?', $pattern );

    $allowedCharacters = '[a-zA-Z0-9\_\-]+';
    $pattern = preg_replace(
      '/:( '.$allowedCharacters.' )/', // replace ":param"
      '(?<$1>'. $allowedCharacters .')', // with "(?<param>[a-zA-Z0-9\_\-]+)"
      $pattern
    );

    // capture group for {param}
    $pattern = preg_replace(
      '/{('.$allowedCharacters.')}/',
      '(?<$1>'.$allowedCharacters.')'
    );


    $patternAsRegex = "@^" . $pattern . "$@D";

    return $patternAsRegex;
  }

  public function parseRegex()
  {
    $url = $this->getCurrentRoute();

    $patterAsRegex = $this->getRegex( $url );

    if( $ok = !!$patterAsRegex ){

      // if( $ok = preg_match( $patternAsRegex, $ ) ){

      // } 
      // needs to check current route agaisnt match

    }

  }

	public function process()
	{
    if( !isset( $this->handler ) ){
      return false;
    }


		$type = gettype($this->handler);

    $this->setParams();

		if( is_object( $this->handler ) && is_callable( $this->handler ) ){
			$return = call_user_func_array( $this->handler, $this->getParams() );
      if( is_string( $return ) ){
        echo $return;
      }
    }
    else if( is_array( $this->handler ) && !empty( $this->handler ) ){
      if( isset( $this->handler["controller"] ) && class_exists( $this->handler["controller"] ) ){
        $controller = new $this->handler["controller"];
        if( method_exists( $controller , $this->handler["method"] ) ){
          $return = $controller->{$this->handler["method"]}( @$this->handler["passin"] );
          if( is_string( $return ) ){
            echo $return;
          } 
        }
      }
    }
	}
  
}
