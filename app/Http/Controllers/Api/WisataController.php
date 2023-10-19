<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Kategori;
use Illuminate\Support\Carbon;
use App\Models\UserLike;
use App\Models\History;
use Illuminate\Support\Facades\Auth;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Persisters\Serialize;
use Illuminate\Support\Facades\File;
use Rubix\ML\Persisters\Filesystem;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Extractors\Json;
use Rubix\ML\Transformers\VectorAssembler;
use Rubix\ML\Datasets\Unlabeled;


class WisataController extends Controller
{
    public function listhotel(Request $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $limit = $request->input('limit', 50);
        $radius = $request->input('radius', 5);
        $locationsJson = \File::get(base_path('public/dbhotel/hotel.json'));
        $locations = json_decode($locationsJson, true);

        $nearestLocations = [];
        $nearestDistance = [];

        foreach ($locations as $location) {
            $distance = $this->haversineDistances($latitude, $longitude, $location['latitude'], $location['longitude']);

            if ($distance <= $radius) {
                $location['distance'] = $distance;
                $nearestLocations[] = $location;
            }
        }

        usort($nearestLocations, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $nearestLocations = array_slice($nearestLocations, 0, $limit);

        return response()->json($nearestLocations);
        return response()->json($distance);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // in kilometers
    
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;
    
        $a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $earthRadius * $c; // Distance in kilometers
    
        return $distance;
    }

    private function haversineDistances($lat1, $lon1, $lat2, $lon2) {
        $lat1 = floatval($lat1);
        $lon1 = floatval($lon1);
        $lat2 = floatval($lat2);
        $lon2 = floatval($lon2);
        $earthRadius = 6371; // Earth's radius in kilometers
    
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
    
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
    
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $earthRadius * $c;
    
        return $distance;
    }

    private function haversineDistancee($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Earth's radius in kilometers
    
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
    
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
    
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $earthRadius * $c;
    
        return $distance;
    }

    function cariRestoranTerdekat(
        $userLatitude,
        $userLongitude,
        $slot,
        $visitedAttractions,
        $resto
    ) {
        $nearestRestaurant = null;
        $minDistance = PHP_FLOAT_MAX;
        $filteredRestaurants = array_filter($resto, function ($restaurant) use ($slot) {
            return $restaurant['rekomendasi'] === $slot;
        });
    
        foreach ($visitedAttractions as $visitedAttraction) {
            foreach ($filteredRestaurants as $restaurant) {
                $distance = haversineDistances(
                    $visitedAttraction['latitude'],
                    $visitedAttraction['longitude'],
                    $restaurant['latitude'],
                    $restaurant['longitude']
                );
    
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestRestaurant = $restaurant;
                }
            }
        }
    
        return $nearestRestaurant;
    }

