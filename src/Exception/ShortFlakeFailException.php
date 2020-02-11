<?php

namespace ShortFlake\Exception;

use Exception;
use Throwable;

class ShortFlakeFailException extends Exception {
    public function __construct($message = "Can not acquire ShortFlake id", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}