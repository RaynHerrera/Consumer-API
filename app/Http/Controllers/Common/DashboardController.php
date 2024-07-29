<?php

namespace App\Http\Controllers\Common;


use Exception;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Models\Extension\FormData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Extension\CompanyTrack;

class DashboardController extends Controller
{
    public function getCompanyCount(){
        try {
           
            // $data = FormData::where('user', Auth::guard('api')->user()->id)->get()->groupBy('company');
            $data = collect(CompanyTrack::with('company:id,company_name,company_logo')->where('user', Auth::guard('api')->user()->id)->get())->map(function ($obj) {
                return $obj;
            });
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Company Get sucessfully', $data->count(), $data->count());
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function getUniqueCount(){
        try {
           
            $data = FormData::where('user', Auth::guard('api')->user()->id)->get()->groupBy('data_element');
         
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Data Element Get sucessfully', $data->count(), $data->count());
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
}
