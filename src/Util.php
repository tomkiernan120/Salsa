<?php

namespace Salsa;


/**
 * Util / Helper functions
 */
trait Util
{
    /**
     * summary
     */
    public static function isHTML( string $string )
    {
   		return ( $string != strip_tags( $string ) );
    }


    public static function generateUrl( string $string )
    {
    	$url = preg_replace( "/[^a-zA-Z\/-]/", "", strtolower($string) );
        $url = preg_replace( "/-{2,}/", "-", $url );
        $url = ltrim( $url, "/" );
    	return $url;	
    }
    
}