<?php

namespace Salsa;


use Salsa\Http\HTTPHandler as HTTP;
use Salsa\Regex\RegexHandler as Regex;
use Salsa\Error\ErrorHandler as Error;

final class Salsa 
{

	private $config;
	private $baseRoute;
	private $routes;
	private $currentRoute;
	private $currentMethod;
	private $method;
	private $handler;

  public $currentMatchedRoute;
  public $http;
  public $regex;

	public function __construct( array $config = array() )
	{
		$this->setConfig( $config );
		if( isset( $this->config["baseRoute"] ) ){
			$this->setBaseRoute($this->config["baseRoute"]);
		}
    $this->http = new HTTP;
    $this->regex = new Regex;
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
    $this->http->setMethod();
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

  public function setCurrentMatchedRoute( $route )
  {
    $this->currentMatchedRoute = $route;
  }

  public function getCurrentMatchedRoute()
  {
    return $this->currentMatchedRoute;
  }

	public function setHandler( $handler = null )
	{
		if( $handler ){
				$this->handler = $handler;	
		}
	}

	public function getHandler()
	{
		return $this->handler;
	}

	public function addRoute( $route, $parameter, string $method = HTTP::ALL_METHODS )
	{
		$this->routes[strtolower($route)][$method] = $parameter;
    return $this;
	}

  public function checkRoute()
  {

    if( null == $this->getCurrentRoute() ){
      return false;
    }

    if( !count( $this->routes ) ){
      return false;
    }

    foreach( $this->routes as $route => $methods ){
      
      foreach( $methods as $methodsString => $options ){
        
        $methodArray = explode( "|", $methodsString );

        if( in_array( $this->http->getMethod(), $methodArray ) ){

          $this->regex->converToRegex( $route );

          $return = $this->regex->parseRegex( $this->getCurrentRoute() );


          if( $return !== false ){
            $this->setCurrentMatchedRoute( $this->routes[$route] ); 
            $this->setHandler( $options );
            return $return;
          }
        }
      }
    }
  }

	public function run()
	{
    // TODO: tidy up routing big style!!
    // FIX: really need to stop supressing warnings
    $this->setCurrentRoute();
    $this->checkRoute();


    if( null === $this->getCurrentMatchedRoute() ){
      Error::error();
    }

		if( $this->getHandler() ) {
			$this->process();
		}
		else {
      Error::error();
		}
	}


	public function process()
	{
    if( !isset( $this->handler ) ){
      return false;
    }


		$type = gettype($this->handler);

		if( is_object( $this->handler ) && is_callable( $this->handler ) ){
			$return = call_user_func_array( $this->handler, $this->regex->getParams() );
      if( is_string( $return ) ){
        echo $return;
      }
    }
    else if( is_array( $this->handler ) && !empty( $this->handler ) ){
      if( isset( $this->handler["controller"] ) && class_exists( $this->handler["controller"] ) ){
        $controller = new $this->handler["controller"];
        if( method_exists( $controller , $this->handler["method"] ) ){
          $return = $controller->{$this->handler["method"]}( $this->getParams() );
          if( is_string( $return ) ){
            echo $return;
          } 
        }
      }
    }
	}

}
