<?php



namespace Solenoid\HTTP;



class Retry
{
    public int $num_attempts;
    public int $time_interval;



    # Returns [self]
    public function __construct (int $num_attempts = 3, int $time_interval = 10)
    {
        // (Getting the values)
        $this->num_attempts  = $num_attempts;
        $this->time_interval = $time_interval;
    }

    # Returns [Retry]
    public static function create (int $num_attempts = 3, int $time_interval = 10)
    {
        // Returning the value
        return new Retry( $num_attempts, $time_interval );
    }
}



?>