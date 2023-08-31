<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "tags";

    protected $fillable = [
        'artikels_id','artikelkategoris_id'
    ];
}
