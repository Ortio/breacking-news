<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class ParseRBC extends Command
{
    //console command
    protected $signature = 'parse:rbc';
    protected $description = 'Парсинг страниц с новостями из РБК и сохранение их в базу';

    private $site = 'https://www.rbc.ru';
    private $newsCount = 15;
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
     * Поскольку мы парсим всего 15 новосей, то я видел смысл загружать все скопом в базу.
     * Уникальность новости решил проверять по Url, послкольку он по определению должен быть уникальным.
     * Можно еще по title, в данном случае, т.к. новости не должны повторять свои заголовки, для того чтобы создавать
     * иллюзию новой информации.
     *
     *
     * @return int
     */
    public function handle()
    {
        $links = $this->getLinks($this->site, ".js-main-reload-item");
        //в условии сказано о 15 новостях, поэтому на всякий случай срезаю массив до нужного количества
        $links = array_slice($links, 0, $this->newsCount);

        $news = News::pluck('url');
        $data = [];

        $progressBar = $this->output->createProgressBar($this->newsCount);
        $progressBar->start();

        foreach ($links as $link) {
            $n = $this->getNews($link);
            if(empty($n) || $news->contains($n['url'])){
                sleep(0.1);
                $progressBar->advance();
                continue;
            }

            $data[] = $n;
            sleep(0.5);
            $progressBar->advance();
        }

        $res = News::insert($data);
        $progressBar->finish();
        echo "\n";
        echo ($res) ? "Загружено новых новостей: ".count($data) : "Что-то пошло не так";

        return 0;
    }


    /**
     * @param string $url
     * @param string $selector
     * @return array
     */
    private function getLinks($url, $selector){
        $dom = $this->getDoomFromLink($url);
        $links = [];
        foreach ($dom->findMulti($selector) as $el){
            $parseUrl = parse_url($el->getElementByTagName('a')->getAttribute('href'));
            $links[] = $parseUrl['scheme']."://".$parseUrl['host'].$parseUrl['path'];//need to find in db
        }

        return $links;
    }

    function getNews($link){
        $dom = $this->getDoomFromLink($link);

        if (empty($dom))
            return null;

        $data = [];

        $data['url']    = $link;
        $data['title']  = $dom->findOne('.article__header__title-in')->text;
        $data['img']    = $dom->findOne('.article__main-image__image')->getAttribute('src');

        $texts          = $dom->findMulti('.article__content')->find('p');
        $data['text']   = '';
        foreach ($texts as $text) {
            $data['text'] .= $text->text." ";
        }

        $newsTime = $dom->findOne('.article__header__date')->getAttribute('content');
        $dateTime = date_create_from_format("Y-m-d\TH:i:sP", $newsTime);
        $data['news_time'] = null;
        //у РБК Pro нет даты, точнее это не совсем новости.
        if (!empty($dateTime))
            $data['news_time']  = $dateTime->format('Y-m-d H:i:s');

        return $data;
    }

    /**
     * @param string $link
     * @return HtmlDomParser|null
     */
    function getDoomFromLink($link){
        try{
            $body = Http::get($link)->body();
            $html = HtmlDomParser::str_get_html($body);
        }catch (\Exception $e){
            echo $e->getMessage();
        }

        return (!empty($html)) ? $html : null;
    }


}
