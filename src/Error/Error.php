<?php
namespace Salsa\Error;

class Error implements MainInterface {

		public $warningtypes = array(
		    'E_ERROR',
		    'E_WARNING',
		    'E_PARSE',
		    'E_NOTICE',
		    'E_CORE_ERROR',
		    'E_CORE_WARNING',
		    'E_COMPILE_ERROR',
		    'E_COMPILE_WARNING',
		    'E_USER_ERROR',
		    'E_USER_WARNING',
		    'E_USER_NOTICE',
		    'E_STRICT',
		    'E_RECOVERABLE_ERROR',
		    'E_DEPRECATED',
		    'E_USER_DEPRECATED',
		    'E_ALL',
		);

		public function __construct( ){

		}

		public static function exception( string $message, $code = 0 ){
			throw new Exception( $message, $code );
		}

		public static

}
