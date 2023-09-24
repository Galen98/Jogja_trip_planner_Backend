<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class UserControllers extends Controller
{

    public function index()
    {
        //get posts
        $dashboard = User::latest()->paginate(5);

        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $dashboard);
    }
    public function show(User $dashboard)
    {
            return new PostResource(true, 'Data Post Ditemukan!', $dashboard);
        
    }
    
    

    public function update(Request $request, User $dashboard){
        // $validator = Validator::make($request->all(), [
        //     'job'     => 'required',
        //     'motivation'   => 'required',
        //     'hometown' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }
        if ($request->hasFile('image')) {
            //upload image
            $image = $request->file('image');
            $image->storeAs('public/img', $image->hashName());

            //delete old image
            Storage::delete('public/img/'.$dashboard->image);

            //update post with new image
            $dashboard->update([
                'image'     => $image->hashName(),
                'name' => $request->name,
                'job'     => $request->job,
                'motivation'   => $request->motivation,
                'hometown' => $request->hometown,
                'tipe' => $request->tipe,
                'usia' => $request->usia
            ]);

        }
        else {

            //update post without image
            $dashboard->update([
                'name' => $request->name,
                'job'     => $request->job,
                'motivation'   => $request->motivation,
                'hometown' => $request->hometown,
                'tipe' => $request->tipe,
                'usia' => $request->usia
            ]);
        }
        return new PostResource(true, 'Data Post Berhasil Diubah!', $dashboard);
    }
}
