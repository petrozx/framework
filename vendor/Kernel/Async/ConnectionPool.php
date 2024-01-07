<?
namespace Kernel\Async;

class ConnectionPool
{
    private $connections = [];

    public function getConnection($url)
    {
        if (!isset($this->connections[$url])) {
            $socket = @stream_socket_client($url, $errno, $errstr, 30);
            if ($socket) {
                stream_set_blocking($socket, false);
                $this->connections[$url] = $socket;
            } else {
                echo "Error connecting to $url: $errstr ($errno)\n";
            }
        }

        return $this->connections[$url];
    }

    public function releaseConnection($url)
    {
        if (isset($this->connections[$url])) {
            fclose($this->connections[$url]);
            unset($this->connections[$url]);
        }
    }
}