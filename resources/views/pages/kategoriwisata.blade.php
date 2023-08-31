@extends('layouts.app')

@section('content')
@include('sweetalert::alert')
<section class="section">
<div class="container is-fullhd">
<nav class="breadcrumb" aria-label="breadcrumbs">
  <ul>
    <li><a href="/admin/dashboard">Home</a></li>
    <li><a href="/wisata">Wisata</a></li>
    <li class="is-active"><a href="/wisata" aria-current="page">Kategori Wisata</a></li>
  </ul>
</nav>
  <h1 class="title">Kategori Wisata</h1>
  <div class="pb-4">

  <form action="/wisata/kategori/form" method="GET">
  <button type="submit" class="button is-dark is-rounded">
<span class="material-symbols-outlined mr-2">
add_circle
</span> Tambah Kategori</button>
</form>
  </div>

  <div class="columns">
 
  <div class="column is-full">
    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
      <thead>
        <tr>
          <th>Nama Kategori</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        @foreach($kategori as $item)
        <tr>
          <td>{{$item->namakategori}}</td>
          <td>
          <form method="GET" action="{{'/wisata/kategori/edit/'.$item->id}}">  
          <button type="submit" class="button is-info is-rounded is-small"><i class="fas fa-edit"></i> Edit</button>
</form></td>
          <td><form method="POST" action="{{'/deletekategori/'.$item->id}}">
  @csrf
  @method('delete')
    <button type="submit" class="button is-danger is-rounded is-small"><i class="fa fa-trash mr-3" aria-hidden="true"></i> Delete</button>
</form></td>
          @endforeach
        </tr>
      </tbody>
    </table>
  </div>
</div>
</div>
</section>
@endsection