<?php

namespace App\Http\Controllers;

use Clue\React\Buzz\Browser;
use Clue\React\HttpProxy\ProxyConnector;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

class Proxy extends Controller
{
    public function proxy()
    {

        require __DIR__ . '/../../../vendor/autoload.php';
        $loop = Factory::create();


        $proxy = new ProxyConnector('127.0.0.1:8080', new Connector($loop));

        $connector = new Connector($loop, array(
            'tcp' => $proxy,
            'timeout' => 3.0,
            'dns' => false
        ));

        $connector->connect('tls://google.com:443')->then(function (ConnectionInterface $stream) {
            $stream->write("GET / HTTP/1.1\r\nHost: google.com\r\nConnection: close\r\n\r\n");
            $stream->on('data', function ($chunk) {
                //echo $chunk;
            });
        }/*, 'printf'*/);

        $browser = new Browser($loop);
        $browser
            ->get('https://www.imdb.com')
            ->then(function (ResponseInterface $response) {
                echo $response->getBody() . PHP_EOL;
            });

        $loop->run();

    }
}
