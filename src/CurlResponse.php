<?php



namespace Solenoid\HTTP;



class CurlResponse
{
    public Status    $status;
    public array     $headers;
    public           $body;

    public CurlError $error;
    public array     $info;



    # Returns [self]
    public function __construct
    (
        Status    $status,
        array     $headers, 
                  $body,

        CurlError $error, 
        array     $info
    )
    {
        // (Getting the values)
        $this->status  = $status;
        $this->headers = $headers;
        $this->body    = $body;

        $this->error   = $error;
        $this->info    = $info;
    }

    # Returns [CurlResponse]
    public static function create
    (
        Status    $status,
        array     $headers, 
                  $body,

        CurlError $error, 
        array     $info
    )
    {
        // Returning the value
        return
            new CurlResponse
            (
                $status,
                $headers,
                $body,

                $error,
                $info
            )
        ;
    }



    # Returns [array<string>]
    public function to_array ()
    {
        // Returning the value
        return get_object_vars( $this );
    }

    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return json_encode( $this->to_array() );
    }
}



?>