    public function tripGrup(Request $request){
    $jumlahHariWisata = $request->input('jumlah_hari');
    $transport = $request->input('transport');
    $tipewisata = $request->input('tipe_wisata');
    $userLatitude = deg2rad(-7.7892805);
    $userLongitude = deg2rad(110.3608548);
    $restoran = \File::get(base_path('public/dbrestoran/resto.json'));
    $resto = json_decode($restoran, true);
    $hotel = \File::get(base_path('public/dbhotel/hotelgrup.json'));
    $hotels = json_decode($hotel,true);

    $mobil = \File::get(base_path('public/dbtransport/trnsportgrup.json'));
    $oleh = \File::get(base_path('public/dbwisata/wisataoleholeh.json'));
    $oleholeh = json_decode($oleh, true);
    $mobils = json_decode($mobil,true);   

    if($tipewisata == 'Gathering'){
        $placez = \File::get(base_path('public/dbwisata/wisatagrup.json'));
        $places = json_decode($placez, true);
    }

    if($tipewisata == 'Study_Tour'){
        $placez = \File::get(base_path('public/dbwisata/wisataedukasi.json'));
        $places = json_decode($placez, true);
    }

    foreach ($places as $location) {
        $locationLat = deg2rad($location['latitude']);
        $locationLon = deg2rad($location['longitude']); 

        $distance = $this->haversineDistance($userLatitude, $userLongitude, $locationLat, $locationLon);

        $location['distance'] = $distance; 
        $locationsWithDistance[] = $location;
    }

    $orderedPlaces = $locationsWithDistance;
    usort($orderedPlaces, function ($a, $b) {
        return $a['distance'] <=> $b['distance']; 
    });

$tripPlan = [];
$placesPerDay = array_chunk($orderedPlaces, 2);
$timeslots = ['pagi', 'siang', 'malam'];
$timeslotsMakan = ['MakanPagi', 'MakanSiang', 'MakanMalam'];
$hotelStatus = 'Check in hotel'; 
$firstDay = true;
$visitedRestaurants = [];
$visitedKuliner = [];
$visitedOleh = [];
$visitedAttractionsToday = [];
for ($day = 0; $day < $jumlahHariWisata; $day++) {
    if (isset($placesPerDay[$day])) {
        $places = $placesPerDay[$day];
        $dailyPlan = [];
        foreach ($timeslots as $index => $slot) {
            if (isset($places[$index]) && $places[$index] !== 'Tidak ada aktivitas wisata.') {
                
                if ($slot === 'pagi' && $firstDay) {

                    if($transport == 'Kurang dari 50'){
                        if($places[0]){
                            $filteredMobil = array_filter($mobils, function ($kapasitas){
                                return in_array($kapasitas['kapasitas'], ['<50']);
                            });
                            $dailyPlan['mobil'] = $filteredMobil;
                        }
                    }
                    if($transport == 'Lebih dari 50'){
                        if($places[0]){
                            $filteredMobil = array_filter($mobils, function ($kapasitas){
                                return in_array($kapasitas['kapasitas'], ['>50']);
                            });
                            $dailyPlan['mobil'] = $filteredMobil;
                        }
                    }
                    
                    $nearestHotel = null;
                    $minHotelDistance = PHP_FLOAT_MAX;
                    $minDistance = PHP_FLOAT_MAX;
                
        
                    foreach ($hotels as $hotel) {
                        $distance = $this->haversineDistances($places[0]['latitude'], $places[0]['longitude'], $hotel['latitude'], $hotel['longitude']);

                        if ($distance < $minHotelDistance) {
                            $minHotelDistance = $distance;
                            $nearestHotel = $hotel;
                        }
                    }

                    if ($nearestHotel) {
                        $dailyPlan['Hotel'] = $nearestHotel;
                        $hotelStatus = 'Persiapan ke lokasi wisata'; 
                    }

                    $firstDay = false; 
                }
               
                $dailyPlan[$slot] = $places[$index];
                if ($slot === 'siang') {
                    $nearestOleh = null;
                    $minDistance = PHP_FLOAT_MAX;

                    foreach ($oleholeh as $olehan) {
                        $distance = $this->haversineDistances($places[$index]['latitude'], $places[$index]['longitude'], $olehan['latitude'], $olehan['longitude']);
                    
                        if ($distance < $minDistance && !in_array($olehan['nama'], $visitedOleh)) {
                            $minDistance = $distance;
                            $nearestOleh = $olehan;
                        }
                    }

                    if ($nearestOleh) {
                        $dailyPlan["wisataoleholeh"] = $nearestOleh;
                        $visitedOleh[$day][] = $nearestOleh;
                        $oleholeh = array_filter($oleholeh, function ($olehan) use ($nearestOleh) {
                            return $olehan['nama'] !== $nearestOleh['nama'];
                        });
                    }
                }

                
                $nearestRestaurant = null;
                $nearestKuliner = null;
                $nearestOleh = null;
                $minDistance = PHP_FLOAT_MAX;

                $filteredRestaurants = array_filter($resto, function ($grup){
                    return in_array($grup['grup'], ['1']);
                });


                foreach ($filteredRestaurants  as $restaurant) {
                        $distance = $this->haversineDistances($places[$index]['latitude'], $places[$index]['longitude'], $restaurant['latitude'], $restaurant['longitude']);
                        if ($distance < $minDistance && !in_array($restaurant['nama'], $visitedRestaurants)) {
                            $minDistance = $distance;
                            $nearestRestaurant = $restaurant;
                        }
                }

                if ($nearestRestaurant) {
                    $dailyPlan["Makan$slot"] = $nearestRestaurant;
                    $visitedRestaurants[$day][] = $nearestRestaurant;
                    $resto = array_filter($resto, function ($restaurant) use ($nearestRestaurant) {
                        return $restaurant['nama'] !== $nearestRestaurant['nama'];
                    });
                }
            } 
        }
        if (!isset($dailyPlan['Hotel'])) {
            $dailyPlan['Hotel'] = $hotelStatus;
        }
        
        $dailyPlan['Malam'] = 'Kembali ke hotel';

        
        $tripPlan[] = [
            'Hari' => $day + 1,
            'Tempatwisata' => $dailyPlan ,
        ];
    }
}

$itineraryId = 'IT_' . uniqid(); 
return response()->json([
    'trip_plan' => $tripPlan,
    'itinerary_id' => $itineraryId,
]);

    return response()->json($orderedPlaces);
    }

