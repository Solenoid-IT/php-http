<?php



namespace Solenoid\HTTP\Client;



use \Solenoid\HTTP\Status;



class ResponseHead
{
    public string $protocol;
    public Status $status;
    public array  $headers;



    # Returns [self]
    public function __construct (string $protocol, Status $status, array $headers)
    {
        // (Getting the value)
        $this->protocol = $protocol;
        $this->status   = $status;
        $this->headers  = $headers;
    }



    # Returns [string|null]
    public function get (string $key)
    {
        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Getting the value)
            $parts = explode( ': ', $header, 2 );

            if ( $parts[0] === $key )
            {// Match OK
                // Returning the value
                return $parts[1];
            }
        }



        // Returning the value
        return null;
    }

    # Returns [array<string>]
    public function get_all (string $key)
    {
        // (Setting the value)
        $values = [];

        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Getting the value)
            $parts = explode( ': ', $header, 2 );

            if ( $parts[0] === $key )
            {// Match OK
                // (Appending the value)
                $values[] = $parts[1];
            }
        }



        // Returning the value
        return $values;
    }
}



?>