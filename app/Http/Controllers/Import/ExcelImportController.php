<?php

namespace App\Http\Controllers\Import;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Imports\PrivacyImport;
use App\Imports\CustomersImport;
use App\Models\Import\Excelupload;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExcelImportController extends Controller
{
    
  
    public function excelUpload(Request $request)
    {
        try {
           
            $validator = validator::make($request->all(), [
               'excel_file'=>'required'
            ]);

            if ($validator->fails()) {
                return CommonHelper::notFoundMessage($validator->errors(), config('constants.STATUS_CODE.HTTP_UNPROCESSABLE_ENTITY'));
            }
            // return $request->file();
     
            if ($request->file('excel_file')) {
                $files = $request->file('excel_file');
                $name = $files->getClientOriginalName();

                do {
                    $path =  'assest/excel/'.rand(10000000, 99999999);
                } while (Excelupload::where('file_name', $path)->exists());
                //  $path = 'assest/excel/'.Carbon::now();
        
            //   $file = $request->file->store($path);
            //   return true;
            //store file into document folder
              
                if (env('AWS_IMAGE_UPLOAD') == 1) {
                    Storage::disk('s3')->put($path, file_get_contents($files));
                } else {
                    if (!is_dir($path)) {
                        mkdir($path, 0755, true);
                    }
        
                    $files->move($path, $name);
                }
      
           $excelupload = Excelupload::create([
                    'file_name'=>$path.'/'.$name,
                    'original_name'=>$name,
                    'created_by'=>1,
                ]);
            
                return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'File Uploaded successfully', '','');
            }else{
                return CommonHelper::notFoundMessage('File Uploaded successfully', config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
            }

           
        } catch (Exception $ex) {
            
        }
    }
    public function excelUploadlist()
    {
        try {
         
           $data = collect(Excelupload::select( 'id','file_name','status','original_name','created_at','emailsent_at')->with('user')->get())->map( function($obj){
                // return $obj;   
                $obj['id'] = $obj->id;
               $obj['user_name'] = $obj->user ? $obj->user->first_name.' '.$obj->user->last_name : null ;
               $obj['statues'] = $obj->status;
               $obj['download_link'] = '' ;
               $obj['created_date'] = CommonHelper::changeTimezone($obj->created_at) ;
                return $obj;
            });

            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Get File Uploaded Data Successfully', $data->count(),$data);
        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }

    public function excelImport()
    {
        try {
            
          $excelFiles =  Excelupload::where('status',0)->get();
            foreach($excelFiles as $excelFile){
                $origanizationImport = new CustomersImport();
                Log::alert($excelFile);
                Excel::import($origanizationImport, public_path($excelFile->file_name));
                $excelUpload = Excelupload::where('id',$excelFile->id)->first();
                $excelUpload->status = 1;
                $excelUpload->save();
            }
          
            return CommonHelper::successfulMessage(config('constants.STATUS_CODE.HTTP_OK'), 'Get File Uploaded Data Successfully', '','');

        } catch (Exception $ex) {
            return CommonHelper::notFoundMessage($ex->getMessage(), config('constants.STATUS_CODE.HTTP_NOT_FOUND'));
        }
    }
   
}
