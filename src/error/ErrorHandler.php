<?php
namespace Salsa\Error;

/**
 * summary
 */
class ErrorHandler
{
    /**
     * summary
     */
    public function __construct()
    {
        
    }


		public static function error( $errorMessage = "404 - Page not found", $code = 404 ) // TODO: could add a hook in here to allow for plugins
		{
			http_response_code( $code );
			echo $errorMessage;
			exit;
		}
}
