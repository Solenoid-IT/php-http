<?php



namespace Solenoid\HTTP\Client;



class RequestError
{
    public int    $code;
    public string $message;



    # Returns [self]
    public function __construct (int $code, string $message)
    {
        // (Getting the values)
        $this->code    = $code;
        $this->message = $message;
    }



    # Returns [string]
    public function __toString()
    {
        // Returning the value
        return implode( ' ', [ $this->code, $this->message ] );
    }
}



?>