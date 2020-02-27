<?php

namespace App\Http\Controllers;

use Clue\React\Buzz\Browser;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;
use Symfony\Component\DomCrawler\Crawler;
use function React\Promise\all;

class Scraper extends Controller
{

    private $browser;

    public function __construct(Browser $browser)
    {

        $this->browser = $browser;
    }

    public function scrape(string ...$urls): PromiseInterface
    {
        $promises = array_map(function ($url) {
            return $this->extractFromUrl($url);
        }, $urls);
        return all($promises);
    }

    private function extract(string $responseBody): Files
    {
        $crawler = new Crawler($responseBody);
        $title = $crawler->filter('h1')->text();
        $rating = $crawler->filter('#title-overview-widget > div.vital > div.title_block > div > div.ratings_wrapper > div.imdbRating > div.ratingValue > strong > span')->text();
        $plot = $crawler->filter('#title-overview-widget > div.plot_summary_wrapper > div.plot_summary > div.summary_text')->text();
        $keywords = $crawler->filter('#titleStoryLine > div:nth-child(6) > a')->each(function (Crawler $node, $i) {
            return $node->text();
        });
        $link = $crawler->filter('#title-overview-widget > div.plot_summary_wrapper > div.titleReviewBar > div:nth-child(1) > div > div:nth-child(2) > span > a');
        $source = $link->attr('href');

        return new Files($title, $rating, $plot, $source, ...$keywords);

    }

    private function extractFromUrl($url): PromiseInterface
    {
        return $this->browser->get($url)->then(function (ResponseInterface $response) {
            return $this->extract((string) $response->getBody());
        });
    }
}
