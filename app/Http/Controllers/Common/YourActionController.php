<?php

namespace App\Http\Controllers\Common;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Extension\CompanyAction;
use App\Models\Extension\CompanyUrlName;
use App\Models\Extension\PrivacyStatement;
use Illuminate\Support\Facades\Validator;

class YourActionController extends Controller
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

            $companyAction = CompanyAction::with('company')->where('user_id', $request->user_id);
               
            if (isset($request->company_id)) {
                $companyAction->where('company_id', $request->company_id);
            }
            $data = collect($companyAction->orderBy('updated_at','desc')->get())->map(function ($obj) {
                // return $obj;
                $CompanyUrlName = CompanyUrlName::where('company_id', $obj->company->id)->first();
                //    $data = collect(CompanyAction::with('company')->where('user_id',$request->user_id)->get())->map(function($obj){
                return [
                "id" => $obj->id,
                "company_name" => $obj->company ? $obj->company->company_name : '',
                "company_url" => $CompanyUrlName ? $CompanyUrlName->company_url_name :'',
                "company_logo" => $obj->company ? $obj->company->company_logo : '',
                "action_type" => CompanyAction::action_type($obj->action_type),
                "action_status" => CompanyAction::action_status($obj->action_status),
                "action_status_id" => $obj->action_status,
                "created_date" => date('m/d/Y h:i A',strtotime($obj->created_at)),
                "updated_date" => date('m/d/Y h:i A',strtotime($obj->updated_at)),
                
            ];
            });
           
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), ' Your Action Data Get sucessfully', $data->count(), $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function update(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'company_action_id' => 'required',
                'action_status' => 'required',
                ]);
    
            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $data = CompanyAction::where('id', $request->company_action_id)->first();
            $data->action_status = $request->action_status;
            $data->save();
           
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), ' Your Action Data Updated sucessfully', '', '');
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function getCompany($id)
    {
        try {

            $data = PrivacyStatement::select('california_consumer_request_information_link','alifornia_consumer_request_information_phone_number','california_consumer_request_information_other','for_Non_CA_citizen_action_rights_other','for_Non_CA_citizen_action_rights_phone_number','for_Non_CA_citizen_action_rights_link')->where('company', $id)->first();
           
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), ' Your Action Data Updated sucessfully', '',  $data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
    public function Add(Request $request)
    {
        try {

           $validator = validator::make($request->all(), [
                'user_id' => 'required',
                'company_id' => 'required',
                'action_type' => 'required',
                ]);
    
            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }

            $companyAction = CompanyAction::create($request->all());
           
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), ' Your Action Data Added sucessfully', '',  '');
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
}
