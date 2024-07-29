<?php

namespace App\Models\Extension;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurePiDataNameList extends Model
{
    use HasFactory;

    protected $table = "extension_purepidatanamelist";

    protected $fillable = ["pure_pi_name","pi_element","id"];

    // public function fromData(){
    //     return
    // }
}
