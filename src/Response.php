<?php



namespace Solenoid\HTTP;



use \Solenoid\HTTP\Status;



class Response
{
    public Status $status;

    public array  $headers;
    public mixed  $body;



    # Returns [self]
    public function __construct (?Status $status = null, array $headers = [], mixed $body = '')
    {
        // (Getting the values)
        $this->status  = $status ?? new Status();
        $this->headers = $headers;
        $this->body    = $body;
    }
}



?>