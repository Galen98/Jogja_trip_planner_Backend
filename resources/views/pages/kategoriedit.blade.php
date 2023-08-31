@extends('layouts.app')
@section('content')
@include('sweetalert::alert')
<section class="section">
<div class="container is-fullhd">
@foreach($kategori as $item)
<nav class="breadcrumb" aria-label="breadcrumbs">
<ul>
    <li><a href="/admin/dashboard">Home</a></li>
    <li><a href="/wisata">Wisata</a></li>
    <li><a href="/wisata/kategori">Kategori Wisata</a></li>
    <li class="is-active"><a href="/wisata" aria-current="page">Edit</a></li>
  </ul>
</nav>

  <h3 class="is-size-4 mb-4">Edit Artikel</h3>
  <form action="{{url('updatekategori/' .$item->id)}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('patch')
   <center><img src="{{ url('public/img/'.$item->image) }}" class="img-fluid" alt="Responsive image" style="padding-bottom: 20px; width:800px;"></center> 
  <div class="field">
  <label class="label">Nama Kategori</label>
  <div class="control">
    <input class="input" type="text" name="namakategori" placeholder="Judul Artikel" value="{{$item->namakategori}}">
  </div>
</div>

<div class="field mt-4">
  <label class="label">Short Description (Overview)</label>
  <div class="control">
    <textarea class="textarea" name="short" placeholder="Short Description">{{$item->shortdescription}}</textarea>
  </div>
</div>

<input type="hidden" name="namagambar" value="{{$item->image}}">
<div class="field mt-4">
<label class="label">Edit Thumbnail</label>
  <div class="control">
  <div class="file">
  <label class="file-label">
  <input class="file-input" type="file" name="image">
    <span class="file-cta">
      <span class="file-icon">
        <i class="fas fa-upload"></i>
      </span>
      <span class="file-label">
        Choose a file…
      </span>
    </span>
  </label>
</div>
  </div>
</div>

<div class="field is-grouped mt-5">
  <div class="control">
    <button type="submit" class="button is-link">Submit</button>
  </div>
  </form>

  <div class="control">
    <form action="/artikel" method="GET">
    <button type="submit" class="button is-link is-light">Cancel</button>
    </form>
  </div>
</div>

</div>
</section>
@endforeach
@endsection