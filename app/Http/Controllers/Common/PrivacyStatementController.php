<?php

namespace App\Http\Controllers\Common;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Extension\CompanyTrack;
use App\Models\Extension\PrivacyStatement;
use Illuminate\Support\Facades\Validator;

class PrivacyStatementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'user_id' => 'required',
            ]);


            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $data = collect(CompanyTrack::with(['company', 'url:id,company_id,company_url_name', 'privacy_statement'])->where('user', $request->user_id)->get())->map(function ($obj) {
                // return $obj;
                return  [
                    "id" => $obj->company ? $obj->company->id : '',
                    "company_name" => $obj->company ? $obj->company->company_name : '',
                    "company_url" => $obj->url ? $obj->url->company_url_name : '',
                    "company_logo" => $obj->company ? $obj->company->company_logo : '',
                    "last_update_date" => $obj->privacy_statement ? date('m/d/Y', strtotime($obj->privacy_statement->elroi_internal_date)) : '',
                ];
            });

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), ' Data Get sucessfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function privacyData(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'company_id' => 'required',
            ]);


            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $data = collect(PrivacyStatement::select()->where('company', $request->company_id)->get())->map(function ($obj) {
                // return $obj;
                return  [
                    "id" => $obj->id,
                    "in_compliance_with_CCPA_statement" => $obj->in_compliance_with_CCPA_statement,
                    "in_compliance_for_GDPR_statement" => $obj->in_compliance_for_GDPR_statement,
                ];
            });

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), ' Data Get sucessfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
}
