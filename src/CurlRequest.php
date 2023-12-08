<?php



namespace Solenoid\HTTP;



class CurlRequest
{
    const STATUS_MESSAGES =
    [
        0   => 'Curl Error',

        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required'
    ]
    ;



    public string $url;
    public string $method;
    public array  $headers;
    public string $body;

    public string $response_type;

    public bool   $raw;

    public array  $options;



    # Returns [self]
    public function __construct
    (
        string $url,
        string $method        = 'GET',
        array  $headers       = [],
        string $body          = '',

        string $response_type = '',

        bool   $raw           = false,

        array  $options       = []
    )
    {
        // (Getting the values)
        $this->url           = $url;
        $this->method        = $method;
        $this->headers       = $headers;
        $this->body          = $body;

        $this->response_type = $response_type;

        $this->raw           = $raw;

        $this->options       = $options;
    }

    # Returns [CurlRequest]
    public static function create
    (
        string $url,
        string $method        = 'GET',
        array  $headers       = [],
        string $body          = '',

        string $response_type = '',

        bool   $raw           = false,

        array  $options       = []
    )
    {
        // Returning the value
        return new CurlRequest
        (
            $url,
            $method,
            $headers,
            $body,

            $response_type,

            $raw,

            $options
        )
        ;
    }



    # Returns [array<string>]
    public static function compact_headers (array $headers)
    {
        // (Setting the value)
        $header_list = [];

        foreach ($headers as $k => $v)
        {// Processing each entry
            // (Appending the value)
            $header_list[] = "$k: $v";
        }



        // Returning the value
        return $header_list;
    }



    # Returns [(CurlResponse|string)|false|null] | Throws [Exception]
    public function send (?Retry $retry = null)
    {
        // (Initializing a cURL)
        $curl = curl_init();

        if ( $curl === false )
        {// (Unable to initialize the curl)
            // (Setting the value)
            $message = "Unable to initialize the curl";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return null;
        }



        // (Setting the options)
        $result = curl_setopt_array
        (
            $curl,
            [
                CURLOPT_URL            => $this->url,
                CURLOPT_CUSTOMREQUEST  => $this->method,
                CURLOPT_HTTPHEADER     => self::compact_headers($this->headers),
                CURLOPT_POSTFIELDS     => $this->body,

                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,

                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false
             ]
        )
        ;

        if ( !$result )
        {// (Unable to set the curl options)
            // (Setting the value)
            $message = "Unable to set the curl options";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return null;
        }



        // (Getting the value)
        $num_attempts  = 1 + ( $retry ? $retry->num_attempts : 0 );
        $time_interval = $retry ? $retry->time_interval : 0;



        while ( $num_attempts > 0 )
        {// Processing each entry
            // (Decrementing the value)
            $num_attempts -= 1;



            // (Executing the curl request)
            $output = curl_exec($curl);



            // (Getting the value)
            $error_code = curl_errno( $curl );

            if ( $error_code !== 28 )
            {// (CURL error code is not CURLE_OPERATION_TIMEDOUT)
                // (Getting the value)
                $info = curl_getinfo( $curl );

                if ( $info === false )
                {// (Unable to get the curl info)
                    // (Setting the value)
                    $message = "Unable to get the curl info";

                    // Throwing an exception
                    throw new \Exception($message);

                    // Returning the value
                    return null;
                }



                // (Setting the values)
                $headers = [];
                $body    = '';



                if ( $info['http_code'] !== 0 )
                {// (There is no an error)
                    // (Getting the value)
                    #list( $head, $body ) = explode( "\r\n\r\n", $output );
                    $parts = explode( "\r\n\r\n", $output );

                    $head = $parts[ count( $parts ) - 2 ];
                    $body = $parts[ count( $parts ) - 1 ];



                    // (Getting the value)
                    $headers = explode( "\r\n", $head );

                    // (Shifting the array)
                    array_shift( $headers );



                    // (Setting the value)
                    $h = [];

                    foreach ($headers as $header)
                    {// Processing each entry
                        // (Getting the value)
                        list( $k, $v ) = explode( ': ', $header );

                        // (Getting the value)
                        $h[ $k ] = $v;
                    }



                    // (Getting the value)
                    $headers = $h;



                    if ( $this->response_type === 'json' )
                    {// Match OK
                        // (Getting the value)
                        $body = json_decode( $body, true );
                    }
                }



                // Returning the value
                return
                    $this->raw
                        ?
                    $output
                        :
                    (
                        CurlResponse::create
                        (
                            Status::create( $info['http_code'], self::STATUS_MESSAGES[ $info['http_code'] ] ?? '' ),
                            $headers,
                            $body,

                            CurlError::create
                            (
                                $error_code,
                                curl_strerror( $error_code ),
                                curl_error( $curl )
                            ),
                            $info
                        )
                    )
                ;
            }



            // (Waiting for the seconds)
            sleep( $time_interval );
        }



        // Returning the value
        return false;
    }
}



?>