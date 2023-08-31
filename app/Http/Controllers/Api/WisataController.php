<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Kategori;
use Illuminate\Support\Carbon;
use App\Models\UserLike;
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
            $distance = $this->haversineDistance($latitude, $longitude, $location['latitude'], $location['longitude']);

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
    $jumlahHariWisata = $request->input('jumlah_hari');
    $userLatitude = $request->input('user_latitude');
    $userLongitude = $request->input('user_longitude');
    $user = JWTAuth::user();
    $places = UserLike::where('users_id', $user->id)->get()->toArray();
    
    // Bangun representasi graf dengan jarak sebagai bobot
    $graph = [];
    foreach ($places as $place) {
        $place['distance'] = $this->haversineDistances($userLatitude, $userLongitude, $place['latitude'], $place['longitude']);
        var_dump($place['distance']);
        $graph[$place['id']] = [
            'distance' => $place['distance'],
            'neighbors' => [],
        ];
    }
    
    // Mengisi informasi tetangga untuk setiap tempat wisata
    foreach ($graph as $placeId => &$placeInfo) {
        foreach ($graph as $neighborId => $neighborInfo) {
            if ($placeId !== $neighborId) {
                $distance = $neighborInfo['distance'];
                $placeInfo['neighbors'][$neighborId] = $distance;
            }
        }
    }
    
    
    
    // Implementasi algoritma Dijkstra
    $distances = [];
    $previous = [];
    $unvisited = [];
    
    foreach ($graph as $placeId => $placeInfo) {
        $distances[$placeId] = INF;
        $previous[$placeId] = null;
        $unvisited[$placeId] = $placeInfo['distance'];
    }
    
    $currentPlaceId = null;
    
    while (!empty($unvisited)) {
        $minDistance = min($unvisited);
        $currentPlaceId = array_search($minDistance, $unvisited);
        
        foreach ($graph[$currentPlaceId]['neighbors'] as $neighborId => $distance) {
            $totalDistance = $distances[$currentPlaceId] + $distance;
            if ($totalDistance < $distances[$neighborId]) {
                $distances[$neighborId] = $totalDistance;
                $previous[$neighborId] = $currentPlaceId;
            }
        }
        
        unset($unvisited[$currentPlaceId]);
    }
    
    
    $visited = array_keys($previous); 
    $currentPlaceId = array_search(min($distances), $distances);
    
    while ($currentPlaceId !== null) {
        $visited[] = $currentPlaceId;
        $currentPlaceId = $previous[$currentPlaceId];
    }
    
   
    $orderedPlaces = [];
foreach ($visited as $placeId) {
    foreach ($places as $place) {
        if ($place['id'] === $placeId) {
            $orderedPlaces[] = $place;
            break;
        }
    }
}


    
    $tripPlan = [];
$placesPerDay = array_chunk($orderedPlaces, 3);

for ($day = 0; $day < $jumlahHariWisata; $day++) {
    if (isset($placesPerDay[$day])) {
        $tripPlan[] = [
            'Hari' => $day + 1,
            'Tempat wisata' => $placesPerDay[$day],
        ];
    } else {
        break; 
    }
}

return response()->json([
    'trip_plan' => $place,
]);
}

// public function generateTrip(Request $request)
//     {
//         $jumlahHariWisata = $request->input('jumlah_hari');
//         $userLatitude = $request->input('user_latitude');
//         $userLongitude = $request->input('user_longitude');
//         $user = JWTAuth::user();
//         $places = UserLike::where('users_id', $user->id)->get()->toArray();
    
//         foreach ($places as &$place) {
//             $place['distance'] = $this->haversineDistance($userLatitude, $userLongitude, $place['latitude'], $place['longitude']);
//         }
    
//         usort($places, function ($a, $b) {
//             return $a['distance'] <=> $b['distance'];
//         });
    
//         $visited = [];
//         $tripPlan = [];
//         $userLocation = $places[0];
    
//         for ($day = 1; $day <= $jumlahHariWisata; $day++) {
//             $currentPlace = $userLocation;
//             $placesForDay = [];
//             $slots = ['Pagi', 'Siang', 'Malam'];
    
//             foreach ($slots as $slot) {
//                 $nextPlace = null;
    
//                 foreach ($places as $place) {
//                     if (!in_array($place, $visited) && !in_array($place, $placesForDay)) {
//                         $nextPlace = $place;
//                         break;
//                     }
//                 }
    
//                 if ($nextPlace) {
//                     $placesForDay[$slot] = $nextPlace;
//                     $visited[] = $nextPlace;
//                 }
//             }
    
//             $tripPlan[] = [
//                 'Hari' => $day,
//                 'Tempat wisata' => $placesForDay,
//             ];
//         }
    
//         return response()->json([
//             'trip_plan' => $tripPlan,
//         ]);
//     }

    
    




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
            $distance = $this->haversineDistance($latitude, $longitude, $location['latitude'], $location['longitude']);

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

    public function wisatabykategori($kategori){
        $wisataJson = \File::get(base_path('public/dbwisata/wisatadataset.json')); 
        $wisata = json_decode($wisataJson, true);

        $filteredWisata = array_filter($wisata, function ($item) use ($kategori) {
            return (
                (empty($kategori) || $item['kategori'] === $kategori)
            );
        });

        return response()->json($filteredWisata);
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
    $latitude = deg2rad($request->latitude); // Convert to radians
    $longitude = deg2rad($request->longitude); // Convert to radians
    $radius = $request->input('radius', 5); // Radius in kilometers

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

public function mapslocation(Request $request){
    $locationsJson = \File::get(base_path('public/dbwisata/wisatadataset.json'));
    $locations = json_decode($locationsJson, true);

    return response()->json($locations);
}


    public function cuaca(Request $request)
    {
        $apiKey = 'b7a0fc591e66f553fe00e419f33cbc30'; 
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
    // Check for authentication first
    $user = JWTAuth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    // Load JSON data
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
            'deskripsi' => $indexedAttractionsData[$attractionId]['deskripsi']
        ]);
       
    } else {
        // Handle invalid attraction ID
        return response()->json(['error' => 'Invalid attraction ID'], 400);
    }
}

public function removeLove(Request $request, $attractionId)
{
    $user = JWTAuth::user();
    
    // Find the user like record and delete it
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

    // Load attractions from JSON file
    $jsonData = \File::get(base_path('public/dbwisata/wisatadataset.json'));
    $attractions = json_decode($jsonData, true);

    // Get user's liked attractions and their types
    $userLikedAttractions = UserLike::where('users_id', $userId)->pluck('jenis_wisata');

    $recommendedAttractions = [];
    foreach ($userLikedAttractions as $jenisWisata) {
        $similarAttractions = array_filter($attractions, function ($attraction) use ($jenisWisata) {
            return $attraction['jeniswisata'] === $jenisWisata;
        });
    
        // Exclude already liked attractions
        $userLikedAttractionIds = UserLike::where('users_id', $userId)->pluck('wisata_id')->all();
        $recommendedAttractions[$jenisWisata] = array_filter($similarAttractions, function ($attraction) use ($userLikedAttractionIds) {
            return !in_array($attraction['id'], $userLikedAttractionIds);
        });
    
        // Sort attractions by rating in descending order
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

}