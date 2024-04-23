<?php



namespace Solenoid\HTTP;



use \Solenoid\HTTP\CurlRequest;
use \Solenoid\HTTP\Retry;



class Request
{
    private static Request $instance;



    public static string $client_ip;
    public static string $server_ip;
    public static string $proxy_client_ip;

    public static string $protocol;
    public static string $host;
    public static string $port;

    public static string $path;
    public static string $query;

    public static string $method;

    public static array  $headers;
    public static string $body;

    public static array  $cookies;

    public static string $base_url;
    public static string $url;



    # Returns [self]
    private function __construct ()
    {
        // (Getting the values)
        $host      = self::get_host();

        $server_ip = dns_get_record( $host, DNS_A );
        $server_ip = $server_ip ? $server_ip[0]['ip'] : '';

        $headers   = getallheaders();



        // (Getting the values)
        self::$client_ip       = $_SERVER['REMOTE_ADDR'];
        self::$server_ip       = $server_ip;
        self::$proxy_client_ip = $headers['X-Forwarded-For'] ?? '';

        self::$protocol        = ( isset( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) === 'on' )  ? 'https' : 'http';
        self::$host            = $host;
        self::$port            = $_SERVER['SERVER_PORT'];

        self::$path            = preg_replace( '/\?[^\?]*$/', '', $_SERVER['REQUEST_URI'] );
        self::$query           = explode( '?', $_SERVER['REQUEST_URI'] )[1] ?? '';

        self::$method          = $_SERVER['REQUEST_METHOD'];

        self::$headers         = $headers;
        self::$body            = file_get_contents( 'php://input' );

        self::$cookies         = $_COOKIE;

        self::$base_url        = self::$protocol . '://' . self::$host . ( in_array( self::$port, [ 80, 443 ] ) ? '' : ':' . self::$port );
        self::$url             = self::$base_url . $_SERVER['REQUEST_URI'];
    }



    # Returns [Request|false]
    public static function read ()
    {
        if ( !self::exists() )
        {// (Request not found)
            // Returning the vaiue
            return false;
        }



        if ( isset( self::$instance ) )
        {// Value found
            // Returning the value
            return self::$instance;
        }



        // (Creating a Request)
        self::$instance = new Request();



        // Returning the value
        return self::$instance;
    }



    # Returns [assoc]
    public static function parse_query (?string $query = null)
    {
        if ( $query === null && isset( self::$query ) ) $query = self::$query;



        // (Setting the value)
        $parsed = [];

        // (Getting the value)
        $kv_entries = explode( '&', $query );

        foreach ($kv_entries as $kv_entry)
        {// Processing each entry
            // (Getting the value)
            $kv_parts = explode( '=', $kv_entry );

            if ( $kv_parts[0] === '' )
            {// Match OK
                // Continuing the iteration
                continue;
            }



            if ( count( $kv_parts ) === 1 )
            {// Match OK
                // (Setting the value)
                $kv_parts[1] = '';
            }



            // (Getting the value)
            $parsed[ rawurldecode( $kv_parts[0] ) ] = rawurldecode( $kv_parts[1] );
        }



        // Returning the value
        return $parsed;
    }



    # Returns [bool]
    public static function exists ()
    {
        // Returning the value
        return isset( $_SERVER['REQUEST_METHOD'] );
    }



    # Returns [(CurlResponse|string)|false|null] | Throws [Exception]
    public static function send
    (
        string $url,
        string $method        = 'GET',
        array  $headers       = [],
        string $body          = '',

        string $response_type = '',

        bool   $raw           = false,
        array  $options       = [],

        ?Retry $retry         = null
    )
    {
        // Returning the value
        return
            CurlRequest::create
            (
                $url,
                $method,
                $headers,
                $body,

                $response_type,

                $raw,
                $options
            )
                ->send( $retry )
        ;
    }

    # Returns [bool]
    public static function forward (string $url, ?Retry $retry = null)
    {
        // (Getting the value)
        $headers                    = self::$headers;
        $headers['X-Forwarded-For'] = self::$client_ip;



        // (Removing the element)
        unset( $headers['Host'] );



        // (Sending a request)
        $response = self::send
        (
            $url,
            self::$method,
            $headers,
            self::$body,

            '',
            false,
            [],

            $retry
        )
        ;

        if ( $response === null || $response === false )
        {// (Request failed)
            // Returning the value
            return false;
        }



        // (Setting the response code)
        http_response_code( $response->status->code );

        // Printing the value
        echo $response->body;



        // Returning the value
        return true;
    }



    # Returns [string]
    public static function get_host ()
    {
        // Returning the value
        return $_SERVER['SERVER_NAME'];
    }

    # Returns [string]
    public static function get_route (bool $exclude_method = false)
    {
        // (Setting the value)
        $components = [];



        if ( !$exclude_method )
        {// Match OK
            // (Appending the value)
            $components[] = strtoupper( self::$method );
        }

        // (Appending the value)
        $components[] = self::$path . ( self::$query ? '?' . self::$query : '' );



        // Returning the value
        return implode( ' ', $components );
    }



    # Returns [assoc]
    public static function to_array ()
    {
        // Returning the value
        return
        [
            'client_ip'       => self::$client_ip,
            'server_ip'       => self::$server_ip,
            'proxy_client_ip' => self::$proxy_client_ip,

            'protocol'        => self::$protocol,
            'host'            => self::$host,
            'port'            => self::$port,

            'path'            => self::$path,
            'query'           => self::$query,

            'method'          => self::$method,

            'headers'         => self::$headers,
            'body'            => self::$body,

            'origin'          => self::$base_url,
            'url'             => self::$url
        ]
        ;
    }



    # Returns [string]
    public static function summarize ()
    {
        // Returning the value
        return ( self::$proxy_client_ip ? self::$proxy_client_ip . ' via ' : '' ) . self::$client_ip . ' - ' . self::get_route() . ' -> ' . '"' . self::$headers['Action'] ?? '' . '"' . ' - ' . http_response_code() . ' - ' . '"' . self::$headers['User-Agent'] . '"';
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->summarize();
    }
}



?>