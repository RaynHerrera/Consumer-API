<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = "countries";

    protected $fillable = [
      'name','code','phone','symbol','created_by','updated_by'
    ];

}