    public function tripKeluarga(Request $request)
    {
    $jumlahHariWisata = $request->input('jumlah_hari');
    $transport = $request->input('transport');
    $inputhotel = $request->input('budget');
    $paketinput = $request->input('paket');
    $latitude = $request->input('latitude');
    $longitude = $request->input('longitude');
    $mobil = \File::get(base_path('public/dbtransport/transportkeluarga.json'));
    $mobils = json_decode($mobil,true);
    $restoran = \File::get(base_path('public/dbrestoran/restokeluarga.json'));
    $resto = json_decode($restoran, true);
    $hotel = \File::get(base_path('public/dbhotel/hotelbackpacker.json'));
    $hotels = json_decode($hotel,true);
    $placez = \File::get(base_path('public/dbwisata/wisatakeluarga.json'));
    $places = json_decode($placez, true);
    $kuliners = \File::get(base_path('public/dbwisata/wisatakuliner.json'));
    $kuliner = json_decode($kuliners, true);
    $oleh = \File::get(base_path('public/dbwisata/wisataoleholeh.json'));
    $oleholeh = json_decode($oleh, true);
    $pakets = \File::get(base_path('public/dbpaketwisata/paketwisata.json'));
    $paket = json_decode($pakets, true);

    if($inputhotel == 'Ekonomis'){
        $inputhotel =['1.0', '2.0'];
    } if($inputhotel == 'Sedang'){
        $inputhotel =['3.0', '4.0'];
    } if($inputhotel == 'Mahal'){
        $inputhotel =['4.0', '5.0'];
    }
    

    if($transport == 'Kereta'){
        $userLatitude = deg2rad(-7.7892802);
        $userLongitude = deg2rad(110.3611012);
    } if($transport == 'Pesawat'){
        $userLatitude = deg2rad(-7.831796);
        $userLongitude = deg2rad(110.3083358);
    } if($transport == 'Pribadi'){
        $userLatitude = deg2rad($latitude);
        $userLongitude = deg2rad($longitude);
    } if($transport == 'Lainnya'){
        $userLatitude = deg2rad(-7.7829218);
        $userLongitude = deg2rad(110.3645008);
    }

    foreach ($places as $location) {
        $locationLat = deg2rad($location['latitude']);
        $locationLon = deg2rad($location['longitude']); 

        $distance = $this->haversineDistance($userLatitude, $userLongitude, $locationLat, $locationLon);

        $location['distance'] = $distance; 
        $locationsWithDistance[] = $location;
    }

    $orderedPlaces = $locationsWithDistance;
    usort($orderedPlaces, function ($a, $b) {
        return $a['distance'] <=> $b['distance']; 
    });


    
$tripPlan = [];
$placesPerDay = array_chunk($orderedPlaces, 2);
$timeslots = ['pagi', 'siang', 'malam'];
$timeslotsMakan = ['MakanPagi', 'MakanSiang', 'MakanMalam'];
$hotelStatus = 'Check in hotel'; 
$firstDay = true;
$visitedRestaurants = [];
$visitedKuliner = [];
$visitedOleh = [];
$visitedAttractionsToday = [];
for ($day = 0; $day < $jumlahHariWisata; $day++) {
    if (isset($placesPerDay[$day])) {
        $places = $placesPerDay[$day];
        $dailyPlan = [];

        $filteredHotel = array_filter($hotels, function ($jenishotel) use ($inputhotel){
            return in_array($jenishotel['hotelClass'], $inputhotel);
        });
        
        foreach ($timeslots as $index => $slot) {
            if (isset($places[$index]) && $places[$index] !== 'Tidak ada aktivitas wisata.') {
                if ($slot === 'pagi' && $firstDay) {
                    $nearestHotel = null;
                    $minHotelDistance = PHP_FLOAT_MAX;
                    $minDistance = PHP_FLOAT_MAX;

                    if($transport == 'Kereta'){
                    if($places[0]){
                        $dailyPlan['sewa'] = $mobils;
                    }
                }
                if($transport == 'Pesawat'){
                    if($places[0]){
                        $dailyPlan['sewa'] = $mobils;
                    }
                }
                if($transport == 'Lainnya'){
                    if($places[0]){
                        $dailyPlan['sewa'] = $mobils;
                    }
                }
                if($paketinput == 'Ya'){
                    if($places[0]){
                        $dailyPlan['paketwisata'] = $paket;
                    }
                }else{
                    $dailyPlan['paketwisata'] = [];
                }
                
        
                    foreach ($filteredHotel as $hotel) {
                        $distance = $this->haversineDistances($places[0]['latitude'], $places[0]['longitude'], $hotel['latitude'], $hotel['longitude']);

                        if ($distance < $minHotelDistance) {
                            $minHotelDistance = $distance;
                            $nearestHotel = $hotel;
                        }
                    }

                    if ($nearestHotel) {
                        $dailyPlan['Hotel'] = $nearestHotel;
                        $hotelStatus = 'Persiapan ke lokasi wisata'; 
                    }

                    $firstDay = false; 
                }
               
                $dailyPlan[$slot] = $places[$index];
                if ($slot === 'pagi') {
                    $nearestOleh = null;
                    $minDistance = PHP_FLOAT_MAX;

                    foreach ($oleholeh as $olehan) {
                        $distance = $this->haversineDistances($places[$index]['latitude'], $places[$index]['longitude'], $olehan['latitude'], $olehan['longitude']);
                    
                        if ($distance < $minDistance && !in_array($olehan['nama'], $visitedOleh)) {
                            $minDistance = $distance;
                            $nearestOleh = $olehan;
                        }
                    }

                    if ($nearestOleh) {
                        $dailyPlan["wisataoleholeh"] = $nearestOleh;
                        $visitedOleh[$day][] = $nearestOleh;
                        $oleholeh = array_filter($oleholeh, function ($olehan) use ($nearestOleh) {
                            return $olehan['nama'] !== $nearestOleh['nama'];
                        });
                    }
                }

                
                $nearestRestaurant = null;
                $nearestKuliner = null;
                $nearestOleh = null;
                $minDistance = PHP_FLOAT_MAX;


                foreach ($resto  as $restaurant) {
                        $distance = $this->haversineDistances($places[$index]['latitude'], $places[$index]['longitude'], $restaurant['latitude'], $restaurant['longitude']);
                        if ($distance < $minDistance && !in_array($restaurant['nama'], $visitedRestaurants)) {
                            $minDistance = $distance;
                            $nearestRestaurant = $restaurant;
                        }
                }

                if ($nearestRestaurant) {
                    $dailyPlan["Makan$slot"] = $nearestRestaurant;
                    $visitedRestaurants[$day][] = $nearestRestaurant;
                    $resto = array_filter($resto, function ($restaurant) use ($nearestRestaurant) {
                        return $restaurant['nama'] !== $nearestRestaurant['nama'];
                    });
                }
            } 
        }
        if (!isset($dailyPlan['Hotel'])) {
            $dailyPlan['Hotel'] = $hotelStatus;
        }
        
        $dailyPlan['Malam'] = 'Kembali ke hotel';

        
        $tripPlan[] = [
            'Hari' => $day + 1,
            'Tempatwisata' => $dailyPlan ,
        ];
    }
}

$itineraryId = 'IT_' . uniqid(); 
return response()->json([
    'trip_plan' => $tripPlan,
    'itinerary_id' => $itineraryId,
]);
    }

