<?php

namespace ShortFlake\Exception;

use Exception;

class TcpConnectionException extends Exception {

    /**
     * @var int
     */
    private $tcp_error_no;

    /**
     * @var string
     */

    private $tcp_error_message;

    /**
     * @var string
     */
    private $uri;

    public function __construct($uri, $tcp_error_no, $tcp_error_message) {
        $this->uri               = $uri;
        $this->tcp_error_no      = $tcp_error_no;
        $this->tcp_error_message = $tcp_error_message;

        parent::__construct($tcp_error_message, $tcp_error_no);
    }

    /**
     * @return int
     */
    public function getTcpErrorNo() {
        return $this->tcp_error_no;
    }

    /**
     * @return string
     */
    public function getTcpErrorMessage() {
        return $this->tcp_error_message;
    }
}