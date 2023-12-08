<?php



namespace Solenoid\HTTP;



class Status
{
    public int    $code;
    public string $message;



    # Returns [void]
    public function __construct (int $code, string $message = '')
    {
        // (Getting the value)
        $this->code    = $code;
        $this->message = $message;
    }

    # Returns [CurlError]
    public static function create (int $code, string $message = '')
    {
        // Returning the value
        return new Status( $code, $message );
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
        return "$this->code :: $this->message";
    }
}



?>