<?php

namespace App\Http\Controllers\Common;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Models\Extension\FormData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Extension\PiDataElement;
use App\Models\Extension\PurePiDataNameList;
use App\Models\Extension\Setting;
use Illuminate\Support\Facades\Validator;
use App\Models\Extension\Company;

class PIDataElementController extends Controller
{
    public function addElement(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $pi_data_element = PiDataElement::where('pi_date_element', $request->name)->first();
            if (!$pi_data_element) {
                $new_pi_data_element = PiDataElement::create([
                    "pi_date_element" => ucwords($request->name)
                ]);
            }

            $pi_data_elements = PiDataElement::get()->pluck('pi_date_element');
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Element Added sucessfully', '', $pi_data_elements);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function formTrack(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'host' => 'required',
                'url' => 'required',
                'company' => 'required',
                'formData' => 'required',
                'formData.*.key' => 'required',
                'formData.*.value' => 'required'
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $company = Company::where('company_name', $request->company)->first();

            foreach ($request->formData as $formData) {
                $PiDataElement = PiDataElement::where('pi_date_element', $formData['key'])->first();
                if ($PiDataElement) {
                    FormData::create([
                        "url" => $request->url,
                        "company" => $company->id,
                        "data_element" => $PiDataElement->id,
                        "user" => Auth::guard('api')->user()->id,
                        "value" => $formData['value'],
                        "occurrences" => 1,
                        "hash_value" => $formData['value'],
                    ]);
                }
            }


            $form_data = FormData::where('user', Auth::guard('api')->user()->id)->get();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Form Data Added sucessfully', '', $form_data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function getElement()
    {
        try {
            $data = PiDataElement::get();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Pi Data Get sucessfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function getSetting()
    {
        try {
            $data = Setting::where('user', Auth::guard('api')->user()->id)->get();
            if (empty($data)) {
                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Setting Data Get sucessfully', $data->count(), ["enable" => true, "period" => $data]);
            } else {
                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Setting Data Not Get sucessfully', $data->count(), ["enable" => true, "period" => 1]);
            }
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function updateSetting(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'enable' => 'required',
                'period' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $data = Setting::where('user', Auth::guard('api')->user()->id)->get();
            $data->enable = $request->enable;
            $data->period = $request->period;
            $data->save();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Data Updated sucessfully', '', '');
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function getFormData()
    {
        try {
            $data = FormData::where('user', Auth::guard('api')->user()->id)->get();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Form Data Get sucessfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function getPiElement()
    {
        try {
            $data = PiDataElement::get()->pluck('pi_date_element');

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'PI Data Get sucessfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function getPiName()
    {
        try {
            $data = collect(PurePiDataNameList::get())->map(function ($obj) {
                // return [
                //     "pure_pi_name" => $obj->pure_pi_name,
                //     "pi_element" => $obj->pi_element
                // ];
                return [
                    "pure_name" => $obj->pure_pi_name,
                    "element" => $obj->pi_element
                ];
            });

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Get Pi Name List sucessfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
}
