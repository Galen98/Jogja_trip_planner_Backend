<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $table = "historys";
    protected $fillable = [
        'users_id',
        'caption',
        'link',
        'image',
        'judul',
        'share',
        'nama_user',
        'img_user',
        'tipe'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
