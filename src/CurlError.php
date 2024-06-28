<?php



namespace Solenoid\HTTP;



class CurlError
{
    public int    $code;
    public string $message;
    public string $description;



    # Returns [self]
    public function __construct (int $code, string $message = '', string $description = '')
    {
        // (Getting the value)
        $this->code        = $code;
        $this->message     = $message;
        $this->description = $description;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return implode( ' :: ', $this->to_array() );
    }
}



?>