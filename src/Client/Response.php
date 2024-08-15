<?php



namespace Solenoid\HTTP\Client;



use \Solenoid\HTTP\Client\RequestError;



class Response
{
    public array        $heads;
    public mixed        $body;

    public RequestError $error;
    public array        $info;



    # Returns [self]
    public function __construct (array $heads, mixed $body, RequestError $error, array $info)
    {
        // (Getting the values)
        $this->heads     = $heads;
        $this->body      = $body;

        $this->error     = $error;
        $this->info      = $info;
    }



    # Returns [Head|null]
    public function fetch_head ()
    {
        // Returning the value
        return $this->heads[ 0 ];
    }

    # Returns [Head|null]
    public function fetch_tail ()
    {
        // Returning the value
        return $this->heads[ count( $this->heads ) - 1 ];
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return json_encode( $this, JSON_PRETTY_PRINT );
    }
}



?>