<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use App\Models\atrikelkategoris;
use App\Models\Artikels;
use App\Models\Tags;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; 

class ArtikelController extends Controller
{
    // public function addkategoriartikel(Request $request){
    //     $namakategori=$request->namakategori;
    //     $data=[
    //         'namakategori'=>$namakategori
    //     ];
    //     atrikelkategoris::create($data);
    //     Alert::success('Sukses');
    //     return redirect('/arikel/artikelkategori');
    // }

    // public function viewkategoriartikel(){
    //     $kategori=atrikelkategoris::paginate(10);

    //     return view('pages.artikelkategori', compact('kategori'));
    // }

    // public function showkategori($KategoriID){
    //     // $Kategori=DB::table('artikelkategoris')->where('id',$KategoriID)->first();
    //     $Kategori=atrikelkategoris::where('id',$KategoriID)->first();
    //     return response()->json([
    //     'status'=>200,
    //     'Kategori'=>$Kategori
    //     ]);
    // }

    // public function updatekategori(Request $request, $idkategori){
    //     $idkategori = Request('idkategori');
    //     $Kategori = atrikelkategoris::where('id', $idkategori)
    //    ->update([
    //     'namakategori' => $request->namakategori,
    //    ]); 
    // }

    // public function deletekategori(Request $request, $idkategoris){
    //     $idkategoris=Request('idkategoris');
    //     DB::table('artikelkategoris')->where('id', $idkategoris)->delete();
    //     DB::table('tags')->where('artikelkategoris_id', $idkategoris)->delete();
    // }

    //add artikel
    public function insertartikels(Request $request){
        $judul=$request->judul;
        $author=$request->author;
        $short=$request->short;
        $description=$request->description;
        $img=request('image');
        $backpacker=$request->tipebackpacker;
        $keluarga=$request->tipekeluarga;
        $grup=$request->tipegrup;
        $kategori=$request->kategori;

        $nama_file = time()."_".$img->getClientOriginalName();
		$tujuan_upload = 'public/img';
        $img->move($tujuan_upload,$nama_file);

        $data=[
            'judul'=>$judul,
            'shortdescription'=>$short,
            'description'=>$description,
            'image'=>$nama_file,
            'author'=>$author,
            'kategori'=>$kategori,
        ];

        $artikel=Artikels::create($data);
        
        Alert::success('Artikel Berhasil Ditambahkan');
        return redirect()->to('/artikel');

    }

    public function editviewartikel($artikelid){
        $artikel=Artikels::where('id', $artikelid)->get();
        return view('pages.artikeledit', compact('artikel'));
    }

    public function deleteartikel($artikelid){
        $images=Artikels::where('id',$artikelid)->first();
        File::delete('public/img/'.$images->image);
        Artikels::where('id', $artikelid)->delete();
        Alert::error('Artikel Telah Dihapus');
        return redirect()->to('/artikel');

    }

    public function updateartikel(Request $request, $artikelid){
        $img=request('image');
        
        if($img == null){
            $nama_file=$request->namagambar;
        }
        else{
            $images = Artikels::where('id', $artikelid)->first();
            File::delete('public/img/'.$images->image);
            $nama_file = time()."_".$img->getClientOriginalName();
            $tujuan_upload = 'public/img';
            $img->move($tujuan_upload,$nama_file);
        }
        DB::table('artikels')->where('id', $artikelid)
        ->update([
            'author'=>$request->author,
            'judul'=>$request->judul,
            'kategori'=>$request->kategori,
            'shortdescription'=>$request->short,
            'description'=>$request->description,
            'image'=>$nama_file
        ]);
        Alert::success('Berhasil','Berhasil Diupdate');
        return redirect()->to('/artikel');
    }
    
}
