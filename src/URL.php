<?php



namespace Solenoid\HTTP;



class URL
{
    public string  $protocol;

    public ?string $username;
    public ?string $password;

    public string  $host;
    public ?int    $port;

    public string  $path;
    public ?string $query;
    public ?string $fragment;



    # Returns [self]
    public function __construct
    (
        string  $protocol,

        ?string $username = null,
        ?string $password = null,

        string  $host,
        ?int    $port     = null,

        string  $path,
        ?string $query    = null,
        ?string $fragment = null
    )
    {
        // (Getting the values)
        $this->protocol = $protocol;

        $this->username = $username;
        $this->password = $password;

        $this->host     = $host;
        $this->port     = $port;

        $this->path     = $path;
        $this->query    = $query;
        $this->fragment = $fragment;
    }



    # Returns [self]
    public static function parse (string $value)
    {
        // (Getting the value)
        $parts = parse_url($value);



        // Returning the value
        return new URL
        (
            $parts['scheme'],

            $parts['user'],
            $parts['pass'],

            $parts['host'],
            $parts['port'],

            $parts['path'] ?? '/',
            $parts['query'],
            $parts['fragment']
        )
        ;
    }

    # Returns [assoc]
    public static function parse_query (string $query)
    {
        // (Setting the value)
        $data = [];



        // (Getting the value)
        $kv_entries = explode( '&', $query );

        foreach ( $kv_entries as $kv_entry )
        {// Processing each entry
            // (Getting the value)
            $kv_parts = explode( '=', $kv_entry );

            if ( $kv_parts[0] === '' )
            {// Match OK
                // Continuing the iteration
                continue;
            }



            if ( count($kv_parts) === 1 )
            {// Match OK
                // (Setting the value)
                $kv_parts[1] = '';
            }



            // (Getting the value)
            $data[ rawurldecode( $kv_parts[0] ) ] = rawurldecode( $kv_parts[1] );
        }



        // Returning the value
        return $data;
    }



    # Returns [string]
    public function fetch_base ()
    {
        // Returning the value
        return
            $this->protocol
                .
            '://'
                .
            ( $this->username && $this->password ? ( $this->username . ':' . $this->password . '@' ) : '' )
                .
            $this->host
                .
            ( $this->port ? ( in_array( $this->port, [ 80, 443 ] ) ? '' : ( ':' . $this->port ) ) : '' )
        ;
    }

    # Returns [assoc]
    public function fetch_params ()
    {
        // Returning the value
        return $this->query === null ? [] : self::parse_query( $this->query );
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return
            $this->fetch_base()
                .
            $this->path
                .
            ( $this->query ? ( '?' . $this->query ) : '' )
                .
            ( $this->fragment ? ( '#' . $this->fragment ) : '' )
        ;
    }
}



?>