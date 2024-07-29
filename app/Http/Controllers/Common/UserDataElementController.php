<?php

namespace App\Http\Controllers\Common;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Models\Extension\FormData;
use App\Http\Controllers\Controller;
use App\Models\Extension\CompanyUrlName;
use App\Models\Extension\PiDataElement;
use Illuminate\Support\Facades\Validator;

class UserDataElementController extends Controller
{
    public function userDataElement(Request $request)
    {
        try {

            $validator = validator::make($request->all(), [
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }
            $formdata =  FormData::with('pidataName:id,pi_date_element')->where('user', $request->user_id);

            if ($request->fromDate && $request->toDate) {
                $formdata->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
                // $formdata->whereBetween('created_at',[$request->fromDate,$request->toDate]);
            }

            $datas = $formdata->get();
            $datass = collect($datas)->map(function ($obj) use ($request) {
                // return $obj;
                $formdatahistroies = FormData::select('id', 'url', 'created_at')->where(['data_element' => $obj->data_element, 'user' => $request->user_id])->get();
                $company_collected = FormData::select('company')->where(['data_element' => $obj->data_element, 'user' => $request->user_id])->groupBy('company')->get();
                return [
                    "data_element_id" => $obj->data_element,
                    "data_element_created_at" => $obj->created_at,
                    "data_element_name" => $obj->pidataName ? $obj->pidataName->pi_date_element : null,
                    "data_element_time_collected" => FormData::select('id')->where(['data_element' => $obj->data_element, 'user' => $request->user_id])->get()->count(),
                    "data_element_company_collected" => count($company_collected),
                    "first_collected_date" => CommonHelper::changeTimezone(FormData::select('created_at')->where(['data_element' => $obj->data_element, 'user' => $request->user_id])->get()->first()->created_at),
                    "last_collected_date" => CommonHelper::changeTimezone(FormData::select('created_at')->where(['data_element' => $obj->data_element, 'user' => $request->user_id])->latest()->first()->created_at),
                    "data_element_company_collected_histroy" => collect($formdatahistroies)->map(function ($obj) {
                        return [
                            "id" => $obj->id,
                            "url" => $obj->url,
                            "created_at" => CommonHelper::changeTimezone($obj->created_at),
                        ];
                    }),

                ];
            });


            $lastData = $datass->groupBy('data_element_id');
            $lastDatas = [];
            foreach ($lastData as $array) {
                array_push($lastDatas, $array[0]);
            }
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'FromData Get Successfully', '', $lastDatas);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function userDataElementForCompany(Request $request)
    {
        try {

            $validator = validator::make($request->all(), [
                'user_id' => 'required',
                'company_id' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }


            $elements =  collect(PiDataElement::orderBy('pi_date_element')->get())->map(function ($obj) use ($request) {

                $c = FormData::where(['data_element' => $obj->id, 'user' => $request->user_id, 'company' => $request->company_id]);

                if ($request->fromDate && $request->toDate) {
                    $c->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
                }

                $c = $c->orderBy('created_at')->get();

                if (count($c) > 0) {

                    return [
                        "data_element_id" => $obj->id,
                        "data_element_name" => $obj->pi_date_element,
                        "first_collected_date" => CommonHelper::changeTimezone($c[0]->created_at),
                        "data_element_company_collected" =>  count($c),
                        "last_collected_date" => CommonHelper::changeTimezone($c[count($c) - 1]->created_at),
                        "data_element_company_collected_histroy" => collect($c)->map(function ($c_obj) {
                            return [
                                "id" => $c_obj->id,
                                "url" => $c_obj->url,
                                "created_at" => CommonHelper::changeTimezone($c_obj->created_at),
                            ];
                        }),
                    ];
                } else {
                    if (!($request->fromDate && $request->toDate)) {
                        return [
                            "data_element_id" => $obj->id,
                            "data_element_name" => $obj->pi_date_element,
                            "data_element_time_collected" => 0
                        ];
                    }
                }
            });

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'User FromData Get Successfully', '', collect($elements)->whereNotnull()->values());
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function userCompanyDataElement(Request $request)
    {
        try {

            $validator = validator::make($request->all(), [
                'data_element_id' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $formdata =  FormData::with('companyDetails')->where(['data_element' => $request->data_element_id, 'user' => $request->user_id]);

            if ($request->fromDate && $request->toDate) {
                $formdata->whereDate('created_at', '>=', $request->fromDate)->whereDate('created_at', '<=', $request->toDate);
                //  $formdata->whereBetween('created_at',[$request->fromDate,$request->toDate]);
            }

            $datas = $formdata->get();

            $datass = collect($datas)->map(function ($obj) use ($request) {
                // return $obj;
                $CompanyUrlName = CompanyUrlName::where('company_id', $obj->company)->first();
                return [
                    "id" => $obj->id,
                    "company_id" => $obj->company,
                    "company_url" => isset($CompanyUrlName) ? $CompanyUrlName->company_url_name : '',
                    "company_name" => $obj->companyDetails->company_name,
                    "logo" => $obj->companyDetails->company_logo,
                    "company_time_collected" => FormData::select('id')->where(['data_element' => $request->data_element_id, 'user' => $request->user_id, 'company' => $obj->company])->get()->count(),
                    "last_collected_date" => CommonHelper::changeTimezone(FormData::select('created_at')->where(['data_element' => $request->data_element_id, 'user' => $request->user_id, 'company' => $obj->company])->latest()->first()->created_at),
                    "company_time_url_collected" => collect(FormData::select('url', 'created_at')->where(['data_element' => $request->data_element_id, 'user' => $request->user_id, 'company' => $obj->company])->orderBy('created_at')->get()->map(function ($obj) {
                        return [
                            "url" => $obj->url,
                            "created_at" => CommonHelper::changeTimezone($obj->created_at),
                        ];
                    }))
                ];
            });
            $lastData = $datass->groupBy('company_id');
            $lastDatas = [];
            foreach ($lastData as $array) {
                array_push($lastDatas, $array[0]);
            }
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'FromData Get Successfully', '', $lastDatas);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function userDataElementList(Request $request)
    {
        try {

            $validator = validator::make($request->all(), [
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $datass = collect(FormData::with('pidataName:id,pi_date_element')->where('user', $request->user_id)->get())->map(function ($obj) use ($request) {
                return [
                    "data_element_id" => $obj->data_element,
                    "data_element_name" => $obj->pidataName->pi_date_element,
                ];
            });

            $lastData = $datass->groupBy('data_element_id');
            $lastDatas = [];
            foreach ($lastData as $array) {
                array_push($lastDatas, $array[0]);
            }
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'FromData Get Successfully', '', $lastDatas);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
}
