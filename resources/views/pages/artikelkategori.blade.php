@extends('layouts.app')

@section('content')
@include('sweetalert::alert')
<section class="section">
<div class="container is-fullhd">
<nav class="breadcrumb" aria-label="breadcrumbs">
  <ul>
    <li><a href="/admin/dashboard">Home</a></li>
    <li><a href="#">Artikel</a></li>
    <li class="is-active"><a href="#" aria-current="page">Kategori Artikel</a></li>
  </ul>
</nav>
<h3 class="is-size-4 mb-4">Kategori Artikel</h3>
<button type="button" class="button is-dark is-rounded mb-4 js-modal-trigger" data-target="modal-js-add">
<span class="material-symbols-outlined mr-2">
add_circle
</span> Tambah Kategori Artikel</button>

<div class="table-container">
      <div class="table-wrapper has-mobile-cards">
        <table class="table">
          <thead>
          <tr>
            <th>No</th>
            <th>Kategori Artikel</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
          </thead>
          <tbody>
            @foreach($kategori as $item)
          <tr>
            <td data-label="Name">{{ $loop->iteration }}</td>
            <td data-label="Company">{{$item->namakategori}}</td>
            <td><button type="button" class="button is-small is-rounded is-primary js-modal-edit" value="{{$item->id}}" data-target="modal-js-edit"><i class="fas fa-edit"></i></button></td>
            <td><button type="button" class="button is-small is-rounded is-danger js-modal-delete" value="{{$item->id}}"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
          </tr>
          @endforeach
</tbody>
</table>
</div>
</div>
</div>

</section>
{{ $kategori->links() }}
@endsection

@section('script')
<div id="modal-js-add" class="modal">
  <div class="modal-background"></div>
  <div class="modal-content">
    <div class="box">
      <p>Tambah Artikel Kategori</p>
      <form action="{{url('addartikelkategori')}}" method="POST">
        @csrf
      <div class="field mt-4">
    <label class="label">Nama Kategori</label>
    <div class="control">
    <input class="input" type="text" name="namakategori" placeholder="Nama Kategori">
    </div>
    </div>
    <button type="submit" class="button is-primary is-rounded mt-3">Submit</button>
    </form>
    </div>
  </div>
  <button class="modal-close is-large" aria-label="close"></button>
</div>

<div id="modal-js-edit" class="modal modaledit">
  <div class="modal-background"></div>
  <div class="modal-content">
    <div class="box">
      <p>Edit Artikel Kategori</p>
      <form action="{{url('updatekategori')}}" enctype="multipart/form-data" method="POST" id="formedit">
        @csrf
      <div class="field mt-4">
    <label class="label">Nama Kategori</label>
    <div class="control">
    <input type="hidden" name="idkategori" id="idkategori" readonly=""> 
    <input class="input" type="text" id="namakategori" name="namakategori" placeholder="Nama Kategori">
    </div>
    </div>
    <button type="button" class="button is-primary is-rounded mt-3 btnupdate">Update</button>
    </form>
    </div>
  </div>
  <button class="modal-close is-large" aria-label="close"></button>
</div>

