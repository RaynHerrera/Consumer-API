<?php

namespace App\Http\Controllers\Authentication;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserDataTypes;
use App\Mail\EmailChange;
use App\Mail\ChangePassword;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Mail\ActiveAccountMail;
use App\Mail\TwoFactorSendMail;
use App\Models\Common\LoginLog;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Common\ChangeProfileDetail;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
                'username' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'country_id' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            if ($user = User::where('username', $request->username)->whereOr('email', $request->email)->first()) {
                return CommonHelper::notFoundMessage('User already Exist in Database', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            if (User::create($request->all())) {
                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User added sucessfully', '', '');
            } else {
                return CommonHelper::notFoundMessage("Something Wrong on User Create time", config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function checkUser(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'username' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            if ($user = User::where('username', $request->username)->first()) {
                return CommonHelper::notFoundMessage('User in DataBase', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Not in DataBase', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function checkEmail(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'email' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            if ($user = User::where('email', $request->email)->first()) {
                return CommonHelper::notFoundMessage('User in DataBase', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Email Not in DataBase', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function sendVerifyEmail(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'email' => 'required|exists:authentication_users,email',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            if ($user = User::where('email', $request->email)->first()) {
                Mail::to($user->email)->send(new ActiveAccountMail([
                    "user_full_name" => ucfirst($user->first_name) . ' ' . ucfirst($user->first_name),
                    "url" =>    env("FRONT_URL") . "/email-confirm/" . encrypt($user->id . '--' . $user->created_at->format('M') . '--' . $user->email_code)
                ]));


                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Email Send Successfully', '', '');
            } else {
                return CommonHelper::notFoundMessage('Error on Email Send Time', config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function checkActivation(Request $request)
    {
        try {

            //return    $latestUser =  User::orderBy('created_at','desc')->get()->first();
            // return encrypt('123456--jan--15986');
            $validator = validator::make($request->all(), [
                'token' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }
            // return decrypt($request->token);
            $data = decrypt($request->token);
            // return $data;
            if ($user = User::where('elroi_id', decrypt($request->token))->first()) {
                if ($user->is_verified == true) {
                    return CommonHelper::notFoundMessage("Email and Token is Already Verify", config('constants.STATUS_CODE.HTTP_OK'));
                }

                $user->email_code = null;
                $user->is_verified = true;
                $user->save();

                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Verified sucessfully', '', '');
            } else {
                return CommonHelper::notFoundMessage("Wrong Email and Token", config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function profileEmailVerify(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'token' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            //   $data= explode("--", decrypt($request->token));
            if (!$changeData = ChangeProfileDetail::where(['id' => decrypt($request->token)])->first()) {
                return CommonHelper::notFoundMessage('Profile Verify Link is expired', config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }

            $user = User::where('id', $changeData->user_id)->first();
            if ($changeData->user_name != null) {
                $user->username = $changeData->user_name;
            } else {
                $user->email = $changeData->email_id;
            }

            $user->save();
            ChangeProfileDetail::where(['id' => decrypt($request->token)])->delete();
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Details Updated Successfully', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function forGotPassword(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'forget_user' => 'required'
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            if (!$user = User::where('username', $request->forget_user)->orWhere('email', $request->forget_user)->first()) {
                return CommonHelper::notFoundMessage("Username Not Match in Database", config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }

            if ($user->is_verified == false) {
                return CommonHelper::notFoundMessage('User Email Not verify ', config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }

            do {
                $email_code =  rand(100000, 999999);
            } while (User::where('forgot_password_code', $email_code)->exists());


            Mail::to($user->email)->send(new ForgotPasswordMail([
                "user_full_name" => ucfirst($user->first_name) . ' ' . ucfirst($user->last_name),
                "forgot_code" => $email_code
            ]));
            $user->forgot_password_code = $email_code;
            $user->save();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Forgot Email Send Successfully', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function forGotPasswordVerify(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'forget_user' => 'required',
                'otp' => 'required',
                'password' => 'required',

            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            if (!$user = User::where('username', $request->forget_user)->orWhere('email', $request->forget_user)->first()) {
                return CommonHelper::notFoundMessage("Username Not Match in Database", config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }

            if ($user->forgot_password_code == $request->otp) {
                $user->password = Hash::make($request->password);
                $user->forgot_password_code = null;
                $user->save();

                $emailArray['user_full_name'] = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);
                $emailArray['login_url'] = env("FRONT_URL");
                Mail::to($user->email)->send(new ChangePassword($emailArray));

                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Password Change Successfully Successfully', '', '');
            } else {
                return CommonHelper::notFoundMessage("Wrong OTP", config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function Login(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'user_name' => 'required|exists:authentication_users,username',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $user = User::where('username', $request->user_name)->first();

            // if ($user->is_verified == false) {
            //     return CommonHelper::notFoundMessage('User Email Not verified yet.', config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            // }
            Log::debug($user);


            if (Hash::check($request->password, $user->password)) {

                if ($user->is_2fa_active == 1) {
                    // dd("on");
                    do {
                        $email_code =  rand(100000, 999999);
                    } while (User::where('email_code', $email_code)->exists());
                    $data = [
                        "user_full_name" => ucfirst($user->first_name) . ' ' . ucfirst($user->last_name),
                        "factor_code" => $email_code
                    ];

                    Mail::to($user->email)->send(new TwoFactorSendMail($data));
                    $user->email_code = $email_code;
                    $user->save();

                    return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_UNAUTHORIZED'), 'User Has active 2-factor for otp Check Email id', '', ['two_factor_code' => encrypt($user->elroi_id . '-twofactor-' . $email_code)]);

                    // return CommonHelper::notFoundMessage('User Has active 2-factor for otp Check Email id', config('constants.STATUS_CODE.HTTP_UNAUTHORIZED'));
                }
                // dd("off");
                // return $this->Logintoken($user);

                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Login sucessfully', '', $this->Logintoken($user));
            } else {
                return CommonHelper::notFoundMessage("Username Or Password Not Match in Database", config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Verified sucessfully', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function Logintoken($user)
    {
        $accessToken = $user->createToken('authToken');
        LoginLog::create([
            'user_id' => $user->id,
            'login_time' => Carbon::now(),
            'access_tokens_id' => $accessToken->token->id,
        ]);

        $user->last_login = Carbon::now();
        $user->save();

        return [
            "user_id" => $user->id,
            "elroi_id" => $user->elroi_id,
            "email" => $user->email,
            "username" => $user->username,
            "full_name" => $user->first_name . ' ' . $user->last_name,
            "first_name" => ucfirst($user->first_name),
            "is_2fa_active" => $user->is_2fa_active,
            "tokens" => $accessToken->accessToken,
            "has_onboarded" => $user->has_onboarded >= 11,
            "profile_image" => $user->logo ? 'assest/upload-logo/user-logo/' . $user->id . '/' . $user->logo : ''
        ];
    }

    public function sendCode(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'two_factor_code' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            do {
                $email_code =  rand(100000, 999999);
            } while (User::where('email_code', $email_code)->exists());

            $twoFactData = explode("-", decrypt($request->two_factor_code));
            //  return $twoFactData[0];

            // return Carbon::now()->addMinute(15);
            $user = User::where('elroi_id', $twoFactData[0])->first();
            if (!$user) {
                return CommonHelper::notFoundMessage('User Not Found In Database', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }
            // if($user->updated_at <= Carbon::now()->subMinute(15)){
            //     return CommonHelper::notFoundMessage('Two Factor Code Time Expired', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));    
            // }

            // $user = User::where('id', $request->user_id)->first();
            Mail::to($user->email)->send(new TwoFactorSendMail([
                "user_full_name" => ucfirst($user->first_name) . ' ' . ucfirst($user->last_name),
                "factor_code" => $email_code
            ]));
            $user->email_code = $email_code;
            $user->save();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), '2-factor authentication OTP sent sucessfully, please check your email', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function authverify(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'two_factor_code' => 'required',
                'code' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }
            $twoFactData = explode("-", decrypt($request->two_factor_code));
            // return  $twoFactData[2];
            // if ($twoFactData[2] != $request->code) {
            //     return CommonHelper::notFoundMessage('Wrong Two Factor Code', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            // }
            // return Carbon::now()->subMinute(15);
            $user = User::where('elroi_id', $twoFactData[0])->where('email_code', $request->code)->first();
            if (!$user) {
                return CommonHelper::notFoundMessage('Wrong Two Factor Code', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }
            if ($user->updated_at <= Carbon::now()->subSeconds(90)) {
                return CommonHelper::notFoundMessage('Two Factor Code Time Expired', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }
            // return Carbon::now()->addMinute(15);
            // return $user;
            if ($user) {
                $user->email_code = null;
                $user->save();
                // return $this->Logintoken($user);
                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Verified sucessfully', '', $this->Logintoken($user));
            } else {
                return CommonHelper::notFoundMessage("Wrong Token", config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }


    public function logout(Request $request)
    {
        try {
            $accessToken = LoginLog::where('access_tokens_id', $request->user()->token()->id)->first();

            $accessToken->logout_time = Carbon::now();
            $accessToken->save();



            $request->user()->token()->revoke();
            // auth()->user()->token()->revoke();
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function profile(Request $request)
    {
        try {
            $data = User::with('country:id,name')->where('id', Auth::guard('api')->user()->id)->first();
            $data['profile_image'] = $data->logo ? 'assest/upload-logo/user-logo/' . $data->id . '/' . $data->logo : '';

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Data Get sucessfully', $data->count(), $data);
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function setupOnboarding(Request $request)
    {
        try {
            $user = User::where('id', Auth::guard('api')->user()->id)->first();
            if (!$user) {
                return CommonHelper::notFoundMessage('User Not Found', config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }

            $userDataTypes = UserDataTypes::where('user_id', $user->id)->first();

            if (!$userDataTypes) {
                $userDataTypes = new UserDataTypes();
                $userDataTypes->user_id = $user->id;
                // Log::debug($userDataTypes);
            }

            $this->handleSavingUserDataTypeAndCompanyTypes($request, $user);
            $user->has_onboarded = $request->step;
            $user->save();
            // Log::debug($request->all());

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Onboarding Data saved Successfully', $user->count(), [
                'step' => $user->has_onboarded,
                'data' => $userDataTypes
            ]);
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    private function handleSavingUserDataTypeAndCompanyTypes($request, $user)
    {
        $userDataTypes = UserDataTypes::where('user_id', $user->id)->first();

        if (!$userDataTypes) {
            $userDataTypes = new UserDataTypes();
            $userDataTypes->user_id = $user->id;
        }

        switch ($request->column) {
            case 'general':
                $userDataTypes->general = json_encode($request->dataPoints);
                break;
            case 'record':
                $userDataTypes->record = json_encode($request->dataPoints);
                break;
            case 'commercial':
                $userDataTypes->commercial = json_encode($request->dataPoints);
                break;
            case 'inferences':
                $userDataTypes->inferences = json_encode($request->dataPoints);
                break;
            case 'set_1':
                $userDataTypes->company_types_1 = json_encode($request->dataPoints);
                break;
            case 'set_2':
                $userDataTypes->company_types_2 = json_encode($request->dataPoints);
                break;
            case 'set_3':
                $userDataTypes->company_types_3 = json_encode($request->dataPoints);
                break;
            case 'set_4':
                $userDataTypes->company_types_4 = json_encode($request->dataPoints);
                break;
            default:
                break;
        }
        $userDataTypes->save();
    }


    public function updateProfile(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'id' => 'required|exists:authentication_users,id',
                'elroi_id' => 'required',
                'email' => 'required',
                // 'username' => 'required',
                // 'logo' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                // 'is_2fa_active' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $user = User::where('id', $request->id)->first();

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->country_id = $request->country_id;

            // if ($request->username) {

            //     if ($username = User::where('username', $request->username)->exists()) {
            //         return CommonHelper::notFoundMessage('User Name Already Exists in Database', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            //     }

            //     if($ChangeProfileDetail = ChangeProfileDetail::where('user_name',$request->username)->exists()){
            //         return CommonHelper::notFoundMessage('User Name Change Request Send Successfully but Not verifier ', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            //     }

            //     $this->ChangeProfileDetails($user->id, '', $request->username);

            // }

            if ($request->email != $user->email) {

                if ($username = User::where('email', $request->email)->exists()) {
                    return CommonHelper::notFoundMessage('Email Id Already Exists in Database', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
                }
                ChangeProfileDetail::where('email_id', $request->email)->delete();
                // if($ChangeProfileDetail = ChangeProfileDetail::where('email_id',$request->email)->exists()){
                //     return CommonHelper::notFoundMessage('Email Id Change Request Send Successfully but Not verifier ', config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
                // }

                return $this->ChangeProfileDetails($user->id, $request->email, '');
            }

            // if ($user->is_verified == 1) {
            //     do {
            //         $email_code =  rand(100000, 999999);
            //     } while (User::where('email_code', $email_code)->exists());

            //     Mail::to($request->email)->send(new ActiveAccountMail([
            //         "user_full_name" => ucfirst($request->first_name).' '.ucfirst($request->last_name),
            //         "url" =>    env("FRONT_URL")."/email-confirm/".encrypt($request->id.'--'.$user->created_at->format('M').'--'.$email_code)
            //         ]));
            //     $user->email_code = $email_code;
            // }

            if ($request->file('logo')) {
                $files = $request->file('logo');
                $name = $files->getClientOriginalName();
                $path = 'assest/upload-logo/user-logo/' . $user->id;

                if (env('AWS_IMAGE_UPLOAD') == 1) {
                    Storage::disk('s3')->put($path, file_get_contents($files));
                } else {
                    if (!is_dir($path)) {
                        mkdir($path, 0755, true);
                    }

                    $files->move($path, $name);
                }

                $user->logo =  $name;
            }


            $user->save();


            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Data Updated sucessfully', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function updateTwoFactor(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'id' => 'required|exists:authentication_users,id',
                'is_2fa_active' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $user = User::where('id', $request->id)->first();
            $user->is_2fa_active = $request->is_2fa_active;
            $user->save();


            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User 2-factor Data Updated sucessfully', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function ChangeProfileDetails($userID, $email = null, $userName = null)
    {
        do {
            $change_code =  rand(100000, 999999);
        } while (ChangeProfileDetail::where('change_code', $change_code)->exists());

        ChangeProfileDetail::create([
            'user_id' => $userID,
            'email_id' => $email,
            // 'user_name'=>$userName,
            'change_code' => $change_code
        ]);
        $user = User::where('id', $userID)->first();
        $latestChangeProfileDetail =  ChangeProfileDetail::latest()->first();

        $emailArray['user_full_name'] = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);

        $emailArray['request_changes'] = $email != null ? "Email" : "User Name";


        if ($email != null) {

            $emailArray['url'] = env("FRONT_URL") . "/email-update/" . encrypt($latestChangeProfileDetail->id);
            $emailArray['subject'] = "Verify Email";
            $emailArray['changes_text'] = "You have changed email address in your profile. It will be updated after verification.";
            $emailArray['changes_text_one'] = "Please click on button below to verify your email.";
            $emailArray['btn_name'] = "Verify Email";

            Mail::to($email)->send(new EmailChange($emailArray));

            $emailArray['btn_name'] = null;

            $emailArray['changes_text'] = $email != null ? "You have requested to change email address in your profile. Your email was changed from " . $user->email . " to " . $email . "."  : null;
            $emailArray['changes_text_one'] = $email != null ? " We have sent a verification email to your new email address. You will be able to login with the new email address after verification." : null;
            $emailArray['subject'] = "Email Changed in Profile";

            // $emailArray['url'] = env("FRONT_URL");

            Mail::to($user->email)->send(new EmailChange($emailArray));
        }
        // if($userName != null){   

        //     $emailArray['changes_text'] = null;

        //     $emailArray['url'] = env("FRONT_URL")."/email-verify/".encrypt($latestChangeProfileDetail->id.'--'.$change_code);

        //     Mail::to($user->email)->send(new ProfileChangesMail($emailArray));
        // }


        return true;
    }
    public function SendVerifyEmails()
    {

        // Mail::to($request->email)->send(new ActiveAccountMail([
        //     "user_full_name" => ucfirst($request->first_name).' '.ucfirst($request->last_name),
        //     "url" =>    env("FRONT_URL")."/email-confirm/".encrypt($request->id.'--'.$user->created_at->format('M').'--'.$email_code)
        //     ]));
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'id' => 'required',
                'old_password' => 'required',
                'new_password' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $user = User::where('id', $request->id)->first();
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();

                $emailArray['user_full_name'] = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);
                $emailArray['login_url'] = env("FRONT_URL");
                Mail::to($user->email)->send(new ChangePassword($emailArray));
            } else {
                return CommonHelper::notFoundMessage('Your Old Password Does not match in Database', config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User Data Updated sucessfully', '', '');
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
}
