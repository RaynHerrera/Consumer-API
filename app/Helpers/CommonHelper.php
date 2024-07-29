<?php

namespace App\Helpers;


use Carbon\Carbon;
use App\Mail\CommonMail;
use App\Model\Connector\Role;
use App\Model\Connector\Product;
use Illuminate\Support\Facades\DB;
use App\Model\Common\Configuration;
use App\Model\Common\Debugger_logs;
use App\Model\Connector\UserHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Model\CreateDebuggerLogTable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Exceptions\HttpResponseException;


class CommonHelper
{

    public static function notFoundMessage($message, $code)
    {
        $response = [
            'code' => $code,
            'message' => $message,
        ];
        return response()->json($response, $response['code']);
    }



    public static function successfulMessage($code, $message, $count, $payload)
    {
        $response = [
            'code' => $code,
            'message' => $message,
            'count' => $count,
            'data' => $payload,
        ];
        return response()->json($response, $response['code']);
    }

    public static function changeTimezone($time)
    {
        return $time != null ? date('m/d/Y h:i A', strtotime($time)) : $time;
    }

    public static function sendResponse($message, $code)
    {
        header('Cache-Control: no-cache, private');
        header('Content-type: application/json');
        header('access-control-allow-origin: *');
        header("HTTP/1.1 " . $code);

        echo json_encode(
            array(
                'code' => $code,
                'message' => $message,
            )
        );
        exit;
    }

    public static function getIpInformation($ipaddress)
    {
        $json     = json_decode(file_get_contents('https://geolocation-db.com/json/' . $ipaddress), true);
        return $json;
    }
}
