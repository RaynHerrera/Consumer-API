<?php

namespace App\Models\Extension;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyUrlName extends Model
{
    use HasFactory;

    protected $table = "extension_companyurlname";

    protected $fillable = ["company_url_name","company_id","id"];

    public function company(){
        return $this->hasOne(Company::class,'id','company_id');
    }
}
