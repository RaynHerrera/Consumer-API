<?php

namespace App\Models;

use App\Models\Common\Country;
use App\Mail\ActiveAccountMail;
use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = "authentication_users";

    protected $fillable = [
        'password', 'last_login', 'is_superuser', 'country_id', 'username', 'first_name', 'last_name', 'is_staff', 'date_joined', 'email', 'email_code', 'role_id', 'elroi_id', 'is_2fa_active', 'is_active', 'is_verified', 'logo', 'forgot_password_code', 'has_onboarded'
    ];

    // protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'is_verified'];
    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
    public static function create(array $attributes = [])
    {
        $elroi_id = User::randNumber('elroi_id');

        do {
            $email_code =  rand(100000, 999999);
        } while (User::where('email_code', $email_code)->exists());


        $user = new User();
        $user->password = Hash::make($attributes['password']);
        $user->is_superuser = false;
        $user->username = $attributes['username'];
        $user->first_name = $attributes['first_name'];
        $user->last_name = $attributes['last_name'];
        $user->is_staff = false;
        $user->email = $attributes['email'];
        $user->email_code = $email_code;
        $user->country_id = $attributes['country_id'];
        //   $user->country_id = 1;
        $user->role_id = isset($attributes['role_id']) ? $attributes['role_id'] : 1;
        $user->elroi_id = $elroi_id;
        $user->logo = isset($attributes['logo']) ? $attributes['logo'] : null;
        $user->created_at = Carbon::now()->timestamp;


        if ($userId = $user->save()) {
            $latestUser =  User::orderBy('created_at', 'desc')->get()->first();

            Mail::to($attributes['email'])->send(new ActiveAccountMail([
                "user_full_name" => ucfirst($attributes['first_name']) . ' ' . ucfirst($attributes['last_name']),
                //"url" =>    env("FRONT_URL")."/email-confirm/".encrypt($latestUser->id.'--jan--'.$latestUser->email_code)
                "url" =>    env("FRONT_URL") . "/email-confirm/" . encrypt($latestUser->elroi_id)

            ]));


            return true;
        } else {
            return false;
        }
    }

    public static function randNumber($feild)
    {

        $chrList = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chrRepeatMin = 1;
        $chrRepeatMax = 5;
        $chrRandomLength = 10;

        do {
            $elroiId =  substr(str_shuffle(str_repeat($chrList, mt_rand($chrRepeatMin, $chrRepeatMax))), 1, $chrRandomLength);
        } while (User::where($feild, $elroiId)->exists());

        return $elroiId;
    }
}
