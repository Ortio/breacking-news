@extends('index')

@section('content')
        <div class="row">
        @foreach($data as $item)
            <div class="col-md-12">
                <h3><a href="/news/{{$item->id}}">{{$item->title}}</a></h3>
                <p>{{ Str::limit(html_entity_decode($item->text), 200)}}</p>
                <a type="button" class="btn btn-primary">Подробнее</a>
            </div>

        @endforeach
        </div>
@endsection
