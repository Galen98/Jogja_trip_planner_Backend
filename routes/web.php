<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\WisataController;
use App\Models\atrikelkategoris;
use App\Models\Artikels;
use App\Models\Kategori;
use App\Models\Tags;
use App\Models\User;
// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/',[LoginController::class,'showAdminLoginForm'])->name('admin.login-view');
Route::post('/admin',[LoginController::class,'adminLogin'])->name('admin.login');
Route::get('/form/artikel',function(){
    return view('pages.formartikel');
});
Route::get('/form/event',function(){
    return view('pages.formevent');
});

Route::get('/admin/register',[RegisterController::class,'showAdminRegisterForm'])->name('admin.register-view');
Route::post('/admin/register',[RegisterController::class,'createAdmin'])->name('admin.register');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/admin/dashboard',function(){
    $artikel=Artikels::paginate(4);
    return view('admin',compact('artikel'));
})->middleware('auth:admin');

//routing artikel kategori
// Route::post('addartikelkategori', [App\Http\Controllers\ArtikelController::class,'addkategoriartikel']);
// Route::delete('/deleteartikelkategori/{idkategoris}', [App\Http\Controllers\ArtikelController::class,'deletekategori']);
// Route::get('/arikel/artikelkategori', [App\Http\Controllers\ArtikelController::class,'viewkategoriartikel']);
// Route::get('/showkategori/{KategoriID}', [App\Http\Controllers\ArtikelController::class,'showkategori']);
// Route::patch('/updatekategori/{idkategori}', [App\Http\Controllers\ArtikelController::class,'updatekategori']);

//routing artikel
Route::get('/artikel', function(){
$artikel=Artikels::paginate(8);
return view('pages.artikel', compact('artikel'));
});
Route::post('insertartikels', [App\Http\Controllers\ArtikelController::class,'insertartikels']);
Route::get('artikel/edit/{artikelid}', [App\Http\Controllers\ArtikelController::class,'editviewartikel']);
Route::delete('artikeldelete/{artikelid}', [App\Http\Controllers\ArtikelController::class,'deleteartikel']);
Route::patch('updateartikel/{artikelid}', [App\Http\Controllers\ArtikelController::class,'updateartikel']);
//routing event
Route::post('insertevent', [App\Http\Controllers\EventController::class, 'postevent']);
Route::get('/event', [App\Http\Controllers\EventController::class, 'getevent']);
Route::get('/cobajson',[App\Http\Controllers\EventController::class, 'cobajson']);
//routing wisata
Route::get('/wisata/kategori/form', function(){
return view('pages.formkategoriwisata');
});
Route::post('insertkategori', [App\Http\Controllers\WisataController::class, 'insertkategori']);
Route::delete('/deletekategori/{kategoriid}', [App\Http\Controllers\WisataController::class,'deletekategori']);
Route::get('/wisata/kategori', function(){
    $kategori=Kategori::paginate(10);
    return view('pages.kategoriwisata',compact('kategori'));
});
Route::get('/wisata/kategori/edit/{kategoriid}', [App\Http\Controllers\WisataController::class,'editkategoriview']);
Route::patch('updatekategori/{kategoriid}',[App\Http\Controllers\WisataController::class,'updatekategori']);