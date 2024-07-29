<?php

namespace App\Models\Extension;

use App\Models\Extension\FormData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PiDataElement extends Model
{
    use HasFactory;

    protected $table = "extension_pidataelement";

    protected $fillable = ['id','pi_date_element'];

    public function formData(){
        return $this->hasOne(FormData::class,'data_element','id');
    }
}
