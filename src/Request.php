<?php



namespace Solenoid\HTTP;



use \Solenoid\HTTP\URL;
use \Solenoid\HTTP\CurlRequest;
use \Solenoid\HTTP\Retry;



class Request
{
    private static self $instance;



    public string $client_ip;
    public int    $client_port;

    public string $server_name;
    public string $server_ip;
    public int    $server_port;

    public string $proxy_client_ip;

    public string $method;

    public array  $headers;
    public string $body;

    public array  $cookies;

    public string $base_url;
    public URL $url;



    # Returns [self]
    private function __construct ()
    {
        // (Getting the value)
        $this->headers = getallheaders();



        // (Getting the values)
        $this->client_ip       = $_SERVER['REMOTE_ADDR'];
        $this->client_port     = $_SERVER['REMOTE_PORT'];



        // (Getting the values)
        $this->server_name     = $_SERVER['SERVER_NAME'];
        $this->server_ip       = self::resolve( $this->server_name );
        $this->server_port     = $_SERVER['SERVER_PORT'];

        $this->proxy_client_ip = $this->headers['X-Forwarded-For'] ?? '';



        // (Getting the values)
        $this->method  = $_SERVER['REQUEST_METHOD'];
        $this->body    = file_get_contents( 'php://input' );
        $this->cookies = &$_COOKIE;



        // (Getting the values)
        $this->url = new URL
        (
            ( isset( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) === 'on' )  ? 'https' : 'http',
            null,
            null,
            $this->server_name,
            $this->server_port,
            preg_replace( '/\?[^\?]*$/', '', $_SERVER['REQUEST_URI'] ),
            explode( '?', $_SERVER['REQUEST_URI'] )[1]
        )
        ;
    }



    # Returns [self|false]
    public static function fetch ()
    {
        if ( !isset( $_SERVER ) )
        {// (PHP is not running under webserver mode)
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



    # Returns [string]
    public static function resolve (string $fqdn)
    {
        // (Getting the value)
        $ip = dns_get_record( $fqdn, DNS_A );

        if ( !$ip )
        {// (Unable to resolve the FQDN)
            // Returning the value
            return '';
        }



        // Returning the value
        return $ip[0]['ip'];
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
    public function fetch_route ()
    {
        // Returning the value
        return $this->method . ' ' . $this->path . ( $this->query ? '?' . $this->query : '' );
    }



    # Returns [assoc]
    public function to_array ()
    {
        // Returning the value
        return json_decode( json_encode($this), true );
    }



    # Returns [string]
    public function __toString ()
    {
        // (Getting the values)
        $client = ( $this->proxy_client_ip ? $this->proxy_client_ip . ' via ' : '' ) . $this->client_ip;
        $route  = self::fetch_route();



        // (Setting the value)
        $base_headers = [];

        foreach ( [ 'Action', 'User-Agent' ] as $type )
        {// Processing each entry
            if ( isset( $this->headers[$type] ) )
            {// Value found
                // (Appending the value)
                $base_headers[] = '"' . $this->headers[$type] . '"';
            }
        }

        // (Getting the value)
        $base_headers = implode( ', ', $base_headers );



        // Returning the value
        return implode( ' - ', [ $client, $route, http_response_code(), $base_headers ] );
    }
}



?>