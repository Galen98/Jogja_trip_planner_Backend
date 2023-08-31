@extends('layouts.app')

@section('content')
@include('sweetalert::alert')
<!-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Hello Admin!
                </div>
            </div>
            <div class="card">
  <div class="card-image">
    <figure class="image is-1">
      <img src="https://naikmotor.com/wp-content/uploads/2020/06/20200626_news_400xnc-01.jpg" alt="Placeholder image">
    </figure>
  </div>
  <div class="card-content">
    <div class="media">
      <div class="media-left">
        <figure class="image is-48x48">
          <img src="https://naikmotor.com/wp-content/uploads/2020/06/20200626_news_400xnc-01.jpg" alt="Placeholder image">
        </figure>
      </div>
      <div class="media-content">
        <p class="title is-4">John Smith</p>
        <p class="subtitle is-6">@johnsmith</p>
      </div>
    </div>

    <div class="content">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit.
      Phasellus nec iaculis mauris. <a>@bulmaio</a>.
      <a href="#">#css</a> <a href="#">#responsive</a>
      <br>
      <time datetime="2016-1-1">11:09 PM - 1 Jan 2016</time>
    </div>
  </div>
</div>


        </div>
        <div class="columns">
  <div class="column">
    First column
  </div>
  <div class="column">
    Second column
  </div>
  <div class="column">
    Third column
  </div>
  <div class="column">
    Fourth column
  </div>
</div>
    </div>
</div> -->

<section class="section">
<div class="container is-fullhd">
  <h1 class="title">List Artikel</h1>
  <div class="pb-4">

  <form action="/form/artikel" method="GET">
  <button type="submit" class="button is-dark is-rounded">
<span class="material-symbols-outlined mr-2">
add_circle
</span> Tambah Artikel</button>
</form>
  </div>

  <div class="columns is-multiline mt-4">
    @foreach($artikel as $item)
  <div class="column is-one-quarter">
  <a href="">
  <div class="card">
  <div class="card-image">
    <figure class="image is-4by3">
      <img src="{{ url('public/img/'.$item->image) }}" alt="Placeholder image">
    </figure>
  </div>
  <div class="card-content">
    <div class="media">
      <div class="media-left">
      </div>
      <div class="media-content">
        <p class="title is-4 has-text-weight-semibold">{{$item->judul}}</p>
        <p class="subtitle is-6">By {{$item->author}}</p>
      </div>
    </div>

    <div class="content">
    {{Str::limit($item->shortdescription, 80)}}
      <br>
      <br>
      <time class="has-text-weight-semibold">{{\Carbon\Carbon::parse($item->created_at)->format('d/m/Y') ?? ''}}</time>
    </div>
    <div class="has-text-centered">
    <form method="GET" action="{{'artikel/edit/'.$item->id}}">
    <button type="submit" class="button is-info is-rounded mt-3"><i class="fas fa-edit mr-3"></i> Edit</button>
    </form>
    <form method="POST" action="{{'artikeldelete/'.$item->id}}">
  @csrf
  @method('delete')
    <button type="submit" class="button is-danger is-rounded mt-3"><i class="fa fa-trash mr-3" aria-hidden="true"></i> Delete</button>
</form>
</div>
  </div>
</div>
</a>
  </div>
 @endforeach
</div>

<div class="mt-6 has-text-centered">
<button class="button is-light is-rounded">Lihat semua artikel</button>
</div>
    </div>
</section>



@endsection