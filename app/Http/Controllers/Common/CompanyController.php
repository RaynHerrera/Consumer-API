<?php

namespace App\Http\Controllers\Common;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Models\Extension\Company;
use App\Models\Extension\FormData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Extension\CompanyTrack;
use Illuminate\Support\Facades\Storage;
use App\Models\Extension\CompanyUrlName;
use App\Models\Extension\PiDataElement;
use Illuminate\Support\Facades\Validator;
use App\Models\Extension\PrivacyStatement;
use App\Models\Extension\PurePiDataNameList;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function getFromData($company_id)
    {
        try {
            $data = collect(FormData::select('data_element')->where('company', $company_id)->where('user', Auth::guard('api')->user()->id)->limit(12)->groupBy('data_element')->get())->map(function ($obj) use ($company_id) {
                $firstcollectedDate = FormData::select('created_at')->where('data_element', $obj->pidataName->id)->where('company', $company_id)->where('user', Auth::guard('api')->user()->id)->orderBy('created_at', 'asc')->get()->first();
                $latestcollectedDate = FormData::select('created_at')->where('data_element', $obj->pidataName->id)->where('company', $company_id)->where('user', Auth::guard('api')->user()->id)->orderBy('created_at', 'desc')->get()->first();
                return [
                    "name" => $obj->pidataName ? $obj->pidataName->pi_date_element : '',
                    "created_date" => $firstcollectedDate ? CommonHelper::changeTimezone($firstcollectedDate->created_at) : null,
                    "url" => $obj->url,
                    "collected" =>  true,
                    "total_time_collected" => FormData::select('id')->where('data_element', $obj->data_element)->where('company', $company_id)->where('user', Auth::guard('api')->user()->id)->count(),
                    "last_collected_date" => $latestcollectedDate ? CommonHelper::changeTimezone($latestcollectedDate->created_at) : null,
                ];
            });

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'FromData Get Successfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function getCompanyList(Request $request)
    {
        try {

            $query = CompanyTrack::with(['company:id,company_name,company_logo', 'url:id,company_id,company_url_name'])->where('user', Auth::guard('api')->user()->id);

            if ($request->limit) {
                $query->orderBy('created_at', 'desc')->limit($request->limit);
            }

            $result = $query->get();
            $data = collect($result)->map(function ($obj) {
                $obj['company_name'] = $obj->company ? $obj->company->company_name : '';
                return $obj;
            });

            if ($request->limit) {
                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Count', $data->count(), $data);
            } else {

                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Count', $data->count(), collect($data)->sortBy('company_name')->values());
            }
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function getCompanyListForMobile()
    {
        try {
            $data = collect(CompanyTrack::with('company:id,company_name,company_logo')->where('user', Auth::guard('api')->user()->id)->orderBy('created_at', 'desc')->limit(10)->get())->map(function ($obj) {
                return $obj;
            });

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Count', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function getPrivacyPolicy(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'company_id' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $data = PrivacyStatement::where('company', $request->company_id)->get();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Pi Data Get sucessfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }


    public function add(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'name' => 'required',
                'url' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $company_url = CompanyUrlName::where('company_url_name', $request->url)->first();
            if (!$company_url) {
                $company = Company::create([
                    "company_name" => $request->name,
                    "company_logo" => ''
                ]);

                $company_url = CompanyUrlName::create([
                    "company_id" => $company->id,
                    "company_url_name" => $request->url
                ]);
            }

            $company_track = CompanyTrack::where('company_id', $company_url->company_id)->where('user', Auth::guard('api')->user()->id)->first();
            if (!$company_track) {
                CompanyTrack::create([
                    "company_id" => $company_url->company_id,
                    "user" => Auth::guard('api')->user()->id
                ]);
            }

            $user_company_urls = CompanyUrlName::selectRaw('company_url_name AS url_name')->whereIn('company_id', CompanyTrack::where('user', Auth::guard('api')->user()->id)->get()->pluck('company_id'))
                ->get();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Added sucessfully', '', $user_company_urls);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function getCompanyDashboard(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'user_id' => 'required',
                'company_id' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $data = collect(CompanyTrack::with(['company', 'url'])->where(['user' => $request->user_id, 'company_id' => $request->company_id])->get())->map(function ($obj) use ($request) {
                // return $obj;   
                $dataElement = FormData::where(['user' => $request->user_id, 'company' => $request->company_id])->orderBy('created_at')->get();
                $lastData = $dataElement->groupBy('data_element');
                $lastDatas = [];
                foreach ($lastData as $array) {
                    array_push($lastDatas, $array[0]);
                }
                return [
                    "company_name" => $obj->company ?  $obj->company->company_name : '',
                    "company_url" => $obj->url ? $obj->url->company_url_name : "",
                    "company_logo" => $obj->company ? $obj->company->company_logo : '',
                    "total_data_element" => PiDataElement::count(),
                    "total_collected_data_element" => count($lastDatas),
                    "first_collected_date" => $dataElement->first() ? CommonHelper::changeTimezone($dataElement->first()->created_at) : '',
                ];
            });

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Added sucessfully', '', $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function getCompanyDashboardPrivacyStatement(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                // 'user_id' => 'required',
                'company_id' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $data = PrivacyStatement::select('in_compliance_with_CCPA_statement', 'in_compliance_for_GDPR_statement')->where('company', $request->company_id)->get();

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Privacy Data get sucessfully', '', $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }


    public function detact(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'url' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $user_company = CompanyTrack::where('user', Auth::guard('api')->user()->id)->get()->pluck('company_id');

            $user_company_urls = collect(CompanyUrlName::with('company:id,company_name')->whereIn('company_id', $user_company)->get())->map(function ($obj) {
                return [
                    "url_name" => $obj->company_url_name,
                    "company" => $obj->company ? $obj->company->company_name : null
                ];
            });

            $company_url = CompanyUrlName::where('company_url_name', $request->url)->first();


            if ($company_url) {
                $company_track = CompanyTrack::where('company_id', $company_url->company_id)->where('user', Auth::guard('api')->user()->id)->first();
                if ($company_track) {
                    return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company already linked with user', 1, $user_company_urls);
                } else {
                    return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company not link with user', 0, $user_company_urls);
                }
            } else {
                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Not Exists In Database', 0, $user_company_urls);
            }
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }


    public function setLogo(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'logo' => 'required',
                'host' => 'required',
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $companyUrl = CompanyUrlName::where('company_url_name', $request->host)->first();

            if ($companyUrl) {
                $company = Company::where('id', $companyUrl->company_id)->first();
                if (!$company) {
                    return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Not Exists In Database', '', true);
                } else {
                    $company->company_logo = $request->logo;
                    $company->save();
                    return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Logo Updated', '', true);
                }
            } else {
                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Not Exists In Database', '', true);
            }
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
}
