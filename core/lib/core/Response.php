<?php

namespace Core;

class Response {
    protected $headers = array();
    protected $status = 200;
    protected $body;

    public static $codes = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    /**
     * Sets the HTTP status of the response.
     *
     * @param int $code HTTP status code.
     */
    public function status($code) {
        if (array_key_exists($code, self::$codes)) {
            if (strpos(php_sapi_name(), 'cgi') !== false) {
                header('Status: '.$code.' '.self::$codes[$code], true);
            }
            else {
                header(($_SERVER['SERVER_PROTOCOL'] ?: 'HTTP/1.1').' '.self::$codes[$code], true, $code);
            }
        }
        else {
            throw new Exception('Invalid status code.');
        }

        return $this;
    }

    /**
     * Adds a header to the response.
     *
     * @param string|array $key Header name or array of names and values
     * @param string $value Header value
     */
    public function header($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->headers[$k] = $v;
            }
        }
        else {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * Writes content to the response body.
     *
     * @param string $str Response content
     */
    public function write($str) {
        $this->body .= $str;

        return $this;
    }

    /**
     * Clears the response.
     */
    public function clear() {
        $this->headers = array();
        $this->status = 200;
        $this->body = '';

        return $this;
    }

    /**
     * Sets caching headers for the response.
     *
     * @param int|string $expires Expiration time
     */
    public function cache($expires) {
        if ($expires === false) {
            $this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0'
            );
            $this->headers['Pragma'] = 'no-cache';
        }
        else {
            $expires = is_int($expires) ? $expires : strtotime($expires);
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            $this->headers['Cache-Control'] = 'max-age='.($expires - time());
        }

        return $this;
    }

    /**
     * Sends the response and exits the program.
     */
    public function send() {
        ob_end_clean();

        if (!headers_sent()) {
            foreach ($this->headers as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        header($field.': '.$v);
                    }
                }
                else {
                    header($field.': '.$value);
                }
            }
        }

        exit($this->body);
    }
}