    public function rekomendasiHotelkeluarga(){
        $hotel = \File::get(base_path('public/dbhotel/hotel.json'));
        $hotels = json_decode($hotel,true);
        $inputhotel = ['4.0'];
        $filteredHotel = array_filter($hotels, function ($jenishotel) use ($inputhotel){
            return in_array($jenishotel['hotelClass'], $inputhotel);
        });
        return response()->json($filteredHotel);
    }

    public function rekomendasiHotelgrup(){
        $hotel = \File::get(base_path('public/dbhotel/hotelgrup.json'));
        $hotels = json_decode($hotel,true);
        return response()->json($hotels);
    }

    public function deleteItinerary($itineraryId) {
            History::where('link', $itineraryId)->delete();
            $directoryPath = public_path("/dataitinerarybyuser/{$itineraryId}.json");
            File::delete($directoryPath);
    }
    

    public function cekinspirasi(){
       $cek = \DB::table('historys')->get();
       return response()->json($cek);
    }
    public function rekomendasirestoGrup(){
        $restoran = \File::get(base_path('public/dbrestoran/resto.json'));
        $resto = json_decode($restoran, true);
        $inputresto = ['1'];
        $filteredResto = array_filter($resto, function ($masakan) use ($inputresto){
            return in_array($masakan['grup'], $inputresto);
        });
        return response()->json($filteredResto); 
    }

    public function rekomendasirestoKeluarga(){
    $restoran = \File::get(base_path('public/dbrestoran/resto.json'));
    $resto = json_decode($restoran, true);
    $inputresto = ['Khas', 'Jawa'];
    $filteredResto = array_filter($resto, function ($masakan) use ($inputresto){
        return in_array($masakan['masakan'], $inputresto);
    });
    return response()->json($filteredResto);
    }
    
        
    public function tripBackpacker(Request $request)
    {
    $jumlahHariWisata = $request->input('jumlah_hari');
    $transport = $request->input('transport');
    $motor = \File::get(base_path('public/dbtransport/transport.json'));
    $motors = json_decode($motor,true);
    $restoran = \File::get(base_path('public/dbrestoran/resto.json'));
    $resto = json_decode($restoran, true);
    $makanan = ["$","$$"];
    $hotel = \File::get(base_path('public/dbhotel/hotelbackpacker.json'));
    $hotels = json_decode($hotel,true);
    $placez = \File::get(base_path('public/dbwisata/wisatabackpacker.json'));
    $places = json_decode($placez, true);
    $inputPrice = '200000';
    $latitude = $request->input('latitude');
    $longitude = $request->input('longitude');

    if($transport == 'Kereta'){
        $userLatitude = deg2rad(-7.7892802);
        $userLongitude = deg2rad(110.3611012);
    } if($transport == 'Pesawat'){
        $userLatitude = deg2rad(-7.831796);
        $userLongitude = deg2rad(110.3083358);
    } if($transport == 'Pribadi'){
        $userLatitude = deg2rad($latitude);
        $userLongitude = deg2rad($longitude);
    } if($transport == 'Lainnya'){
        $userLatitude = deg2rad(-7.7829218);
        $userLongitude = deg2rad(110.3645008);
    }
foreach ($places as $location) {
    $locationLat = deg2rad($location['latitude']);
    $locationLon = deg2rad($location['longitude']); 

    $distance = $this->haversineDistance($userLatitude, $userLongitude, $locationLat, $locationLon);

    $location['distance'] = $distance; 
    $locationsWithDistance[] = $location;
}

$orderedPlaces = $locationsWithDistance;
usort($orderedPlaces, function ($a, $b) {
    return $a['distance'] <=> $b['distance']; 
});

    
$tripPlan = [];
$placesPerDay = array_chunk($orderedPlaces, 3);
$timeslots = ['pagi', 'siang', 'malam'];
$timeslotsMakan = ['MakanPagi', 'MakanSiang', 'MakanMalam'];
$hotelStatus = 'Check in hotel'; 
$firstDay = true;
$visitedRestaurants = [];
$visitedAttractionsToday = [];
for ($day = 0; $day < $jumlahHariWisata; $day++) {
    if (isset($placesPerDay[$day])) {
        $places = $placesPerDay[$day];
        $dailyPlan = [];

        $filteredHotel = array_filter($hotels, function ($hotel) use ($inputPrice) {
            $priceRange = $hotel['priceRange'];
            $priceValues = explode('-', $priceRange);
            
            if (count($priceValues) === 2) {
                $lowerPrice = (float)str_replace('.', '', $priceValues[0]);
                $inputPrice = (float)str_replace('.', '', $inputPrice);
        
                return $lowerPrice <= $inputPrice;
            } else {
                return false;
            }
        });


        foreach ($timeslots as $index => $slot) {
            if (isset($places[$index]) && $places[$index] !== 'Tidak ada aktivitas wisata.') {
                if ($slot === 'pagi' && $firstDay) {
                    $nearestHotel = null;
                    $minHotelDistance = PHP_FLOAT_MAX;

                    if($transport == 'Kereta'){
                    if($places[0]){
                        $dailyPlan['sewa'] = $motors;
                    }
                }
                if($transport == 'Pesawat'){
                    if($places[0]){
                        $dailyPlan['sewa'] = $motors;
                    }
                }
                if($transport == 'Lainnya'){
                    if($places[0]){
                        $dailyPlan['sewa'] = $motors;
                    }
                }
                    
                    foreach ($filteredHotel as $hotel) {
                        $distance = $this->haversineDistances($places[0]['latitude'], $places[0]['longitude'], $hotel['latitude'], $hotel['longitude']);

                        if ($distance < $minHotelDistance) {
                            $minHotelDistance = $distance;
                            $nearestHotel = $hotel;
                        }
                    }

                    if ($nearestHotel) {
                        $dailyPlan['Hotel'] = $nearestHotel;
                        $hotelStatus = 'Persiapan ke lokasi wisata'; 
                    }

                    $firstDay = false; 
                }
               
                $dailyPlan[$slot] = $places[$index];
                
                $nearestRestaurant = null;
                $minDistance = PHP_FLOAT_MAX;

                $filteredRestoran = array_filter($resto, function ($tempatmakan) use ($makanan) {
                    return in_array($tempatmakan['harga'], $makanan);
                });

                foreach ($filteredRestoran  as $restaurant) {
                        $distance = $this->haversineDistances($places[$index]['latitude'], $places[$index]['longitude'], $restaurant['latitude'], $restaurant['longitude']);
                
                        if ($distance < $minDistance && !in_array($restaurant['nama'], $visitedRestaurants)) {
                            $minDistance = $distance;
                            $nearestRestaurant = $restaurant;
                        }
                }

                if ($nearestRestaurant) {
                    $dailyPlan["Makan$slot"] = $nearestRestaurant;
                    $visitedRestaurants[$day] = $nearestRestaurant;
                    $filteredRestoran = array_filter($filteredRestoran, function ($restaurant) use ($nearestRestaurant) {
                        return $restaurant['nama'] !== $nearestRestaurant['nama'];
                    }); 
                }
                    
                
            } 
        }
        if (!isset($dailyPlan['Hotel'])) {
            $dailyPlan['Hotel'] = $hotelStatus;
        }
        
        $dailyPlan['Malam'] = 'Kembali ke hotel';

        $tripPlan[] = [
            'Hari' => $day + 1,
            'Tempatwisata' => $dailyPlan ,
        ];
    } else {
        break;
    }
    
}
$itineraryId = 'IT_' . uniqid(); 
return response()->json([
    'trip_plan' => $tripPlan,
    'itinerary_id' => $itineraryId,
]);
    }
    