<div id="js-modal-delete" class="modal modaldelete">
  <div class="modal-background"></div>
  <div class="modal-content">
    <div class="box">
      <form action="{{url('deleteartikelkategori')}}" enctype="multipart/form-data" method="POST" id="formdelete">
        @csrf
        @method('delete')
      <div class="field mt-4">
    <label class="label">Yakin ingin Mnghapus Kategori?</label>
    <div class="control">
    <input type="hidden" name="idkategoris" id="idkategoris" readonly=""> 
    </div>
    </div>
    <button type="button" class="button is-danger is-rounded mt-3 btndelete mr-3">Confirm</button>
    <button type="button" class="button is-light is-rounded mt-3 btndeleteclose">Cancel</button>
    </form>
    </div>
  </div>
  <button class="modal-close is-large" aria-label="close"></button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
  // Functions to open and close a modal
  function openModal($el) {
    $el.classList.add('is-active');
  }

  function closeModal($el) {
    $el.classList.remove('is-active');
  }

  function closeAllModals() {
    (document.querySelectorAll('.modal') || []).forEach(($modal) => {
      closeModal($modal);
    });
  }

  // Add a click event on buttons to open a specific modal
  (document.querySelectorAll('.js-modal-trigger') || []).forEach(($trigger) => {
    const modal = $trigger.dataset.target;
    const $target = document.getElementById(modal);

    $trigger.addEventListener('click', () => {
      openModal($target);
    });
  });

  // Add a click event on various child elements to close the parent modal
  (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(($close) => {
    const $target = $close.closest('.modal');

    $close.addEventListener('click', () => {
      closeModal($target);
    });
  });

  // Add a keyboard event to close all modals
  document.addEventListener('keydown', (event) => {
    const e = event || window.event;

    if (e.keyCode === 27) { // Escape key
      closeAllModals();
    }
  });
});
    </script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
  // Functions to open and close a modal
  function openModal($el) {
    $el.classList.add('is-active');
  }

  function closeModal($el) {
    $el.classList.remove('is-active');
  }

  function closeAllModals() {
    (document.querySelectorAll('.modal') || []).forEach(($modal) => {
      closeModal($modal);
    });
  }

  // Add a click event on buttons to open a specific modal
//   (document.querySelectorAll('.js-modal-edit') || []).forEach(($trigger) => {
//     const modal = $trigger.dataset.target;
//     const $target = document.getElementById(modal);

//     $trigger.addEventListener('click', () => {
//       openModal($target);
//     });
//   });

  // Add a click event on various child elements to close the parent modal
  (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(($close) => {
    const $target = $close.closest('.modal');

    $close.addEventListener('click', () => {
      closeModal($target);
    });
  });

  // Add a keyboard event to close all modals
  document.addEventListener('keydown', (event) => {
    const e = event || window.event;

    if (e.keyCode === 27) { // Escape key
      closeAllModals();
    }
  });
});

$(document).ready(function(){
        $(document).on('click', '.js-modal-edit', function(){
            var idkategori=$(this).val();
            const modaledit = $('.modaledit');
            modaledit.addClass('is-active');
            $.ajax({
                type: "GET",
                url:"/showkategori/"+idkategori,
                success:function(response){
                    console.log(response.Kategori.id);
                     //$('#orderid').val(response.Order.OrderID);
                     $('#idkategori').val(response.Kategori.id); 
                    $('#namakategori').val(response.Kategori.namakategori);   
                }
            });
        });
        
    });
    </script>

<script>
  $(document).ready(function(){
    $(document).on('click', '.btndelete', function(){
            const modaldeletecloses = $('.modaldelete');
            var idkategoris=$('#formdelete').find('#idkategoris').val()
            let formData=$('#formdelete').serialize()
            //console.log(progid);
            console.log(formData)

            $.ajax({
                url:'/deleteartikelkategori/${idkategoris}',
                method:"DELETE",
                data:formData,
                success:function(data){
                  modaldeletecloses.removeClass('is-active');
                  window.location.assign('/arikel/artikelkategori');
                }
            })
        });


        $(document).on('click', '.js-modal-delete', function(){
            var idkategoris=$(this).val();
            const modaldelete = $('.modaldelete');
            modaldelete.addClass('is-active');
            $.ajax({
                type: "GET",
                url:"/showkategori/"+idkategoris,
                success:function(response){
                    console.log(response.Kategori.id);
                     //$('#orderid').val(response.Order.OrderID);
                     $('#idkategoris').val(response.Kategori.id);  
                }
            });
        });
        $(document).on('click', '.btndeleteclose', function(){
          const modaldeleteclose = $('.modaldelete');
            modaldeleteclose.removeClass('is-active');
        });
      });
</script>
@endsection