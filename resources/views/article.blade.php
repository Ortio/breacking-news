@extends('layouts.main')

@section('content')
    <div class="article-block">
        <div class="blog-header">
            <h1 class="blog-title">{{$data->title}}</h1>
        </div>
        <hr>
        @if(!empty($data->img))
            <div class="image-block">
                <img src="{{$data->img}}" alt="{{$data->title}}" class="img-fluid">
            </div>
        @endif
        <div class="blog-post">
            <div class="blog-date">
                <span>{{ $data->news_time->format('H:m, d.m.Y') }}</span>
            </div>
            <div class="blog-content">
                <p>{{html_entity_decode($data->text)}}</p>
            </div>

        </div>

        <a type="button" class="btn btn-primary" href="/"> На главную</a>
    </div>

@endsection

