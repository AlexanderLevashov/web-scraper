<?php

namespace App\Http\Controllers;

use Clue\React\Buzz\Browser;
use Clue\React\HttpProxy\ProxyConnector;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Symfony\Component\DomCrawler\Crawler;

class Quick_start extends Controller
{
    public function parserQuick()
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
            ->get('https://www.imdb.com/title/tt0111161/?pf_rd_m=A2FGELUUNOQJNL&pf_rd_p=e31d89dd-322d-4646-8962-327b42fe94b1&pf_rd_r=C963E04XGBB7EWKPNFQQ&pf_rd_s=center-1&pf_rd_t=15506&pf_rd_i=top&ref_=chttp_tt_1')
            ->then(function (ResponseInterface $response) {
                $crawler = new Crawler((string) $response->getBody());
                $title = $crawler->filter('h1')->text();
                $rating = $crawler->filter('#title-overview-widget > div.vital > div.title_block > div > div.ratings_wrapper > div.imdbRating > div.ratingValue > strong > span')->text();
                $plot = $crawler->filter('#title-overview-widget > div.plot_summary_wrapper > div.plot_summary > div.summary_text')->text();
                $keywords = $crawler->filter('#titleStoryLine > div:nth-child(6) > a')->each(function (Crawler $node, $i) {
                    return $node->text();
                });
                $link = $crawler->filter('#title-overview-widget > div.plot_summary_wrapper > div.titleReviewBar > div:nth-child(1) > div > div:nth-child(2) > span > a');
                $source = $link->attr('href');

                print_r([
                    $title,
                    $rating,
                    $plot,
                    $keywords,
                    $source
                ]);
            });

        $loop->run();
    }
}
