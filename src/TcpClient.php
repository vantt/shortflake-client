<?php

namespace ShortFlake;

use ShortFlake\Exception\ShortFlakeFailException;
use ShortFlake\Exception\TcpConnectionException;
use Throwable;

/**
 * Generate 64bits UUID based on the following formula
 *    42bits: number of millisecond epocs (1000 milliseconds = 1 second) since 1420099200; // 2015-01-01 00:00:00
 *    10bits: generator-id (total 1024 generator instances 0-1023)
 *    12bits: an increment number (smaller than 4096)
 */
class TcpClient {

    private $host;

    private $port;

    /**
     * TcpClient constructor.
     *
     * @param string $host
     * @param int    $port
     */
    public function __construct($host = "127.0.0.1", $port = 11337) {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return int  unsigned 64bits integer
     *
     * @throws ShortFlakeFailException
     * @throws TcpConnectionException
     */
    public function getId() {
        $ids = $this->getIds(1);

        return $ids[0];
    }


    /**
     * @param int $num_ids positive value, the number of ids to generate.
     *
     * @return array
     * @throws ShortFlakeFailException
     * @throws TcpConnectionException
     */
    public function getIds($num_ids) {
        assert($num_ids > 0);

        $success     = false;
        $num_failure = 0;
        $ids         = [];

        do {
            $uri = "tcp://{$this->host}:{$this->port}";
            $fp  = stream_socket_client($uri, $error_no, $error_str, 30);

            if (!$fp) {
                throw new TcpConnectionException($uri, $error_no, $error_str);
            }
            else {
                try {
                    // pack using unsigned short (always 16 bit, little endian byte order)
                    // http://php.net/manual/en/function.pack.php
                    fwrite($fp, pack('v', $num_ids));

                    $binary_string = stream_get_contents($fp);

                    fclose($fp);

                    if ($binary_string) {

                        // unpack using uint64 in the binary string little-endian byte order
                        // http://php.net/manual/en/function.pack.php
                        // number (num_ids) of uint64,
                        // "P5" means 5 continuous uint64 binary string
                        $binary_format = "P{$num_ids}";

                        $ids = unpack($binary_format, $binary_string);

                        if (is_array($ids)) {
                            $success = (count($ids) == $num_ids);
                        }
                    }
                } catch (Throwable $t) {
                    $num_failure += 1;
                    if ($num_failure > 10) {
                        throw new ShortFlakeFailException();
                    }
                }
            }
        } while (!$success);

        return array_values($ids);
    }
}