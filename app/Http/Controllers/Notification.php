<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Response;
use App;
use Exception;

class Notification extends Controller
{
    public function iosPushNotification($body_text,$notification_type,$tokens,$id)
    {   
        $passphrase = '';
        $pem_file = base_path() . '/app/rfebe.pem';
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_file);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp){
            return false;
        }
        $body_ar = $body_text;

        if($notification_type == 2){
            $body['aps'] = array(
                'alert' => $body_text['userName'].' '.$body_text['type'],
                'sound' => 'default',
            ); 
        }

        if($notification_type == 1){
            $body['aps'] = array(
                'alert' => $body_text['userName'].' '.$body_text['type'],
                'sound' => '',
            ); 
        }
        
        $body['data'] = $body_text;        
        $payload = json_encode($body);
        for ($i=0; $i < count($tokens); $i++) {
            $msg = chr(0) . pack('n', 32) . pack('H*', $tokens[$i]) . pack('n', strlen($payload)) . $payload;
            $result = fwrite($fp, $msg, strlen($msg));
        }
        if (!isset($result)){
            return false;
        }
        fclose($fp);
        return true;
    }

    public function androidPushNotification($body_text,$notification_type,$tokens,$id)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array (
                'Authorization: key=' . "AIzaSyBaNAFON6Z7WlrMd4q5sFvhoBYPKtUigeM",
                'Content-Type: application/json'
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        
        $fields = array (
            'registration_ids' => $tokens,                
           'data' => array (
                    "message" => $body_text
            )
        );
        $fields = json_encode ( $fields );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
        $result = curl_exec ( $ch );

        if(curl_error($ch))
        {
            echo 'error:' . curl_error($ch);exit();
        }
        $json = json_decode($result, true);

        if($json['success']){
            $status=1;
        }
        curl_close ( $ch );

        if($json['success']){
            return 1;
        } else {
            return 0;
        }
    }
}

   