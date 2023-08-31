@extends('layouts.app')

@section('content')
@include('sweetalert::alert')
<section class="section">
    
<div class="container is-fullhd">
<nav class="breadcrumb" aria-label="breadcrumbs">
  <ul>
    <li><a href="/admin/dashboard">Home</a></li>
    <li><a href="#">Artikel</a></li>
    <li class="is-active"><a href="#" aria-current="page">Form Artikel</a></li>
  </ul>
</nav>
  <h3 class="is-size-4 mb-4">Form Artikel</h3>

  <form action="{{url('insertartikels')}}" method="POST" enctype="multipart/form-data">
    @csrf
  <div class="field">
  <label class="label">Judul Artikel</label>
  <div class="control">
    <input class="input" type="text" name="judul" placeholder="Judul Artikel">
  </div>
</div>

<div class="field">
  <label class="label">Author</label>
  <div class="control">
    <input class="input" type="text" name="author" placeholder="Author">
  </div>
</div>

<div class="field">
  <label class="label">Kategori</label>
  <div class="select is-multiple">
    <select name="kategori">
      <option value="Petualangan">Petualangan</option>
      <option value="Camping">Camping</option>
      <option value="Honeymoon">Honeymoon</option>
      <option value="Paket wisata">Paket wisata</option>
      <option value="Transportasi">Transportasi</option>
      <option value="Transportasi">Event</option>
      <option value="Informasi lainnya">Informasi lainnya</option>
    </select>
  </div>
</div>


<div class="field mt-4">
  <label class="label">Short Description (Overview)</label>
  <div class="control">
    <textarea class="textarea" name="short" placeholder="Short Description"></textarea>
  </div>
</div>

<div class="field mt-4">
  <label class="label">Full Description</label>
  <div class="control">
    <textarea class="textarea" name="description" id="mytextarea" placeholder="Short Description"></textarea>
  </div>
</div>



<div class="field mt-4">
<label class="label">Thumbnail</label>
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
    <form action="/admin/dashboard" method="GET">
    <button type="submit" class="button is-link is-light">Cancel</button>
    </form>
  </div>
</div>

</div>
</section>
@endsection
