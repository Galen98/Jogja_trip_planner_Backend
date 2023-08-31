@extends('layouts.app')
@section('content')
@include('sweetalert::alert')
<section class="section">
<div class="container is-fullhd">
<nav class="breadcrumb" aria-label="breadcrumbs">
  <ul>
    <li><a href="/admin/dashboard">Home</a></li>
    <li class="is-active"><a href="#" aria-current="page">Event</a></li>
  </ul>
</nav>
  <h1 class="title">Event</h1>
  <div class="pb-4">

  <form action="/form/event" method="GET">
  <button type="submit" class="button is-dark is-rounded">
<span class="material-symbols-outlined mr-2">
add_circle
</span> Tambah Event</button>
</form>
  </div>
@foreach($event as $item)
  <div class="card mt-5">
        <div class="card-content">
          <div class="content">
            <div class="columns">
              <div class="column is-narrow">
                <div class="card-image">
                  <figure class="image">
                    <img
                      src="{{ url('public/img/'.$item->image) }}" style="width:100px;"
                      alt="Placeholder image"
                    />
                  </figure>
                  <div class="media-content has-text-centered">
                    @if($item->kategori == "senibudaya")
                <p class="subtitle is-6"><span class="tag is-danger">Seni & Budaya</span></p>
                @endif
                @if($item->kategori == "keagamaan")
                <p class="subtitle is-6"><span class="tag is-success">Keagamaan</span></p>
                @endif
                @if($item->kategori == "kemasyarakatan")
                <p class="subtitle is-6"><span class="tag is-info">Kemasyarakatan</span></p>
                @endif
                @if($item->kategori == "umum")
                <p class="subtitle is-6"><span class="tag is-warning">Umum</span></p>
                @endif
              </div>
                </div>
              </div>
              <div class="column">
                <nav class="level">
                  <div class="level-item has-text-centered">
                    <div>
                      <p class="heading"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Event</p>
                      <p class="title is-4">{{$item->namaevent}}</p>
                    </div>
                  </div>
                  <div class="level-item has-text-centered">
                    <div>
                      <p class="heading"><i class="fa fa-clock-o" aria-hidden="true"></i> Waktu</p>
                      <p class="title is-4">{{\Carbon\Carbon::parse($item->waktu)->format('d/m/Y') ?? ''}}</p>
                    </div>
                  </div>
                  
                </nav>
                <footer class="card-footer mt-5">
              <a href="#" class="card-footer-item has-text-centered">View</a>
              <a href="#" class="card-footer-item has-text-centered">Edit</a>
              <a href="#" class="card-footer-item has-text-centered">Delete</a>
            </footer>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
</div>
</section>

@endsection