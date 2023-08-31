<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Resources\PostResource;

class EventController extends Controller
{
    public function index(){
        $event = Event::get();
        return new PostResource(true, 'List Data Event', $event);
    }

    public function show($event){
        $id=Event::where('id', $event)->get();
        return new PostResource(true, 'Data Post Ditemukan!', $id);
    }
}
