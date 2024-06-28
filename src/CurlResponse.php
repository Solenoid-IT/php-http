<?php



namespace Solenoid\HTTP;



use \Solenoid\HTTP\CurlError;
use \Solenoid\HTTP\Status;
use \Solenoid\HTTP\Response;



class CurlResponse extends Response
{
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
        // (Calling the function)
        parent::__construct( $status, $headers, $body );



        // (Getting the value)
        $this->error   = $error;
        $this->info    = $info;
    }
}



?>