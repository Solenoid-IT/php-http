<?php



namespace Solenoid\HTTP;



use \Solenoid\HTTP\Cookie;



class Session
{
    private array  $handlers;
    private Cookie $cookie;
    private ?int   $duration;
    private bool   $persistent;



    public ?string $found_id;
    public ?string $generated_id;
    public ?string $destroyed_id;

    public ?string $id;

    public int     $creation;
    public ?int    $expiration;

    public array   $data;

    public ?int    $writing;



    # Returns [self]
    public function __construct (array $handlers, Cookie $cookie, ?int $duration = null, bool $persistent = false)
    {
        // (Getting the values)
        $this->handlers   = $handlers;
        $this->cookie     = $cookie;
        $this->duration   = $duration;
        $this->persistent = $persistent;



        // (Setting the values)
        $this->found_id      = null;
        $this->generated_id  = null;
        $this->destroyed_id  = null;

        $this->id            = null;

        $this->writing       = null;
    }

    # Returns [Session]
    public static function create (array $handlers, Cookie $cookie, ?int $duration = null, bool $persistent = false)
    {
        // Returning the value
        return new Session( $handlers, $cookie, $duration, $persistent );
    }



    # Returns [self|false] | Throws [Exception]
    public function start ()
    {
        // (Getting the value)
        $id = Cookie::fetch_value( $this->cookie->name );

        if ( $id === false )
        {// (Cookie not found)
            // (Calling the function)
            $id = $this->handlers[ 'generate_id' ]();

            // (Calling the function)
            $content = $this->handlers[ 'init' ]( $id, $this->duration );



            // (Getting the value)
            $this->generated_id = $id;
        }
        else
        {// (Cookie found)
            // (Calling the function)
            $result = $this->handlers[ 'validate_id' ]( $id );

            if ( !$result )
            {// (Validation is failed)
                // Returning the value
                return false;
            }



            // (Calling the function)
            $content = $this->handlers[ 'read' ]( $id, $this->duration );



            // (Getting the value)
            $this->found_id = $id;
        }



        // (Getting the value)
        $this->id         = $id;

        $this->creation   = $content->creation;
        $this->expiration = $content->expiration;

        $this->data       = $content->data;



        if ( $this->generated_id )
        {// Value found
            // (Setting the cookie)
            $this->cookie->set( $id, $this->persistent ? $this->expiration : null );
        }



        // Returning the value
        return $this;
    }

    # Returns [self|false] | Throws [Exception]
    public function regenerate_id ()
    {
        if ( $this->generated_id )
        {// (ID has been already generated)
            // Returning the value
            return $this;
        }



        // (Calling the function)
        $new_id = $this->handlers[ 'generate_id' ]();



        // (Calling the function)
        $this->handlers[ 'change_id' ]( $this->id, $new_id );

        // (Getting the value)
        $this->id = $new_id;



        // (Setting the cookie)
        $this->cookie->set( $this->id, $this->persistent ? $this->expiration : null );



        // Returning the value
        return $this;
    }

    # Returns [self]
    public function set_duration (?int $duration = null)
    {
        // (Calling the function)
        $this->expiration = $this->handlers[ 'set_expiration' ]( $duration ?? $this->duration );



        // (Setting the cookie)
        $this->cookie->set( $this->id, $this->persistent ? $this->expiration : null );



        // Returning the value
        return $this;
    }



    # Returns [self|false] | Throws [Exception]
    public function destroy ()
    {
        // (Calling the function)
        $this->handlers[ 'destroy' ]( $this->id );



        // (Setting the cookie)
        $this->cookie->set( $this->id, -1 );



        // (Getting the value)
        $this->destroyed_id = $this->id;



        // (Setting the value)
        $this->id = null;



        // Returning the value
        return $this;
    }



    # Returns [self|false] | Throws [Exception]
    public function write ()
    {
        if ( $this->id === null )
        {// (Session has not been started)
            // Returning the value
            return false;
        }

        if ( $this->writing )
        {// (Content has been already written)
            // Returning the value
            return false;
        }

        if ( $this->destroyed_id !== null )
        {// (Session has been destroyed)
            // Returning the value
            return false;
        }



        // (Calling the function)
        $this->handlers[ 'write' ]( $this->id, SessionContent::create( $this->creation, $this->expiration, $this->data ) );



        // Returning the value
        return $this;
    }



    # Returns [void]
    public function __destruct ()
    {
        // (Writing the content)
        $this->write();
    }
}



?>