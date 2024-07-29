<?php

namespace App\Models\Extension;

use Illuminate\Database\Eloquent\Model;
use App\Models\Extension\CompanyUrlName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyTrack extends Model
{
    use HasFactory;

    protected $table = "extension_companytracked";

    protected $fillable = ["company_id", "user", 'created_by', 'created_at', 'updated_by', 'updated_at'];

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
    public function url()
    {
        return $this->hasOne(CompanyUrlName::class, 'company_id', 'company_id');
    }
    public function privacy_statement()
    {
        return $this->hasOne(PrivacyStatement::class, 'company', 'company_id');
    }
}
