@extends('layouts.main')

@section('content')
    <div class="row">
        <h1>Главные новости!</h1>
    @foreach($data as $item)
        <div class="news-block">
            <div class="col-md-12">
                <h3><a href="/news/{{$item->id}}">{{$item->title}}</a></h3>
                <p>{{ Str::limit(html_entity_decode($item->text), 200)}}</p>
                <a type="button" class="btn btn-primary" href="/news/{{$item->id}}">Подробнее</a>
            </div>
        </div>

    @endforeach
    </div>
@endsection
