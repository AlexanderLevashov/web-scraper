<?php

namespace App\Http\Controllers;

use Clue\React\Buzz\Browser;
use Illuminate\Http\Request;
use React\EventLoop\Factory;
use React\MySQL\Exception;
use React\MySQL\QueryResult;

class Index extends Controller
{
    public function index()
    {
        require __DIR__ . '/../../../vendor/autoload.php';

        $loop = Factory::create();
        $browser = new Browser($loop);
        $scraper = new Scraper($browser);

        $urls = [
            'https://www.imdb.com/title/tt0111161/?pf_rd_m=A2FGELUUNOQJNL&pf_rd_p=e31d89dd-322d-4646-8962-327b42fe94b1&pf_rd_r=C963E04XGBB7EWKPNFQQ&pf_rd_s=center-1&pf_rd_t=15506&pf_rd_i=top&ref_=chttp_tt_1',
            'https://www.imdb.com/title/tt0068646/?pf_rd_m=A2FGELUUNOQJNL&pf_rd_p=e31d89dd-322d-4646-8962-327b42fe94b1&pf_rd_r=SV4XC0JN9NQ25F7WVAEZ&pf_rd_s=center-1&pf_rd_t=15506&pf_rd_i=top&ref_=chttp_tt_2',
        ];

        $factory = new \React\MySQL\Factory($loop);

        $connection = $factory->createLazyConnection('root@127.0.0.1/webscraper');

        $scraper->scrape(... $urls)->then(function (array $savings) use ($connection) {
            $sql = 'INSERT INTO savings (title, rating, plot, keywords, source) VALUES (?, ?, ?, ?, ?)';
                foreach ($savings as $files) {
                    $connection->query($sql, $files->toArray())
                        ->then(function (QueryResult $result) {
                            var_dump($result);
                        }, function (Exception $exception) {
                            echo $exception->getMessage() . PHP_EOL;
                        });
                }
        });
        $loop->run();

    }
}
