@extends('layouts.app')

@section('content')
@include('sweetalert::alert')
<section class="section">
    
<div class="container is-fullhd">
<nav class="breadcrumb" aria-label="breadcrumbs">
  <ul>
    <li><a href="/admin/dashboard">Home</a></li>
    <li><a href="#">Event</a></li>
    <li class="is-active"><a href="#" aria-current="page">Form Event</a></li>
  </ul>
</nav>
  <h3 class="is-size-4 mb-4">Form Event</h3>

  <form action="{{url('insertevent')}}" method="POST" enctype="multipart/form-data">
    @csrf
  <div class="field">
  <label class="label">Nama Event</label>
  <div class="control">
    <input class="input" type="text" name="namaevent" placeholder="Nama Event">
  </div>
</div>

<div class="field">
  <label class="label">Lokasi</label>
  <div class="control">
    <input class="input" type="text" name="lokasi" placeholder="Lokasi">
  </div>
</div>

<div class="field">
  <label class="label">HTM</label>
  <div class="control">
    <input class="input" type="text" name="htm" placeholder="Harga Tiket">
  </div>
</div>

<div class="field">
  <label class="label">Maps</label>
  <div class="control">
    <input class="input" type="text" name="maps" placeholder="Maps">
  </div>
</div>

<div class="field">
  <label class="label">Waktu</label>
  <div class="control">
    <input class="input" type="date" name="waktu" placeholder="Waktu">
  </div>
</div>

<div class="field">
  <label class="label">Kategori</label>
  <div class="select is-multiple">
    <select name="kategori">
      <option value="senibudaya">Seni & Budaya</option>
      <option value="keagamaan">Keagamaan</option>
      <option value="kemasyarakatan">Kemasyarakatan</option>
      <option value="umum">Umum</option>
    </select>
  </div>
</div>


<div class="field mt-4">
  <label class="label">Description</label>
  <div class="control">
    <textarea class="textarea" id="mytextarea" name="description" placeholder="Short Description"></textarea>
  </div>
</div>


<div class="field mt-4">
<label class="label">Image</label>
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
