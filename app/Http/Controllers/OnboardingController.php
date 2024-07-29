<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDataTypes;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class OnboardingController extends Controller
{
    //

    public function updateUserOnboardingData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'onboarding_data' => 'required',
        ]);

        if ($validator->fails()) {
            return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
        }

        return CommonHelper::sendResponse('Onboarding data updated successfully', config('constants.STATUS_CODE.HTTP_OK'), '', '');
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

    public function getOnboadingData(Request $request)
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

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Onboarding Data saved Successfully', $user->count(), [
                'step' => $user->has_onboarded,
                'data' => [
                    "commercial" => json_decode($userDataTypes->commercial),
                    "company_types_1" => json_decode($userDataTypes->company_types_1),
                    "company_types_2" => json_decode($userDataTypes->company_types_2),
                    "company_types_3" => json_decode($userDataTypes->company_types_3),
                    "company_types_4" => json_decode($userDataTypes->company_types_4),
                    "general" => json_decode($userDataTypes->general),
                    "inferences" => json_decode($userDataTypes->inferences),
                    "record" => json_decode($userDataTypes->record),
                ]
            ]);
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }


    public function updateOnboardingData(Request $request)
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
            }

            $userDataTypes->commercial = json_encode($request->commercial);
            $userDataTypes->company_types_1 = json_encode($request->company_types_1);
            $userDataTypes->company_types_2 = json_encode($request->company_types_2);
            $userDataTypes->company_types_3 = json_encode($request->company_types_3);
            $userDataTypes->company_types_4 = json_encode($request->company_types_4);
            $userDataTypes->general = json_encode($request->general);
            $userDataTypes->inferences = json_encode($request->inferences);
            $userDataTypes->record = json_encode($request->record);
            $userDataTypes->save();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Onboarding Data Updated Successfully', $user->count(), [
                'success' => true,
            ]);
        } catch (\Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

}
