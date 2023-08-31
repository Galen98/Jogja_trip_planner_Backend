<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'wisata_id',
        'nama',
        'latitude',
        'longitude',
        'rating',
        'kategori',
        'image',
        'url_maps',
        'jenis_wisata',
        'deskripsi'
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}