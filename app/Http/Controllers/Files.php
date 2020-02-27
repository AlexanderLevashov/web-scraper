<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Files extends Controller
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $rating;
    /**
     * @var string
     */
    public $plot;
    /**
     * @var string
     */
    public $source;
    /**
     * @var string[]
     */
    public $keywords;

    public function __construct(string $title, string  $rating, string $plot, string $source, string ...$keywords)
    {
        $this->title = $title;
        $this->rating = $rating;
        $this->plot = $plot;
        $this->source = $source;
        $this->keywords = $keywords;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'rating' => $this->rating,
            'plot' => $this->plot,
            'keywords' =>json_encode($this->keywords),
            'source' =>$this->source,
        ];
    }
}
