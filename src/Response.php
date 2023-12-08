<?php



namespace Solenoid\HTTP;



class Response
{
    public int    $status_code;

    public array  $headers;
    public string $data;



    # Returns [self]
    public function __construct
    (
        int    $status_code = 200,

        array  $headers     = [],
        string $data        = ''
    )
    {
        // (Getting the values)
        $this->status_code = $status_code;

        $this->headers     = $headers;
        $this->data        = $data;
    }

    # Returns [Response]
    public static function create
    (
        int    $status_code = 200,

        array  $headers     = [],
        string $data        = ''
    )
    {
        // Returning the value
        return
            new Response
            (
                $status_code,

                $headers,
                $data
            )
        ;
    }
}



?>