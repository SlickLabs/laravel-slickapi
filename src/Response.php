<?php

namespace SlickLabs\Laravel\SlickAPI;

/**
 * Dashboard API response object
 *
 * Custom response object to use with the the dashboard API.
 *
 * @author Leo Flapper
 * @version 1.0.0
 */
class Response
{
    /**
     * Map of standard HTTP status code/reason phrases.
     * @var array Map of standard HTTP status code/reason phrases.
     */
    private static $phrases = [
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
        511 => 'Network Authentication Required',
    ];

    private $body;

    /** @var null|string */
    private $reasonPhrase = '';

    /** @var int */
    private $statusCode = 200;

    private $headers = [];

    /** @var array Actual key to list of values per header. */
    private $headerLines = [];

    /** @var string */
    private $protocol = '1.1';

    /**
     * @param int    $status  Status code for the response, if any.
     * @param array  $headers Headers for the response, if any.
     * @param mixed  $body    Stream body.
     * @param string $version Protocol version.
     * @param string $reason  Reason phrase (a default will be used if possible).
     */
    public function __construct(
        $status = 200,
        array $headers = [],
        $body = null,
        $version = '1.1',
        $reason = null
    ) {
        $this->statusCode = (int) $status;

        if ($body !== null) {
            $this->setBody($body);
        }

        $this->setHeaders($headers);
        if (!$reason && isset(self::$phrases[$this->statusCode])) {
            $this->reasonPhrase = self::$phrases[$status];
        } else {
            $this->reasonPhrase = (string) $reason;
        }

        $this->protocol = $version;
    }

    private function setBody($body)
    {
        $this->body = $body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        $new->statusCode = (int) $code;
        if (!$reasonPhrase && isset(self::$phrases[$new->statusCode])) {
            $reasonPhrase = self::$phrases[$new->statusCode];
        }
        $new->reasonPhrase = $reasonPhrase;
        return $new;
    }

    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version)
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }

    public function getHeaders()
    {
        return $this->headerLines;
    }

    public function hasHeader($header)
    {
        return isset($this->headers[strtolower($header)]);
    }

    public function getHeader($header)
    {
        $name = strtolower($header);
        return isset($this->headers[$name]) ? $this->headers[$name] : [];
    }

    public function getHeaderLine($header)
    {
        return implode(', ', $this->getHeader($header));
    }

    public function withHeader($header, $value)
    {
        $new = clone $this;
        $header = trim($header);
        $name = strtolower($header);

        if (!is_array($value)) {
            $new->headers[$name] = [trim($value)];
        } else {
            $new->headers[$name] = $value;
            foreach ($new->headers[$name] as &$v) {
                $v = trim($v);
            }
        }

        // Remove the header lines.
        foreach (array_keys($new->headerLines) as $key) {
            if (strtolower($key) === $name) {
                unset($new->headerLines[$key]);
            }
        }

        // Add the header line.
        $new->headerLines[$header] = $new->headers[$name];

        return $new;
    }

    public function withAddedHeader($header, $value)
    {
        if (!$this->hasHeader($header)) {
            return $this->withHeader($header, $value);
        }

        $new = clone $this;
        $new->headers[strtolower($header)][] = $value;
        $new->headerLines[$header][] = $value;
        return $new;
    }

    public function withoutHeader($header)
    {
        if (!$this->hasHeader($header)) {
            return $this;
        }

        $new = clone $this;
        $name = strtolower($header);
        unset($new->headers[$name]);

        foreach (array_keys($new->headerLines) as $key) {
            if (strtolower($key) === $name) {
                unset($new->headerLines[$key]);
            }
        }

        return $new;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function toJson()
    {
        return json_encode($this->body);
    }


    public function toArray()
    {
        return (array)$this->body;
    }

    private function setHeaders(array $headers)
    {
        $this->headerLines = $this->headers = [];
        foreach ($headers as $header => $value) {
            $header = trim($header);
            $name = strtolower($header);
            if (!is_array($value)) {
                $value = trim($value);
                $this->headers[$name][] = $value;
                $this->headerLines[$header][] = $value;
            } else {
                foreach ($value as $v) {
                    $v = trim($v);
                    $this->headers[$name][] = $v;
                    $this->headerLines[$header][] = $v;
                }
            }
        }
    }

}
