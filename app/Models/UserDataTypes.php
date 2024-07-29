<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDataTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'general',
        'record',
        'commercial',
        'inferences',
        'user_id',
        'company_types_1',
        'company_types_2',
        'company_types_3',
        'company_types_4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
