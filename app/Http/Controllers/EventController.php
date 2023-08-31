<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Resources\PostResource;

class EventController extends Controller
{
    public function postevent(Request $request){
        $namaevent=$request->namaevent;
        $lokasi=$request->lokasi;
        $waktu=$request->waktu;
        $maps=$request->maps;
        $htm=$request->htm;
        $kategori=$request->kategori;
        $description=$request->description;
        $img=$request->image;

        $nama_file = time()."_".$img->getClientOriginalName();
		$tujuan_upload = 'public/img';
        $img->move($tujuan_upload,$nama_file);

        $data=[
            'namaevent' => $namaevent,
            'lokasi' => $lokasi,
            'waktu' => $waktu,
            'kategori' => $kategori,
            'deskripsi' => $description,
            'htm' => $htm,
            'maps' => $maps,
            'image' => $nama_file
        ];

        Event::create($data);
        Alert::success('Event Berhasil Ditambahkan');
        return redirect()->to('/event');
    }

    public function getevent(){
        $event=Event::get();
    
        return view('pages.event', compact('event'));
    }

    // public function editeventview(Request $request, $eventid){
    //   $event=Event::where;  
    // }

}


