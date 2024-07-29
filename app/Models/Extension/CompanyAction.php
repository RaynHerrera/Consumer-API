<?php

namespace App\Models\Extension;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAction extends Model
{
    use HasFactory;

    protected $table = "company_actions";

    protected $fillable = ["user_id","company_id","action_type","action_status"];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public static function action_type($id)
    {
        $type = [
        "1"=> "Deletion request",
        "2"=> "Return of Data",
        "3"=> "Opt out of 3rd party"
      ];
        return isset($type[$id]) ? $type[$id] : null;
    }
    
    public static function action_status($id)
    {
        $type = [
        "1"=> "Submitted",
        "2"=> "Completed"
      ];
        return isset($type[$id]) ? $type[$id] : null;
    }
}
