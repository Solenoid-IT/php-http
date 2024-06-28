<?php



namespace Solenoid\HTTP;



class Status
{
    public int    $code;
    public string $message;



    # Returns [self]
    public function __construct (int $code = 200, string $message = 'OK')
    {
        // (Getting the value)
        $this->code    = $code;
        $this->message = $message;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return "$this->code $this->message";
    }
}



?>