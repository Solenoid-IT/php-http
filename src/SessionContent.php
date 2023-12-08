<?php



namespace Solenoid\HTTP;



class SessionContent
{
    public int   $creation;
    public ?int  $expiration;

    public array $data;



    # Returns [self]
    public function __construct (int $creation, ?int $expiration, array $data)
    {
        // (Getting the values)
        $this->creation   = $creation;
        $this->expiration = $expiration;

        $this->data       = $data;
    }

    # Returns [Content]
    public static function create (int $creation, ?int $expiration, array $data)
    {
        // Returning the value
        return new SessionContent( $creation, $expiration, $data );
    }



    # Returns [assoc]
    public function to_array ()
    {
        // Returning the value
        return get_object_vars( $this );
    }
}



?>