    public function generateTrip(Request $request)
{
    $jumlahHariWisata = $request->input('jumlah_hari');
    $userLatitude = deg2rad($request->input('user_latitude')) ;
    $userLongitude = deg2rad($request->input('user_longitude'));
    $makanan = $request->input('makanan');
    $inputresto= $makanan;
    $inputhotel = $request->input('hotel');
    $user = JWTAuth::user();
    $places = UserLike::where('users_id', $user->id)->get();
    $restoran = \File::get(base_path('public/dbrestoran/resto.json'));
    $resto = json_decode($restoran, true);
    $hotel = \File::get(base_path('public/dbhotel/hotel.json'));
    $hotels = json_decode($hotel,true);
    $locationsWithDistance = [];

    foreach ($places as $location) {
        $locationLat = deg2rad($location->latitude);
        $locationLon = deg2rad($location->longitude); 

        $distance = $this->haversineDistance($userLatitude, $userLongitude, $locationLat, $locationLon);

        $location->distance = $distance; 
        $locationsWithDistance[] = $location;
    }

    $orderedPlaces = $locationsWithDistance;
    usort($orderedPlaces, function ($a, $b) {
        return $a->distance <=> $b->distance; 
    });


    
$tripPlan = [];
$placesPerDay = array_chunk($orderedPlaces, 3);
$timeslots = ['pagi', 'siang', 'malam'];
$timeslotsMakan = ['MakanPagi', 'MakanSiang', 'MakanMalam'];
$hotelStatus = 'Check in hotel'; 
$firstDay = true;
$visitedRestaurants = [];
$visitedAttractionsToday = [];
for ($day = 0; $day < $jumlahHariWisata; $day++) {
    if (isset($placesPerDay[$day])) {
        $places = $placesPerDay[$day];
        $dailyPlan = [];
        
        if($inputhotel == 'OYO'){
            $filteredHotel = array_filter($hotels, function ($jenishotel) {
                return strstr($jenishotel['name'], 'OYO');
            });
        }else if($inputhotel == 'RedDoorz'){
            $filteredHotel = array_filter($hotels, function ($jenishotel) {
                return strstr($jenishotel['name'], 'RedDoorz');
            });
        } else if ($inputhotel !== 'OYO' && 'RedDoorz'){
            $filteredHotel = array_filter($hotels, function ($jenishotel) use ($inputhotel) {
                return strstr($jenishotel['hotelClass'], $inputhotel);
            });
        }

        foreach ($timeslots as $index => $slot) {
            if (isset($places[$index]) && $places[$index] !== 'Tidak ada aktivitas wisata.') {
                if ($slot === 'pagi' && $firstDay) {
                    $nearestHotel = null;
                    $minHotelDistance = PHP_FLOAT_MAX;

                    foreach ($filteredHotel as $hotel) {
                        $distance = $this->haversineDistances($places[0]['latitude'], $places[0]['longitude'], $hotel['latitude'], $hotel['longitude']);

                        if ($distance < $minHotelDistance) {
                            $minHotelDistance = $distance;
                            $nearestHotel = $hotel;
                        }
                    }

                    if ($nearestHotel) {
                        $dailyPlan['Hotel'] = $nearestHotel;
                        $hotelStatus = 'Persiapan ke lokasi wisata'; 
                    }

                    $firstDay = false; 
                }
               
                $dailyPlan[$slot] = $places[$index];
                
                $nearestRestaurant = null;
                $minDistance = PHP_FLOAT_MAX;

                $filteredRestoran = array_filter($resto, function ($tempatmakan) use ($makanan) {
                    return in_array($tempatmakan['masakan'], $makanan);
                });

                foreach ($filteredRestoran  as $restaurant) {
                    if ($restaurant['rekomendasi'] === $slot) {
                        $distance = $this->haversineDistances($places[$index]['latitude'], $places[$index]['longitude'], $restaurant['latitude'], $restaurant['longitude']);
                
                        if ($distance < $minDistance && !in_array($restaurant['nama'], $visitedRestaurants)) {
                            $minDistance = $distance;
                            $nearestRestaurant = $restaurant;
                        }
                    }
                }

                if ($nearestRestaurant) {
                    $dailyPlan["Makan$slot"] = $nearestRestaurant;
                    $visitedRestaurants[] = $nearestRestaurant; 
                }
                    
                
            } 
        }
        if (!isset($dailyPlan['Hotel'])) {
            $dailyPlan['Hotel'] = $hotelStatus;
        }
        
        $dailyPlan['Malam'] = 'Kembali ke hotel';

        $tripPlan[] = [
            'Hari' => $day + 1,
            'Tempatwisata' => $dailyPlan ,
        ];
    } else {
        break;
    }
    
}
$itineraryId = 'IT_' . uniqid(); 

return response()->json([
    'trip_plan' => $tripPlan,
    'itinerary_id' => $itineraryId,
    'makanan' => $inputresto
]);
}

