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
    
}