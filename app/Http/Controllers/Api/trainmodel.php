<?php


require 'vendor/autoload.php';

use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Extractors\Json;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Transformers\OneHotEncoder;

$trainingData = json_decode(file_get_contents('public/dbwisata/decisiontreeexample.json'), true);
// Load the dataset from file

// Create an empty array to hold the samples and labels
$samples = [];
$labels = [];

// Separate features and labels
foreach ($trainingData as $sample) {
    $samples[] = [
        $sample['weather'] === 'Heavy rain' ? 1 : 0,
        $sample['weather'] === 'sunny' ? 1 : 0,
        $sample['type'] === 'indoor' ? 1 : 0,
        $sample['type'] === 'outdoor' ? 1 : 0,
    ];
    $labels[] = $sample['recommendation'];
}

// Create a Labeled dataset with the features and labels
$dataset = Labeled::quick($samples, $labels);

$estimator = new KNearestNeighbors(1);

// Fit the model
$estimator->train($dataset);

// Create an instance of the Filesystem persister with the file path to save the model
$persister = new Filesystem('/Users/macbook/Public/Project/Project itinerary/backend/public/knn.rbx');

// Encode the trained model using the Rubix\ML\Encoding class
$encoding = new \Rubix\ML\Encoding($estimator);

// Save the encoded model
$persister->save($encoding);

echo "Model trained and saved successfully." . PHP_EOL;