    public function listresto(Request $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $limit = $request->input('limit', 100);
        $radius = $request->input('radius', 5);
        $locationsJson = \File::get(base_path('public/dbrestoran/restoran.json'));
        $locations = json_decode($locationsJson, true);

        $nearestLocations = [];
        $nearestDistance = [];

        foreach ($locations as $location) {
            $distance = $this->haversineDistances($latitude, $longitude, $location['latitude'], $location['longitude']);

            if ($distance <= $radius) {
                $location['distance'] = $distance;
                $nearestLocations[] = $location;
            }
        }

        usort($nearestLocations, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $nearestLocations = array_slice($nearestLocations, 0, $limit);

        return response()->json($nearestLocations);
        return response()->json($distance);
    }

    public function kategoriwisata(){
        $kategoriJson = \File::get(base_path('public/dbwisata/kategori.json'));
        $kategori = json_decode($kategoriJson, true);
        return response()->json($kategori);
    }

    public function wisatabykategori(Request $request, $kategori)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $wisataJson = \File::get(base_path('public/dbwisata/wisatadataset.json'));
        $wisata = json_decode($wisataJson, true);
        $nearestLocations = [];
    
        foreach ($wisata as $location) {
            $locationLat = $location['latitude'];
            $locationLon = $location['longitude'];
    
            $distance = $this->haversineDistances($latitude, $longitude, $locationLat, $locationLon);
    
            $location['distance'] = $distance; // Add the distance to the location data
            $nearestLocations[] = $location;
        }
    
        $filteredWisata = array_filter($nearestLocations, function ($item) use ($kategori) {
            return empty($kategori) || $item['kategori'] === $kategori;
        });
    
        return response()->json($filteredWisata);
    }
    

    public function topwisata(){
        $wisataJson = \File::get(base_path('public/dbwisata/wisatadataset.json')); 
        $wisata = json_decode($wisataJson, true);
        usort($wisata, function ($a, $b) {
            return $b['jumlahrating'] - $a['jumlahrating'];
        });
        $top10Wisata = array_slice($wisata, 0, 10);
        return response()->json($top10Wisata);
    }

    public function getkategori($kategori){
        $kategoriJson = \File::get(base_path('public/dbwisata/kategori.json')); 
        $namakategori = json_decode($kategoriJson, true);

        $filteredKategori = array_filter($namakategori, function ($item) use ($kategori) {
            return (
                (empty($kategori) || $item['namakategori'] === $kategori)
            );
        });

        return response()->json($filteredKategori);
    }

    public function rekomendasibytipe($tipe){
        $wisataJson = \File::get(base_path('public/dbwisata/wisatadataset.json'));
        $wisata = json_decode($wisataJson, true); 
        $rekomentipe = array_filter($wisata, function($item) use ($tipe){
            return (
                (empty($tipe) || $item[$tipe] === 'yes')
            );
        });

        return response()->json($rekomentipe);
    }


