<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use App\Models\atrikelkategoris;
use App\Models\Kategori;
use App\Models\Tags;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; 

class WisataController extends Controller
{
    public function insertkategori(Request $request){
        $namakategori = $request->namakategori;
        $description = $request->description;
        $img = $request->image;

        $nama_file = time()."_".$img->getClientOriginalName();
		$tujuan_upload = 'public/img';
        $img->move($tujuan_upload,$nama_file);

        $data = [
            'namakategori'=>$namakategori,
            'shortdescription'=>$description,
            'image'=>$nama_file
        ];

        Kategori::create($data);
        Alert::success('Kategori Berhasil Ditambahkan');
        return redirect()->to('/wisata/kategori');
    }

    public function deletekategori($kategoriid){
        $images=Kategori::where('id',$kategoriid)->first();
        File::delete('public/img/'.$images->image);
        Kategori::where('id', $kategoriid)->delete();
        Alert::error('Berhasil Dihapus');
        return redirect()->back();
    }


    public function editkategoriview($kategoriid){
        $kategori = Kategori::where('id', $kategoriid)->get();
        return view('pages.kategoriedit', compact('kategori'));
    }

    public function updatekategori(Request $request, $kategoriid){
        $img=request('image');
        
        if($img == null){
            $nama_file=$request->namagambar;
        }
        else{
            $images = Kategori::where('id', $kategoriid)->first();
            File::delete('public/img/'.$images->image);
            $nama_file = time()."_".$img->getClientOriginalName();
            $tujuan_upload = 'public/img';
            $img->move($tujuan_upload,$nama_file);
        }
        DB::table('kategoris')->where('id', $kategoriid)
        ->update([
            'namakategori'=>$request->namakategori,
            'shortdescription'=>$request->short,
            'image'=>$nama_file
        ]);
        Alert::success('Berhasil','Berhasil Diupdate');
        return redirect()->to('/wisata/kategori/');
    }
}
