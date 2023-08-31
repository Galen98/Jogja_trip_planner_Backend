<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Nlpjs\NlpManager;
use App\Http\Controllers\Controller;

class NlpController extends Controller
{
    public function nlprequest(Request $request){
         // Get the input from the request
         $input = $request->input('input');

         // Initialize the NlpManager
         $nlpManager = new NlpManager();
 
         // Add the language (English in this case)
         $nlpManager->addLanguage('en');
 
         // Train the NlpManager with your custom dataset or a larger pre-existing dataset
         $nlpManager->processDataset([
             ['input' => 'wisata jogja alam', 'output' => 'alam'],
             // Add more training examples here
         ]);
 
         // Process the input text
         $response = $nlpManager->process('en', $input);
 
         // Get the detected keywords or entities (in this case, we extract the output property)
         $result = $response['entities'][0]['option'];
 
         // Return the result as a JSON response
         return response()->json(['result' => $result]);
    }
}
