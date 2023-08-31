@extends('layouts.app')

@section('content')
@include('sweetalert::alert')
<section class="section">
<div class="container is-fullhd">
<nav class="breadcrumb" aria-label="breadcrumbs">
  <ul>
    <li><a href="/admin/dashboard">Home</a></li>
    <li class="is-active"><a href="#" aria-current="page">Artikel</a></li>
  </ul>
</nav>
  <h1 class="title">Artikel</h1>
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
    <div class="columns is-multiline">
    <div class="column">
    <form method="GET" action="{{'artikel/edit/'.$item->id}}">
    <button type="submit" class="button is-info is-rounded mt-3"><i class="fas fa-edit mr-3"></i> Edit</button>
    </form>
</div>
<div class="column">
<form method="POST" action="{{'artikeldelete/'.$item->id}}">
  @csrf
  @method('delete')
    <button type="submit" class="button is-danger is-rounded mt-3"><i class="fa fa-trash mr-3" aria-hidden="true"></i> Delete</button>
</form>
</div>
</div>
  </div>
</div>
</a>
  </div>
 @endforeach
</div>

<div class="mt-6 has-text-centered">
{{ $artikel->links() }}
</div>
    </div>
</section>
@endsection