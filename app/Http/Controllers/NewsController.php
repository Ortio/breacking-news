<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class NewsController extends Controller
{
    public function index()
    {
        $data = News::select('id', 'title', 'text')->get();
        return view('news', compact('data'));
    }

    public function show(News $news)
    {
        //просто чтобы не городить переменные на вывод
        $data = $news;
        return view('article', compact('data'));
    }

}
