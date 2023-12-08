<?php



namespace Solenoid\HTTP;



class URL
{
    public string  $protocol;

    public ?string $username;
    public ?string $password;

    public string  $host;
    public ?int    $port;

    public ?string $path;
    public ?string $query;
    public ?string $fragment;



    # Returns [self]
    public function __construct
    (
        string  $protocol,

        ?string $username,
        ?string $password,

        string  $host,
        ?int    $port,

        ?string $path,
        ?string $query,
        ?string $fragment
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

    # Returns [URL]
    public static function create
    (
        string  $protocol,

        ?string $username,
        ?string $password,

        string  $host,
        ?int    $port,

        ?string $path,
        ?string $query,
        ?string $fragment
    )
    {
        // Returning the value
        return new URL
        (
            $protocol,

            $username,
            $password,

            $host,
            $port,

            $path,
            $query,
            $fragment
        )
        ;
    }



    # Returns [URL]
    public static function parse (string $url)
    {
        // (Getting the value)
        $parts = parse_url( $url );



        // Returning the value
        return URL::create
        (
            $parts['scheme'],

            $parts['user'],
            $parts['pass'],

            $parts['host'],
            $parts['port'],

            $parts['path'],
            $parts['query'],
            $parts['fragment']
        )
        ;
    }



    # Returns [string]
    public function summarize ()
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
            ( $this->port ? ( ':' . $this->port ) : '' )
                .
            $this->path
                .
            ( $this->query ? ( '?' . $this->query ) : '' )
                .
            ( $this->fragment ? ( '#' . $this->fragment ) : '' )
        ;
    }

    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->summarize();
    }
}



?>