<?php

require 'vendor/autoload.php';

use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Datasets\Labeled;

// Load the dataset from the JSON file
$rawData = json_decode(file_get_contents('public/dbwisata/attraction_dataset.json'), true);
$dataset = Labeled::fromIterator(new \ArrayIterator($rawData));


// Split the dataset into samples (X) and labels (y)
$samples = $dataset->samples();
$labels = $dataset->labels();

// Initialize the OneHotEncoder transformer
$transformer = new OneHotEncoder();

// Fit and transform the transformer on your dataset
$transformer->fit($samples, $labels);
$transformer->transform($samples);

// Save the trained transformer to a file
$transformer->save(new Filesystem(base_path('/public/transformer.rbx')));

echo "Transformer trained and saved successfully!\n";