    public function explorewisata(Request $request)
{
    $latitude = deg2rad($request->latitude); 
    $longitude = deg2rad($request->longitude); 
    $radius = $request->input('radius', 5); 

    $locationsJson = \File::get(base_path('public/dbwisata/wisatadataset.json'));
    $locations = json_decode($locationsJson, true);

    $nearestLocations = [];

    foreach ($locations as $location) {
        $locationLat = deg2rad($location['latitude']); 
        $locationLon = deg2rad($location['longitude']); 

        $distance = $this->haversineDistance($latitude, $longitude, $locationLat, $locationLon);

        if ($distance <= $radius) {
            $location['distance'] = $distance; 
            $nearestLocations[] = $location;
        }
    }

    usort($nearestLocations, function ($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });

    return response()->json($nearestLocations);
}

public function getUserLikeswisata(Request $request)
{
    $latitude = deg2rad($request->latitude); 
    $longitude = deg2rad($request->longitude); 
    $user = JWTAuth::user();
    $likedAttractions = UserLike::where('users_id', $user->id)->get();
    $locationsWithDistance = [];

    foreach ($likedAttractions as $location) {
        $locationLat = deg2rad($location->latitude);
        $locationLon = deg2rad($location->longitude); 

        $distance = $this->haversineDistance($latitude, $longitude, $locationLat, $locationLon);

        $location->distance = $distance; 
        $locationsWithDistance[] = $location;
    }

    usort($locationsWithDistance, function ($a, $b) {
        return $a->distance <=> $b->distance; 
    });

    return response()->json($locationsWithDistance);
}

public function getItinerarybyuser(Request $request){
    $user = JWTAuth::user();
    $itinerary = History::where('users_id',$user->id)->get();
    return response()->json($itinerary);
}

public function mapslocation(Request $request){
    $locationsJson = \File::get(base_path('public/dbwisata/wisatadataset.json'));
    $locations = json_decode($locationsJson, true);
    return response()->json($locations);
}

public function mapshotel(Request $request){
    $locationsJson = \File::get(base_path('public/dbhotel/hotel.json'));
    $locations = json_decode($locationsJson, true);
    return response()->json($locations);
}


