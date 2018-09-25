<?php
namespace Salsa;

class Salsa implements MainInterface
{

	private $routes = array();
	private $namedRoutes = array();
	private $basePath = '';
	private $matchTypes = array();
	private $options = array(

	);
	private $purify;

	public function __construct( $routes = array(), $basePath = '', $matchTypes = array(), $options = array() )
	{
		$this->addRoutes( $routes );
		$this->setBasePath( $basePath );
		$this->addMatchTypes( $matchTypes );
		$this->setOpitons( $options );
		$config = \HTMLPurifier_Config::createDefault();
		$this->purify = new HTML\SHTMLPurifier( $config );
	}

	public function addRoutes( $routes )
	{
		if( !is_array( $routes ) ){
			throw new \Excpetion( 'Routes shoule be an array' );
		}
		else {
			foreach( $routes as $route ){
				call_user_func_array(array( $this, 'map' ), $route );
			}
		}
	}

	public function map( $method, $route, $target, $name = null )
	{
		$this->routes[] = array( $method, $route, $target, $name );

		if( $name ){
			if( isset( $this->namedRoutes[$name] ) ){
				throw new \Excpetion( "Can not redeclare route {$name}" );
			}
			else {
				$this->namedRoute[$route] = $route;
			}
		}
		return;
	}

	public function match( $requestUrl = null, $requestMethod = null )
	{
		$params = array();
		$match = false;

		if( $requestUrl === null ){
			$requestUrl = isset( $_SERVER["REQUEST_URI"] ) ? $_SERVER["REQUEST_URI"] : "/";
		}

		// remove baseURL
		$requestUrl = substr( $requestUrl, strlen( $this->basePath ) );

		// remove
		if( ( $strpos = strpos( $requestUrl, "?" )) !== false ){
			$requestUrl = substr( $requestUrl, 0, $strpos );
		}


		if( $requestMethod === null ){
			$requestMethod = isset( $_SERVER["REQUEST_METHOD"] ) ? $_SERVER["REQUEST_METHOD"] : "GET";
		}

		foreach( $this->routes as $handler ){

			// echo "<pre>";
			// var_dump( $handler );
			// echo "</pre>";

			list( $methods, $route, $target, $name ) = $handler;

			$method_match = ( stripos( $methods, $requestMethod ) !== false );

			// Method did not match continue to next route
			if( !$method_match ){
				continue;
			}


			if( $route === "*" ){
				// wildcard matches all
				$match = true;
			} elseif ( ( $position = strpos( $route, '[' ) ) === false ) {
				// No params in url, do string comparison
				$match = strcmp($requestUrl, $route) === 0;
			} else {
				// comapre request routewith route 
				if( strncmp( $requestUrl, $route, $position ) !== 0 ){
					continue;
				}

				$regex = $this->compiteRoute( $route );

				$match = preg_replace( $regex, $requestUrl, $params ) === 1;
			}

			if( $match ){

				if( $params ){
					foreach( $params as $key => $value ){
						if( is_numeric( $key ) ){
							unset( $params[$key] );
						}
					}
				}


				if( is_callable( $target ) ){
					$return = call_user_func( $target );

					if( is_string( $return ) && $returndata = json_decode( $return,1 ) ){

					} 
				}


				return array(
					'target' => $target,
					'params' => $params,
					'name' => $name,
					'returndata' => $returndata
				);

			}

		}

		return false;
	}

	public function setBasePath( $basePath )
	{
		$this->basePath = $basePath;
	}
	
	public function addMatchTypes($matchTypes)
	{
		$this->matchTypes = array_merge($this->matchTypes, $matchTypes);
	}

	public function compileRoute( $route )
	{
		if( preg_match_all( '`(/|\.|)\[([^:\]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER ) ){


			$matchTypes = $this->matchTypes;

			foreach( $matches as $match ){
				list( $block, $pre, $type, $param, $optional ) = $match;

				if( isset( $matchTypes[$type] ) ){
					$type = $matchTypes[$type];
				} 
				if( $pre === "." ){
					$pre = "\.";
				}

				$options = $optional !== '' ? '?' : null;

				$pattern = '(?:' 
					. ( $pre !== '' ? $pre : null )
					. '('
					. (  $param !== '' ? "?p<{$param}>" : null )
					. $type 
					. ')'
					. $optional
					. ')'
					. $optional;
				$route = str_replace( $block, $patther, $route );
			}

		}

		return "`^$route$`u";
	}

	public function setOpitons( $options = array() ){
		$this->options = $options;
	}

}