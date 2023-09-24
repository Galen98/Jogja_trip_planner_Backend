<?php

namespace App\Http\Controllers\Api;
use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artikels;
use App\Models\UserLike;

class ArtikelController extends Controller
{
    public function index(){
        $artikels = Artikels::get();
        return new PostResource(true, 'List Data Artikel', $artikels);
    }

    public function show($artikels){
        $id = Artikels::where('id', $artikels)->get();
        return new PostResource(true, 'Data Post Ditemukan!', $id);
    }

    public function tripKeluarga(Request $request)
    {
        $jumlahHariWisata = $request->input('jumlah_hari');
        $transport = $request->input('transport');
        $inputhotel = $request->input('budget');
        $paketinput = $request->input('paket');
    
        $mobil = \File::get(base_path('public/dbtransport/transportkeluarga.json'));
        $mobils = json_decode($mobil,true);
    
        $restoran = \File::get(base_path('public/dbrestoran/restokeluarga.json'));
        $resto = json_decode($restoran, true);
    
        $makanan = ["Khas","Jawa", "Seafood", "Tradisional"]; // Tambahkan jenis makanan yang diinginkan
    
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
    
        $inputhotel =['4.0', '5.0'];
    
        $latitude = -7.8086832; 
        $longitude = 110.3189663; 
        $locationsWithDistance = [];
    
        foreach ($places as $location) {
            $locationLat = $location['latitude'];
            $locationLon = $location['longitude']; 
    
            $distance = $this->haversineDistances($latitude, $longitude, $locationLat, $locationLon);
    
            $location['distance'] = $distance; 
            $locationsWithDistance[] = $location;
        }
    
        $orderedPlaces = $locationsWithDistance;
        usort($orderedPlaces, function ($a, $b) {
            return $a['distance'] <=> $b['distance']; 
        });
    
        $tripPlan = [];
        $placesPerDay = array_chunk($orderedPlaces, 2);
        $timeslots = ['pagi', 'siang'];
        $timeslotsMakan = ['MakanPagi', 'MakanSiang', 'MakanMalam'];
        $hotelStatus = 'Check in hotel'; 
        $visitedRestaurants = [];
        $visitedKuliner = [];
        $visitedOleh = [];
        $visitedAttractionsToday = [];
    
        for ($day = 0; $day < 2; $day++) {
            if (isset($placesPerDay[$day])) {
                $places = $placesPerDay[$day];
                $dailyPlan = [];
    
                $filteredHotel = array_filter($hotels, function ($jenishotel) use ($inputhotel){
                    return in_array($jenishotel['hotelClass'], $inputhotel);
                });
                $firstDay = true; 
    
                foreach ($timeslots as $index => $slot) {
                    if (isset($places[$index]) && $places[$index] !== 'Tidak ada aktivitas wisata.') {
                        if ($slot === 'pagi' && $firstDay) {
                            $nearestOleh = null;
            $minDistance = PHP_FLOAT_MAX;

            foreach ($oleholeh as $olehan) {
                $distance = $this->haversineDistances($places[0]['latitude'], $places[0]['longitude'], $olehan['latitude'], $olehan['longitude']);
                
                if ($distance < $minDistance && !in_array($olehan['nama'], $visitedOleh)) {
                    $minDistance = $distance;
                    $nearestOleh = $olehan;
                }
            }

            if ($nearestOleh) {
                $dailyPlan["wisataoleholeh"] = $nearestOleh;
                $visitedOleh[$day][] = $nearestOleh;
            }
            $firstDay = false;
        
                        }
    
                        $dailyPlan[$slot] = $places[$index];
                        
    
                        $nearestRestaurant = null;
                        $nearestKuliner = null;
                        $nearestOleh = null;
                        $minDistance = PHP_FLOAT_MAX;
    
                        $filteredRestoran = array_filter($resto, function ($tempatmakan) use ($makanan) {
                            return in_array($tempatmakan['masakan'], $makanan);
                        });
    
                        foreach ($filteredRestoran as $restaurant) {
                            $distance = $this->haversineDistances($places[$index]['latitude'], $places[$index]['longitude'], $restaurant['latitude'], $restaurant['longitude']);
                            if ($distance < $minDistance && !in_array($restaurant['nama'], $visitedRestaurants)) {
                                $minDistance = $distance;
                                $nearestRestaurant = $restaurant;
                            }
                        }
    
                        if ($nearestRestaurant) {
                            $dailyPlan["Makan$slot"] = $nearestRestaurant;
                            $visitedRestaurants[$day][] = $nearestRestaurant;
                            $minDistance = PHP_FLOAT_MAX;
                        }
                    } 
                }
    
                if (!isset($dailyPlan['Hotel'])) {
                    $dailyPlan['Hotel'] = $hotelStatus;
                }
    
                $dailyPlan['Malam'] = 'Kembali ke hotel';
    
                $tripPlan[] = [
                    'Hari' => $day + 1,
                    'Tempatwisata' => $dailyPlan,
                ];
            }
        }
    
        $itineraryId = 'IT_' . uniqid(); 
        return response()->json([
            'trip_plan' => $tripPlan,
            'itinerary_id' => $itineraryId,
        ]);
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


    public function generateTrip(Request $request)
    {
        $jumlahHariWisata = 7;
        $userLatitude = deg2rad(-7.8267118) ;
        $userLongitude = deg2rad(110.2658991);
        $inputhotel = ['4.0','5.0'];
        $placez = \File::get(base_path('public/dbwisata/wisatakeluarga.json'));
        $places = json_decode($placez, true);
        $restoran = \File::get(base_path('public/dbrestoran/resto.json'));
        $resto = json_decode($restoran, true);
        $restoransiang = \File::get(base_path('public/dbrestoran/restokeluargasiang.json'));
        $restosiang = json_decode($restoransiang, true);
        $hotel = \File::get(base_path('public/dbhotel/hotel.json'));
        $hotels = json_decode($hotel,true);
        $oleh = \File::get(base_path('public/dbwisata/wisataoleholeh.json'));
        $oleholeh = json_decode($oleh, true);
        $locationsWithDistance = [];
    
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
                        }
                    }

                if ($slot === 'pagi') {
                $nearestRestaurant = null;
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
                }
                           
                }
             
                $nearestRestaurant = null;
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
    
}
