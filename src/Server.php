<?php



namespace Solenoid\HTTP;



use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Response;



class Server
{
    public static ?Response $response = null;



    # Returns [void]
    public static function set_status_code (int $code)
    {
        // (Setting the response code)
        http_response_code( $code );
    }



    # Returns [void]
    public static function send_header (string $value, bool $replace = true)
    {
        // (Sending the header)
        header( $value, $replace );
    }

    # Returns [void]
    public static function send_headers (array $values, bool $replace = true)
    {
        foreach ($values as $value)
        {// Processing each entry
            // (Sending the header)
            self::send_header( $value, $replace );
        }
    }



    # Returns [void]
    public static function set_cors
    (
        array $origins     = [],

        array $methods     = [],
        array $headers     = [],

        bool  $credentials = false
    )
    {
        // (Getting the value)
        $current_origin = Request::read()->headers['Origin'];

        if ( !$current_origin || ( $origins && !in_array( $current_origin, $origins ) ) )
        {// Match failed
            // Returning the value
            return;
        }



        // (Getting the values)
        $origin      = $current_origin;
        $methods     = $methods ? implode( ', ', $methods ) : '*';
        $headers     = $headers ? implode( ', ', $headers ) : '*';
        $credentials = $credentials ? 'true' : 'false';



        // (Setting the headers)
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: $methods");
        header("Access-Control-Allow-Headers: $headers");
        header("Access-Control-Allow-Credentials: $credentials");
    }



    # Returns [void]
    public static function send_message (string $message, bool $flush = false)
    {
        // (Printing the value)
        echo $message;



        if ( $flush )
        {// Value is true
            // Printing the value
            echo str_repeat( ' ', 64 * 1024 );



            // (Flushing the output buffer)
            ob_end_flush();
            flush();
        }
    }



    # Returns [void]
    public static function send (Response $response)
    {
        if ( isset( Server::$response ) )
        {// (Server has already sent a response to the client)
            // Returning the value
            return;
        }



        // (Setting the http status)
        self::set_status_code( $response->status_code );



        foreach ($response->headers as $k => $v)
        {// Processing each entry
            // (Sending the headers)
            self::send_header( "$k: $v" );
        }



        // Printing the value
        echo $response->data;



        // (Setting the value)
        self::$response = &$response;
    }
}



?>