<?php

namespace App\Models\Extension;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormData extends Model
{
    use HasFactory;

    protected $table = "extension_formdata";

    // public $timestamps = false;
    
    protected $fillable = [
       'url','company','data_element','user','value','occurrences','hash_value','created_by','created_at','updated_by','updated_at'
    ];

    public function companyDetails(){
       return $this->hasOne(Company::class,'id','company');
    }
    
    public function user()
    {
        return $this->hasMany(User::class, 'user');
    }

    public function pidataName()
    {
        return $this->hasOne(PiDataElement::class, 'id','data_element');
    }
}
