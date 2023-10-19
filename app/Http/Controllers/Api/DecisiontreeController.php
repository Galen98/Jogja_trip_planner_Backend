<?php

namespace App\Http\Controllers\Api;
use Rubix\ML\Persisters\Filesystem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rubix\ML\PersistentModel;
use Rubix\ML\Classifiers\RandomForest;
use Rubix\ML\Classifiers\ClassificationTree;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\CrossValidation\Reports\ConfusionMatrix;
use Rubix\ML\CrossValidation\Metrics\F1Score;
use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Rubix\ML\Classifiers\KNearestNeighbors;

class DecisiontreeController extends Controller
{
    public function recommendTouristSpot(Request $request)
    {
        $jsonFile = \File::get(base_path('/public/dbwisata/attraction_dataset.json'));
        $predictData = json_decode($jsonFile, true);
        $currentWeather = $request->input('current_weather');

        foreach ($predictData as $data) {
            $data['weather'] = $currentWeather;
        }


        $trainData = [
            ["weather" => "Heavy rain", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],


            ["weather" => "Heavy rain", "type" => "indoor", "akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "indoor", "akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "indoor", "akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "indoor", "akses"=>"susah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Thundery outbreaks possible", "type" => "outdoor","akses"=>"susah" ,"dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "outdoor","akses"=>"susah" ,"dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "outdoor","akses"=>"mudah" , "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "outdoor","akses"=>"mudah" ,"dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],


            ["weather" => "Thundery outbreaks possible", "type" => "indoor", "akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "indoor", "akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "indoor", "akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "indoor", "akses"=>"susah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Sunny", "type" => "indoor","akses"=>"mudah","dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "indoor","akses"=>"mudah","dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "indoor","akses"=>"susah","dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "indoor","akses"=>"susah","dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Sunny", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],


            ["weather" => "Partly cloudy", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Partly cloudy", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Clear", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Clear", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Mist", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Mist", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Tidak cocok untuk cuaca saat ini"],
        ];

        
        $samples = [];
        $labels = [];
        foreach ($trainData as $data) {
            $samples[] = [$data["weather"], $data["type"], $data["akses"], $data["dataran"]];
            $labels[] = $data["recommendation"];
        }
        $dataset = new Labeled($samples, $labels);
        $estimator = new RandomForest(new ClassificationTree(10), 300, 0.1, true);

        
        $estimator->train($dataset);

        
        $predictSamples = [];
        foreach ($predictData as $data) {
            $predictSamples[] = [$data["weather"], $data["type"], $data["akses"], $data["dataran"]];
        }

        $predictDataset = new Unlabeled($predictSamples);
        $predictions = $estimator->predict($predictDataset);

        $result = [];

        foreach ($predictions as $index => $prediction) {
            $result[] = [
                "nama" => $predictData[$index]["nama"],
                "type" => $predictData[$index]["type"],
                "akses" => $predictData[$index]["akses"],
                "dataran" => $predictData[$index]["dataran"],
                "recommendation" => $prediction,
            ];
        }
        

        return response()->json($result);
    }


    public function recommendTouristSpotbyweather(Request $request)
    {
        $latitude = deg2rad($request->latitude); // Convert to radians
        $longitude = deg2rad($request->longitude);
        $radius = $request->input('radius', 5);
        $locationsJson = \File::get(base_path('/public/dbwisata/attraction_dataset.json'));
        $locations = json_decode($locationsJson, true);

        $nearestLocations = [];
        $nearestDistance = [];

        foreach ($locations as $location) {
            $locationLat = deg2rad($location['latitude']); // Convert to radians
            $locationLon = deg2rad($location['longitude']); // Convert to radians
    
            $distance = $this->haversineDistance($latitude, $longitude, $locationLat, $locationLon);
    
            if ($distance <= $radius) {
                $location['distance'] = $distance; // Distance in kilometers
                $nearestLocations[] = $location;
            }
        }
    
        usort($nearestLocations, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        // $predict = array_slice($nearestLocations, 0);
        $predicts = json_encode($nearestLocations);
        $predictData = json_decode($predicts, true);

        $currentWeather = $request->input('current_weather');

        foreach ($predictData as $data) {
            $data['weather'] = $currentWeather;
        }


        $trainData = [
            ["weather" => "Heavy rain", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],


            ["weather" => "Heavy rain", "type" => "indoor", "akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "indoor", "akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "indoor", "akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Heavy rain", "type" => "indoor", "akses"=>"susah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Thundery outbreaks possible", "type" => "outdoor","akses"=>"susah" ,"dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "outdoor","akses"=>"susah" ,"dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "outdoor","akses"=>"mudah" , "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "outdoor","akses"=>"mudah" ,"dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],


            ["weather" => "Thundery outbreaks possible", "type" => "indoor", "akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "indoor", "akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "indoor", "akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Thundery outbreaks possible", "type" => "indoor", "akses"=>"susah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Sunny", "type" => "indoor","akses"=>"mudah","dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "indoor","akses"=>"mudah","dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "indoor","akses"=>"susah","dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "indoor","akses"=>"susah","dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Sunny", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Sunny", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],


            ["weather" => "Partly cloudy", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Partly cloudy", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Partly cloudy", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Cloudy", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Cloudy", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Cloudy", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Cloudy", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Cloudy", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Cloudy", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Cloudy", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Cloudy", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Clear", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Clear", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Clear", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Light drizzle", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light drizzle", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light drizzle", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light drizzle", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Light drizzle", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light drizzle", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light drizzle", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light drizzle", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Light rain", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light rain", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light rain", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Light rain", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Light rain", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Light rain", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Light rain", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Light rain", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Moderate rain at times", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Moderate rain at times", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Moderate rain at times", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Moderate rain at times", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Moderate rain at times", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Moderate rain at times", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Moderate rain at times", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Moderate rain at times", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Patchy rain possible", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy rain possible", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy rain possible", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy rain possible", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Patchy rain possible", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Patchy rain possible", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Patchy rain possible", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Patchy rain possible", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Patchy light drizzle", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy light drizzle", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy light drizzle", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy light drizzle", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Patchy light drizzle", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy light drizzle", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy light drizzle", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Patchy light drizzle", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Overcast", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Overcast", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Overcast", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Overcast", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Overcast", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Overcast", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Overcast", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Overcast", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Cocok untuk cuaca saat ini"],

            ["weather" => "Mist", "type" => "indoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "indoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "indoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "indoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Tidak cocok untuk cuaca saat ini"],

            ["weather" => "Mist", "type" => "outdoor","akses"=>"mudah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "outdoor","akses"=>"mudah", "dataran"=>"tinggi", "recommendation" => "Tidak cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "outdoor","akses"=>"susah", "dataran"=>"rendah", "recommendation" => "Cocok untuk cuaca saat ini"],
            ["weather" => "Mist", "type" => "outdoor","akses"=>"susah", "dataran"=>"tinggi","recommendation" => "Tidak cocok untuk cuaca saat ini"],
        ];

        
        $samples = [];
        $labels = [];
        foreach ($trainData as $data) {
            $samples[] = [$data["weather"], $data["type"], $data["akses"], $data["dataran"]];
            $labels[] = $data["recommendation"];
        }
        $dataset = new Labeled($samples, $labels);
        $estimator = new RandomForest(new ClassificationTree(10), 300, 0.1, true);

        
        $estimator->train($dataset);

        
        $predictSamples = [];
        foreach ($predictData as &$data) {
            $predictSamples[] = [$currentWeather, $data["type"], $data["akses"], $data["dataran"]];
            // echo "Predict Sample: " . json_encode($predictSamples) . "\n";
        }

        $predictDataset = new Unlabeled($predictSamples);
        $predictions = $estimator->predict($predictDataset);

        $result = [];

        foreach ($predictions as $index => $prediction) {
            $result[] = [
                "nama" => $predictData[$index]["nama"],
                "type" => $predictData[$index]["type"],
                "akses" => $predictData[$index]["akses"],
                "dataran" => $predictData[$index]["dataran"],
                "kategori" => $predictData[$index]["kategori"],
                "rating" => $predictData[$index]["rating"],
                "id" => $predictData[$index]["id"],
                "latitude" => $predictData[$index]["latitude"],
                "longitude" => $predictData[$index]["longitude"],
                "image" => $predictData[$index]["image"],
                "url_maps" => $predictData[$index]["url_maps"],
                "operating_hours" => $predictData[$index]["operating_hours"],
                "recommendation" => $prediction,

                "distance"=>$nearestLocations[$index]["distance"]
            ];
        }
        
        
        return response()->json($result);
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
}




