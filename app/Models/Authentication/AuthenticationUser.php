<?php

namespace App\Models\Authentication;

use Carbon\Carbon;
use App\Mail\ActiveAccountMail;
use App\Models\Common\EmailLog;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuthenticationUser extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = "authentication_users";

    protected $fillable = [
        'password', 'last_login', 'is_superuser', 'username', 'first_name', 'last_name', 'is_staff', 'date_joined', 'email', 'email_code', 'role_id', 'elroi_id', 'is_2fa_active', 'is_verified', 'logo'
    ];

    // protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at', 'is_verified'];

    public static function create(array $attributes = [])
    {
        $elroi_id = AuthenticationUser::randNumber('elroi_id');

        do {
            $email_code =  rand(100000, 999999);
        } while (AuthenticationUser::where('email_code', $email_code)->exists());


        $user = new AuthenticationUser();
        $user->password = Hash::make($attributes['password']);
        $user->is_superuser = false;
        $user->username = $attributes['username'];
        $user->first_name = $attributes['first_name'];
        $user->last_name = $attributes['last_name'];
        $user->is_staff = false;
        $user->email = $attributes['email'];
        $user->email_code = $email_code;
        $user->role_id = isset($attributes['role_id']) ? $attributes['role_id'] : 1;
        $user->elroi_id = $elroi_id;
        $user->logo = isset($attributes['logo']) ? $attributes['logo'] : null;

        if ($userId = $user->save()) {
            $latestUser =  AuthenticationUser::latest()->first();

            Mail::to($attributes['email'])->send(new ActiveAccountMail([
                "user_full_name" => ucfirst($attributes['first_name']) . ' ' . ucfirst($attributes['last_name']),
                "url" =>    env("FRONT_URL") . "/email-confirm/" . encrypt($latestUser->id . '--' . $latestUser->created_at->format('M') . '--' . $latestUser->email_code)
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
        } while (AuthenticationUser::where($feild, $elroiId)->exists());

        return $elroiId;
    }
}
