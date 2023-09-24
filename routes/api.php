<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\NlpController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/cobaitinerary', [App\Http\Controllers\Api\ArtikelController::class, 'generateTrip']);
Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');
 // * route "/login"
 // * @method "POST"
 // */
Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');
Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('/dashboard', App\Http\Controllers\Api\UserControllers::class);
Route::apiResource('/artikels', App\Http\Controllers\Api\ArtikelController::class);
Route::apiResource('/event', App\Http\Controllers\Api\EventController::class);
//wisata api
Route::post('/listhotel',[App\Http\Controllers\Api\WisataController::class, 'listhotel']);
Route::get('/cuaca',[App\Http\Controllers\Api\WisataController::class, 'cuaca']);
Route::get('/hotelkeluarga',[App\Http\Controllers\Api\WisataController::class, 'rekomendasiHotelkeluarga']);
Route::get('/restokeluarga',[App\Http\Controllers\Api\WisataController::class, 'rekomendasirestoKeluarga']);
Route::get('/hotelgrup',[App\Http\Controllers\Api\WisataController::class, 'rekomendasiHotelgrup']);
Route::get('/restogrup',[App\Http\Controllers\Api\WisataController::class, 'rekomendasirestoGrup']);
Route::post('/recommend-tourist-spot',[App\Http\Controllers\Api\DecisiontreeController::class, 'recommendTouristSpot']);
Route::post('/listresto',[App\Http\Controllers\Api\WisataController::class, 'listresto']);
Route::post('/explorewisata',[App\Http\Controllers\Api\WisataController::class, 'explorewisata']);
Route::post('/recommendTouristSpotbyweather',[App\Http\Controllers\Api\DecisiontreeController::class, 'recommendTouristSpotbyweather']);
Route::get('/listkategori',[App\Http\Controllers\Api\WisataController::class, 'kategoriwisata']);
Route::get('/listkategori/{kategori}',[App\Http\Controllers\Api\WisataController::class, 'wisatabykategori']);
Route::get('/getkategori/{kategori}', [App\Http\Controllers\Api\WisataController::class, 'getkategori']);
Route::get('/rekomentipe/{tipe}', [App\Http\Controllers\Api\WisataController::class, 'rekomendasibytipe']);
Route::get('/checkweek/{date}',[App\Http\Controllers\Api\WisataController::class, 'checkWeekdayOrWeekend']);
Route::get('/topwisata',[App\Http\Controllers\Api\WisataController::class, 'topwisata']);
Route::get('/eksperimen',[App\Http\Controllers\Api\WisataController::class, 'eksperimen']);
Route::middleware('auth:api')->post('/like/{attractionId}',[App\Http\Controllers\Api\WisataController::class, 'addLove']);
Route::middleware('auth:api')->delete('/unlike/{attractionId}',[App\Http\Controllers\Api\WisataController::class, 'removeLove']);
Route::middleware('auth:api')->get('/user/likes',[App\Http\Controllers\Api\WisataController::class, 'getUserLikes']);
Route::middleware('auth:api')->get('/user/itinerary',[App\Http\Controllers\Api\WisataController::class, 'getItinerarybyuser']);
Route::middleware('auth:api')->post('/user/likeswisata',[App\Http\Controllers\Api\WisataController::class, 'getUserLikeswisata']);
Route::middleware('auth:api')->get('/user/recommendations',[App\Http\Controllers\Api\WisataController::class, 'getUserBasedRecommendations']);
//refresh token login
Route::post('/refresh-token', [App\Http\Controllers\HomeController::class, 'refreshToken']);
Route::post('/getnlp', [NlpController::class, 'nlprequest']);
Route::get('/check-token-validity',[App\Http\Controllers\Api\TokenController::class, 'checkTokenValidity']);
Route::get('/bodymaps', [App\Http\Controllers\Api\WisataController::class, 'mapslocation']);
Route::get('/hotelmaps', [App\Http\Controllers\Api\WisataController::class, 'mapshotel']);
Route::get('/paketwisata', [App\Http\Controllers\Api\WisataController::class, 'getPaketwisata']);
Route::get('/rekomendasiresto/{itineraryId}', [App\Http\Controllers\Api\WisataController::class, 'loadrekomendasiresto']);
Route::get('/hotelbackpacker', [App\Http\Controllers\Api\WisataController::class, 'hotelbackpacker']);
Route::get('/restobackpacker', [App\Http\Controllers\Api\WisataController::class, 'restobackpacker']);
Route::get('/restogrup', [App\Http\Controllers\Api\WisataController::class, 'restogrup']);
Route::get('/motorbackpacker', [App\Http\Controllers\Api\WisataController::class, 'kendaraanbackpacker']);
Route::get('/loaditinerary/{itineraryId}', [App\Http\Controllers\Api\WisataController::class, 'loadItinerary']);
Route::get('/detailitinerary/{itineraryId}',[App\Http\Controllers\Api\WisataController::class, 'loadDetailitinerary']);
Route::get('/wisata/{wisataid}',[App\Http\Controllers\Api\WisataController::class, 'Wisatapage']);
Route::get('/checkid/{itineraryId}',[App\Http\Controllers\Api\WisataController::class, 'isIdInDatabase']);
Route::get('/inspirasiperjalanan',[App\Http\Controllers\Api\WisataController::class, 'inspirasiItinerary']);
Route::post('/create-itinerary', [App\Http\Controllers\Api\WisataController::class, 'saveItineraryToJSON']);
Route::middleware('auth:api')->get('/trip-planner', [App\Http\Controllers\Api\WisataController::class, 'generateTrip']);
Route::delete('/hapusitinerary/{itineraryId}',[App\Http\Controllers\Api\WisataController::class, 'deleteItinerary']);
Route::get('/generatebackpacker',[App\Http\Controllers\Api\WisataController::class, 'tripBackpacker']);
Route::get('/generatekeluarga',[App\Http\Controllers\Api\WisataController::class, 'tripKeluarga']);
Route::get('/generategrup',[App\Http\Controllers\Api\WisataController::class, 'tripGrup']);
Route::middleware('auth:api')->post('/saveitineraryuser/{itineraryId}',[App\Http\Controllers\Api\WisataController::class, 'saveItineraryUser']);
Route::middleware('auth:api')->post('/saveitinerarybackpacker/{itineraryId}',[App\Http\Controllers\Api\WisataController::class, 'saveItineraryUserBackpacker']);