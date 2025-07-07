<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class SendNotif
{

    public static function sendMessage($content, array $id)
    {
        $content = array(
            "en" => $content,
        );

        $fields = array(
            'app_id' => "2edc35b0-dfab-42c0-9555-5bda70459f7c",
            'include_player_ids' => $id,
            // 'large_icon' => '@drawable/dndcolor',
            'small_icon' => '@drawable/dndcolor',
            'contents' => $content
        );

        $fields = json_encode($fields);
        // print("\nJSON sent:\n");
        error_log($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_exec($ch);
        curl_close($ch);

        // error_log($response);
    }

    public static function sendBroadcast($content)
    {
        $content = array(
            "en" => $content,
        );

        $fields = array(
            'app_id' => "2edc35b0-dfab-42c0-9555-5bda70459f7c",
            'included_segments' => array(
                'Subscribed Users'
            ),
            'small_icon' => '@drawable/dndcolor',
            'contents' => $content
        );

        $fields = json_encode($fields);
        // print("\nJSON sent:\n");
        error_log($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic MmVjNWQ3ODgtYWI4Mi00MGI0LWE2MTEtY2Q3OTJiNjdlMTkx'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response =  curl_exec($ch);
        curl_close($ch);
        return $response;
        // error_log($response);
    }
}
