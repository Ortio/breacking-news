<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class ParseRBC extends Command
{
    //console command
    protected $signature = 'parse:rbc';
    protected $description = 'Парсинг страниц с новостями из РБК и сохранение их в базу';

    private $site = 'https://www.rbc.ru';
    private $selector = ".js-main-reload-item";//selector that we will search to get link a
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }

    /**
     * @return array
     */
    function getLinks(){
        $dom = $this->getDoomFromLink($this->site);
        $html = [];
        foreach ($dom->findMulti($this->selector) as $el){
            $html[] = $el->getElementByTagName('a')->getAttribute('href');
        }

        return $html;
    }


    function getDoomFromLink($link){
        $body = Http::get($link)->body();
        return HtmlDomParser::str_get_html($body);
    }


}
