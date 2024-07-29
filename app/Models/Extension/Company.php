<?php

namespace App\Models\Extension;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = "extension_company";

    protected $fillable = ["company_name","company_logo","id"];

    public function url(){
      return  $this->hasOne(CompanyUrlName::class);
    }
}
