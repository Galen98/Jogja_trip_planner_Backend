<?php

namespace App\Http\Controllers\Api;
use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artikels;

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
    
}