    public function cuaca(Request $request)
    {
        $apiKey = 'aa6b1088a41e85b147ab21a88458a408'; 
        $location = $request->input('location', 'Yogyakarta'); 

        $client = new Client();
        $response = $client->get("http://api.weatherstack.com/current", [
            'query' => [
                'access_key' => $apiKey,
                'query' => $location,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return response()->json($data);
    }   

    public function addLove(Request $request, $attractionId)
{
    
    $user = JWTAuth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $jsonData = \File::get(base_path('public/dbwisata/wisatadataset.json'));
    $attractionsData = json_decode($jsonData, true);
    $indexedAttractionsData = [];
    foreach ($attractionsData as $attraction) {
        $indexedAttractionsData[$attraction['id']] = $attraction;
    }

    if (array_key_exists($attractionId, $indexedAttractionsData)) {
        UserLike::create([
            'users_id' => $user->id,
            'wisata_id' => $attractionId,
            'nama' => $indexedAttractionsData[$attractionId]['nama'],
            'latitude' => $indexedAttractionsData[$attractionId]['latitude'],
            'longitude' => $indexedAttractionsData[$attractionId]['longitude'],
            'rating' => $indexedAttractionsData[$attractionId]['rating'],
            'image' => $indexedAttractionsData[$attractionId]['image'],
            'url_maps' => $indexedAttractionsData[$attractionId]['url_maps'],
            'kategori' => $indexedAttractionsData[$attractionId]['kategori'],
            'jenis_wisata' => $indexedAttractionsData[$attractionId]['jeniswisata'],
            'deskripsi' => $indexedAttractionsData[$attractionId]['deskripsi'],
            'anak' => $indexedAttractionsData[$attractionId]['anak'],
            'lansia' => $indexedAttractionsData[$attractionId]['lansia'],
            'isnight' => $indexedAttractionsData[$attractionId]['isnight'],
            'descitinerary' => $indexedAttractionsData[$attractionId]['descitinerary'],
            'htm_weekday' => $indexedAttractionsData[$attractionId]['htm_weekday'],
            'htm_weekend' => $indexedAttractionsData[$attractionId]['htm_weekend'],
            
        ]);
       
    } else {
       
        return response()->json(['error' => 'Invalid attraction ID'], 400);
    }
}

public function saveItineraryUser(Request $request, $itineraryId){
    $user = JWTAuth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    History::create([
        'users_id' => $user->id,
        'caption' => $request->caption,
        'share' => $request->share,
        'tipe' => $request->tipe,
        'judul' => $request->judul,
        'image' => $request->image,
        'link' => $itineraryId,
        'nama_user' => $user->name,
        'img_user' => $user->image
    ]);
}

function isIdInDatabase($itineraryId) {
    $history = History::where('link', $itineraryId)->first();
    return !!$history;
}

public function removeLove(Request $request, $attractionId)
{
    $user = JWTAuth::user();
    UserLike::where('users_id', $user->id)
            ->where('wisata_id', $attractionId)
            ->delete();

    return response()->json(['message' => 'Attraction unliked successfully']);
}

public function getUserLikes(Request $request)
{
    $user = JWTAuth::user();
    $likedAttractions = UserLike::where('users_id', $user->id)->pluck('wisata_id');
    return response()->json($likedAttractions);
}



public function getUserBasedRecommendations(Request $request)
{
    $userId = $request->user()->id;
    $jsonData = \File::get(base_path('public/dbwisata/wisatadataset.json'));
    $attractions = json_decode($jsonData, true);
    $userLikedAttractions = UserLike::where('users_id', $userId)->pluck('jenis_wisata');

    $recommendedAttractions = [];
    foreach ($userLikedAttractions as $jenisWisata) {
        $similarAttractions = array_filter($attractions, function ($attraction) use ($jenisWisata) {
            return $attraction['jeniswisata'] === $jenisWisata;
        });
    
      
        $userLikedAttractionIds = UserLike::where('users_id', $userId)->pluck('wisata_id')->all();
        $recommendedAttractions[$jenisWisata] = array_filter($similarAttractions, function ($attraction) use ($userLikedAttractionIds) {
            return !in_array($attraction['id'], $userLikedAttractionIds);
        });
    
        
        usort($recommendedAttractions[$jenisWisata], function ($a, $b) {
            return $b['rating'] - $a['rating'];
        });

        $recommendedAttractions[$jenisWisata] = array_slice($recommendedAttractions[$jenisWisata], 0, 5);
    }
    return response()->json($recommendedAttractions);
}

public function checkWeekdayOrWeekend($date)
{
    $parsedDate = Carbon::parse($date);

    if ($parsedDate->isWeekday()) {
        return response()->json(['result' => "weekday"]);
    } else {
        return response()->json(['result' => "weekend"]);
    }
}

public function saveItineraryToJSON(Request $request)
{
    $itineraryId = $request->input('itinerary_id');
    $tripPlan = $request->input('trip_plan');
    $tripPlanmakanan = $request->input('makanan');


    $jsonTripPlan = json_encode($tripPlan, JSON_UNESCAPED_SLASHES);
    $filePath = public_path("/dataitinerarybyuser/{$itineraryId}.json");
    if (!file_exists(dirname($filePath))) {
        mkdir(dirname($filePath), 0777, true);
    }

    $jsonTripPlanmakanan = json_encode($tripPlanmakanan, JSON_UNESCAPED_SLASHES);
    $filePathmakanan = public_path("/dataitinerarymakanan/{$itineraryId}.json");
    if (!file_exists(dirname($filePathmakanan))) {
        mkdir(dirname($filePathmakanan), 0777, true);
    }



    file_put_contents($filePath, $jsonTripPlan);
    file_put_contents($filePathmakanan, $jsonTripPlanmakanan);

    return response()->json([
        'message' => 'Data itinerary berhasil disimpan ke file JSON.'
    ]);
}

public function loadItinerary($itineraryId){
    $itineraryData = \File::get(base_path("public/dataitinerarybyuser/{$itineraryId}.json"));
    $Data = json_decode($itineraryData, true);
    return response()->json($Data);
}

public function loadrekomendasiresto($itineraryId){
    $itineraryData = \File::get(base_path("public/dataitinerarymakanan/{$itineraryId}.json"));
    $Data = json_decode($itineraryData, true);
    $dataresto = \File::get(base_path('public/dbrestoran/resto.json'));
    $resto = json_decode($dataresto, true);

    $filteredRestoran = array_filter($resto, function ($tempatmakan) use ($Data) {
        return in_array($tempatmakan['masakan'], $Data);
    });
    
    return response()->json($filteredRestoran);
}

public function loadDetailitinerary($itineraryId){
    $itinerarydata = History::where('link', $itineraryId)->get();
    return response()->json($itinerarydata);
}

public function getPaketwisata(){
    $paket = \File::get(base_path('public/dbpaketwisata/paketwisata.json'));
    $paketwisata = json_decode($paket, true);

    return response()->json($paketwisata);
}

public function inspirasiItinerary(){
    $itinerarydata = History::where('share', 1)->get();
    return response()->json($itinerarydata);
}

public function Wisatapage($wisataid){
    $wisataJson = \File::get(base_path('public/dbwisata/wisatadataset.json'));
    $wisata = json_decode($wisataJson, true);
    $filteredWisata = array_filter($wisata, function ($item) use ($wisataid) {
        return empty($wisataid) || $item['id'] === $wisataid;
    });

    return response()->json($filteredWisata);
}

public function hotelbackpacker(){
    $hotel = \File::get(base_path('public/dbhotel/hotelbackpacker.json'));
    $hotels = json_decode($hotel,true);
    $inputPrice = '200000';

    $filteredHotel = array_filter($hotels, function ($hotel) use ($inputPrice) {
        $priceRange = $hotel['priceRange'];
        $priceValues = explode('-', $priceRange);
        
        if (count($priceValues) === 2) {
            $lowerPrice = (float)str_replace('.', '', $priceValues[0]);
            $inputPrice = (float)str_replace('.', '', $inputPrice);
    
            return $lowerPrice <= $inputPrice; 
        } else {
            return false;
        }
    });
    return response()->json($filteredHotel);
}

public function restobackpacker(){
    $restoran = \File::get(base_path('public/dbrestoran/resto.json'));
    $resto = json_decode($restoran, true);
    $makanan = ["$","$$"];
    $filteredRestoran = array_filter($resto, function ($tempatmakan) use ($makanan) {
        return in_array($tempatmakan['harga'], $makanan);
    });
    return response()->json($filteredRestoran);
}

public function restogrup(){
    $restoran = \File::get(base_path('public/dbrestoran/resto.json'));
    $resto = json_decode($restoran, true);
    $makanan = ["1"];
    $filteredRestoran = array_filter($resto, function ($tempatmakan) use ($makanan) {
        return in_array($tempatmakan['grup'], $makanan);
    });
    return response()->json($filteredRestoran);
}

public function kendaraanbackpacker(){
    $motor = \File::get(base_path('public/dbtransport/transportfullpack.json'));
    $motors = json_decode($motor,true);

    return response()->json($motors);
}

public function eksperimen(){
    $latitude = deg2rad(-7.8086832); 
    $longitude = deg2rad(110.3189663); 
    $user = JWTAuth::user();
    $likedAttractions = UserLike::where('users_id', 35)->get();
    $locationsWithDistance = [];

    foreach ($likedAttractions as $location) {
        $locationLat = deg2rad($location->latitude);
        $locationLon = deg2rad($location->longitude); 

        $distance = $this->haversineDistance($latitude, $longitude, $locationLat, $locationLon);

        $location->distance = $distance; 
        $locationsWithDistance[] = $location;
    }

    usort($locationsWithDistance, function ($a, $b) {
        return $a->distance <=> $b->distance; 
    });


return response()->json($locationsWithDistance);
}


}