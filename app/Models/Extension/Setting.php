<?php

namespace App\Models\Extension;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = "extension_extensionsetting";

    protected $fillable = ["enable","period","user","id"];

    
}
