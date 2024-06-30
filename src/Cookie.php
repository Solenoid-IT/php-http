<?php



namespace Solenoid\HTTP;



class Cookie
{
    public string       $name;

    public string     $domain;
    public string       $path;

    public bool       $secure;
    public bool    $http_only;

    public ?string $same_site;



    # Returns [self]
    public function __construct
    (
        string  $name,

        string  $domain    = '',
        string  $path      = '/',

        bool    $secure    = false,
        bool    $http_only = false,

        ?string $same_site = 'Lax'
    )
    {
        // (Getting the values)
        $this->name      = $name;

        $this->domain    = $domain;
        $this->path      = $path;

        $this->secure    = $secure;
        $this->http_only = $http_only;

        $this->same_site = $same_site;
    }



    # Returns [bool] | Throws [Exception]
    public function set (string $value, ?int $expiration = null)
    {
        // (Getting the value)
        $components =
        [
            'Expires'  => $expiration === null ? '' : 'Expires=' . gmdate( DATE_COOKIE, $expiration ) . ';'
            ,
            'Domain'   => $this->domain ? 'Domain=' . $this->domain . ';' : ''
            ,
            'Path'     => $this->path ? 'Path=' . $this->path . ';' : ''
            ,
            'Secure'   => $this->secure ? 'Secure;' : ''
            ,
            'HttpOnly' => $this->http_only ? 'HttpOnly;' : ''
            ,
            'SameSite' => $this->same_site === null ? '' : 'SameSite=' . $this->same_site . ';'
        ]
        ;

        $components = trim( implode( ' ', array_values( array_filter( $components, function ($component) { return $component !== ''; } ) ) ) );



        // (Getting the values)
        $k = $this->name;
        $v = $value;



        // (Getting the value)
        $header = "Set-Cookie: $k=$v; $components";

        // (Setting the header)
        header( $header, false );



        if ( !in_array( $header, headers_list() ) )
        {// (Unable to set the cookie)
            // (Setting the value)
            $message = "Unable to set the cookie";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    # Returns [string|false]
    public static function fetch_value (string $name)
    {
        if ( !isset( $_COOKIE[ $name ] ) )
        {// Value found
            return false;
        }



        // Returning the value
        return $_COOKIE[ $name ];
    }

    # Returns [bool] | Throws [Exception]
    public static function delete (string $name, string $domain = '', string $path = '')
    {
        // (Setting the cookie)
        $result = ( new Cookie( $name, $domain, $path ) )->set( '', -1 );

        if ( !$result )
        {// (Unable to set the cookie)
            // (Setting the value)
            $message = "Unable to set the cookie";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }
}



?>