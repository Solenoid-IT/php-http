<?php



namespace Solenoid\HTTP;



use \Solenoid\HTTP\CurlRequest;
use \Solenoid\HTTP\Retry;



class Request
{
    private static self $instance;



    public string $client_ip;
    public string $server_ip;
    public string $proxy_client_ip;

    public string $protocol;
    public string $host;
    public string $port;

    public string $path;
    public string $query;

    public string $method;

    public array  $headers;
    public string $body;

    public array  $cookies;

    public string $base_url;
    public string $url;



    # Returns [self]
    private function __construct ()
    {
        // (Getting the values)
        $host      = $this->get_host();

        $server_ip = dns_get_record( $host, DNS_A );
        $server_ip = $server_ip ? $server_ip[0]['ip'] : '';

        $headers   = getallheaders();



        // (Getting the values)
        $this->client_ip       = $_SERVER['REMOTE_ADDR'];
        $this->server_ip       = $server_ip;
        $this->proxy_client_ip = $headers['X-Forwarded-For'] ?? '';

        $this->protocol        = ( isset( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) === 'on' )  ? 'https' : 'http';
        $this->host            = $host;
        $this->port            = $_SERVER['SERVER_PORT'];

        $this->path            = preg_replace( '/\?[^\?]*$/', '', $_SERVER['REQUEST_URI'] );
        $this->query           = explode( '?', $_SERVER['REQUEST_URI'] )[1] ?? '';

        $this->method          = $_SERVER['REQUEST_METHOD'];

        $this->headers         = $headers;
        $this->body            = file_get_contents( 'php://input' );

        $this->cookies         = $_COOKIE;

        $this->base_url        = $this->protocol . '://' . $this->host . ( in_array( $this->port, [ 80, 443 ] ) ? '' : ':' . $this->port );
        $this->url             = $this->base_url . $_SERVER['REQUEST_URI'];
    }



    # Returns [Request|false]
    public static function read ()
    {
        if ( !self::exists() )
        {// (Request not found)
            // Returning the vaiue
            return false;
        }



        if ( !isset( self::$instance ) )
        {// Value not found
            // (Creating a Request)
            self::$instance = new Request();
        }



        // Returning the value
        return self::$instance;
    }



    # Returns [assoc]
    public function parse_query (?string $query = null)
    {
        if ( $query === null && isset( $this->query ) ) $query = $this->query;



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
    public function forward (string $url, ?Retry $retry = null)
    {
        // (Getting the value)
        $headers                    = $this->headers;
        $headers['X-Forwarded-For'] = $this->client_ip;



        // (Removing the element)
        unset( $headers['Host'] );



        // (Sending a request)
        $response = self::send
        (
            $url,
            $this->method,
            $headers,
            $this->body,

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
    public function get_host ()
    {
        // Returning the value
        return $_SERVER['SERVER_NAME'];
    }

    # Returns [string]
    public function get_route (bool $exclude_method = false)
    {
        // (Setting the value)
        $components = [];



        if ( !$exclude_method )
        {// Match OK
            // (Appending the value)
            $components[] = strtoupper( $this->method );
        }

        // (Appending the value)
        $components[] = $this->path . ( $this->query ? '?' . $this->query : '' );



        // Returning the value
        return implode( ' ', $components );
    }



    # Returns [assoc]
    public function to_array ()
    {
        // Returning the value
        return
        [
            'client_ip'       => $this->client_ip,
            'server_ip'       => $this->server_ip,
            'proxy_client_ip' => $this->proxy_client_ip,

            'protocol'        => $this->protocol,
            'host'            => $this->host,
            'port'            => $this->port,

            'path'            => $this->path,
            'query'           => $this->query,

            'method'          => $this->method,

            'headers'         => $this->headers,
            'body'            => $this->body,

            'origin'          => $this->base_url,
            'url'             => $this->url
        ]
        ;
    }



    # Returns [string]
    public function summarize ()
    {
        // Returning the value
        return ( $this->proxy_client_ip ? $this->proxy_client_ip . ' via ' : '' ) . $this->client_ip . ' - ' . self::get_route() . ' -> ' . '"' . ( $this->headers['Action'] ?? '' ) . '"' . ' - ' . http_response_code() . ' - ' . '"' . $this->headers['User-Agent'] . '"';
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->summarize();
    }
}



?>