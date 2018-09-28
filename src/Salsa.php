<?php
namespace Salsa;

/**
 * Salsa 
 * PHP Routing
 * @author Tom Kiernan tkiernan120@gmail.com+
 * 
 */
class Salsa
{
		private $routes = array();
		private $baseroute = '';
		private $currentRoute;
		private $searchparams;

    /**
     * summary
     */
    public function __construct( string $baseroute = '' )
    {
        $this->setBaseRoute( $baseroute );
    }


   	// setters
   	
   	public function addRoutes( array $routes )
   	{
   		if( !is_array( $routes ) )
   		{
   			throw new Error( 'Expecting an array instead got a ' . gettype( $routes ) );
   			return;
   		}

   		if( !empty( $routes ) )
   		{
   			foreach( $routes as $name => $options )
   			{
   				$this->addRoute( $name, $options );
   			}
   		} 
   		else {
   			throw new warning( "Received emtpy array" );
   		}
   	}

   	// TODO: Add method
    public function addRoute( string $name,  string $route, $options = array() ) 
    {
  		if( isset( $this->routes[$name] ) && !isset( $options["overwrite"] ) )
  		{
  			throw new warning( "Route {$name} has already been set, to force overwrite set overwrite option" );
  		}
  		else if( ( isset( $this->routes[$name] ) && isset( $options["overwrite"] )  && (bool)$options["overwrite"] ) || !isset( $this->routes[$name] ) )
  		{
  			$this->routes[strtolower($route)][$name] = $options;
  		}
    }

    public function setBaseRoute( string $baseroute = '' )
    {
   		$this->baseroute = trim($baseroute);
    }

    public function setCurrentRoute()
    {
    	$this->currentRoute = str_replace( $this->getBaseRoute(), "", strtok( $_SERVER["REQUEST_URI"], '?' ) );
    }

    // getters

    public function getRoute( $name )
    {
    	return isset($this->routes[$name]) ?: false;
    } 
    
    public function getRoutes()
    {
    	return $this->routes;
    }

    public function getCurrentRoute()
    {
    	return $this->currentRoute;
    }

    public function getBaseRoute()
    {
    	return $this->baseroute;
    }

    public function httpd404()
    {
    	$this->httpstatus( 404, "404 - Page not found" );
    }

    public function httpstatus( int $status, string $statusmessage = "" )
    {
    	http_response_code($status);
    	if( $statusmessage )
    	{
    		echo $statusmessage;
    	}
    	exit;
    }


    public function route()
    {

    	$this->setCurrentRoute();
    	$routes = $this->getRoutes();

    	if( isset( $routes[$this->getCurrentRoute()] ) )
    	{
    		// TODO: Handle options passed into route
    	}
    	else 
    	{
    		$this->httpd404();
    	}
    }


}