<?php

namespace App\Traits;

use App\Model\Connector\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait Uuids
{
    protected static function boot()
    {
       
        parent::boot();
         
        static::creating(function ($model) {
            
         
            
        });
    }
}
