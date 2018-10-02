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

  private $parameters;
  public $patternAsRegex;
  public $currentMatchedRoute;


  const ALL_METHODS = "GET|POST|PUT|DELETE";
  const METHOD_GET = "GET";
  const METHOD_POST = "POST";
  const METHOD_PUT = "PUT";
  const METHOD_DELETE = "DELETE";

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

    $this->setMethod();

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

  public function setMethod()
  {
    $this->currentMethod = $_SERVER["REQUEST_METHOD"];
    return $this;
  }

  public function getMethod()
  {
    return $this->currentMethod;
  }

	public function getHandler()
	{
		return $this->handler;
	}

  public function setParams( $parameter = array() )
  {
    $this->parameters = $parameter;
    return $this;
  }

  public function getParams()
  {
    return $this->parameters;
  }

	public function addRoute( $route, $parameter, string $method = self::ALL_METHODS )
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

        if( in_array( $this->currentMethod, $methodArray ) ){

          $this->patternAsRegex = $this->getRegex( $route );

          $return = $this->parseRegex( $this->patternAsRegex );

          if( is_array( $return ) && !empty( $return ) ){
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
      $this->error();
      die;
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

    // turn "(/)" into "/?"
    $pattern = preg_replace( '#\(/\)#', '/?', $pattern );


    $allowedCharacters = '[a-zA-Z0-9\_\-]+';

    $pattern = preg_replace(
      '/:('.$allowedCharacters.')/', // replace ":param"
      '(?<$1>'. $allowedCharacters .')', // with "(?<param>[a-zA-Z0-9\_\-]+)"
      $pattern
    );


    // capture group for {param}
    $pattern = preg_replace(
      '/{('.$allowedCharacters.')}/',
      '(?<$1>'.$allowedCharacters.')',
      $pattern
    );

    $patternAsRegex = "@^" . $pattern . "$@D";

    return $patternAsRegex;
  }

  public function parseRegex( $url )
  {

    if( $ok = !!$this->patternAsRegex ){

      if( $ok = preg_match( $this->patternAsRegex, $this->getCurrentRoute(), $matches ) ){
        // get elements with string keys from matches
        $params = array_intersect_key($matches, array_flip( array_filter( array_keys( $matches ), 'is_string' ) ) );

        $this->setParams( $params );
        return $params;
      }
      else {
        return false;
      } 
      // needs to check current route agaisnt match

    }

  }

	public function process()
	{
    if( !isset( $this->handler ) ){
      return false;
    }


		$type = gettype($this->handler);

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
          $return = $controller->{$this->handler["method"]}( $this->getParams() );
          if( is_string( $return ) ){
            echo $return;
          } 
        }
      }
    }
	}

}
