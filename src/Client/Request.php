<?php



namespace Solenoid\HTTP\Client;



use \Solenoid\HTTP\Client\Client;



class Request
{
    const LINE_SEPARATOR = "\r\n";



    public string $method;
    public string $path;
    public string $protocol;
    public array  $headers;
    public string $body;



    # Returns [self]
    public function __construct (string $method = 'GET', string $path = '/', string $protocol = 'HTTP/1.1', array $headers = [], string $body = '')
    {
        // (Getting the values)
        $this->method   = $method;
        $this->path     = $path;
        $this->protocol = $protocol;
        $this->headers  = $headers;
        $this->body     = $body;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return implode
        (
            self::LINE_SEPARATOR,
            [
                "{$this->method} {$this->path} HTTP/{$this->protocol}",
                implode( self::LINE_SEPARATOR, $this->headers ),
                self::LINE_SEPARATOR,
                $this->body
            ]
        )
        ;
    }



    # Returns [self]
    public static function parse (string $value)
    {
        // (Getting the values)
        [ $head, $body ] = explode( self::LINE_SEPARATOR . self::LINE_SEPARATOR, $value, 2 );

        // (Getting the value)
        $headers = explode( self::LINE_SEPARATOR, $head );

        // (Getting the values)
        [ $method, $path, $protocol ] = explode( ' ', $headers[0], 3 );



        // (Setting the value)
        $real_headers = [];

        for ( $i = 1; $i < count( $headers ); $i++ )
        {// Iterating each index
            // (Appending the value)
            $real_headers[] = $headers[$i];
        }



        // Returning the value
        return new self( $method, $path, $protocol, $real_headers, $body );
    }



    # Returns [Response|false]
    public function send (string $protocol, string $host)
    {
        // Returning the value
        return Client::send( "$protocol://$host{$this->path}", $this->method, $this->headers, $this->body );
    }
}



?>