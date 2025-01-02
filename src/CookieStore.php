<?php



namespace Solenoid\HTTP;



use \Solenoid\HTTP\Cookie;



class CookieStore
{
    private static $values = [];



    # Returns [Session|false]
    public static function get (string $id)
    {
        // Returning the value
        return self::$values[ $id ] ?? false;
    }

    # Returns [void]
    public static function set (string $id, Cookie &$cookie)
    {
        // (Getting the value)
        self::$values[ $id ] = &$cookie;
    }
}



?>