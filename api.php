<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
require "data.php";

if(!empty($_GET['hour'])) {
    $hour=$_GET['hour'];
    $data = get_busiest_in_hour($hour);
    if($hour>23||$hour<0){
        response(400,"Invalid Request",NULL);
    }
    elseif(empty($data)) {
        response(200,"Data Not Found",NULL);
    }
    else {
        response(200,"Data Found",$data);
    }

}
elseif(!empty($_GET['id']))
{
    $id=$_GET['id'];
    $data = get_sensor_info_last_day($id);

    if(empty($data)) {
        response(200,"Data Not Found",NULL);
    }
    else {
        response(200,"Data Found",$data);
    }

}
else
{
    $data = get_busiest_last_day();
    if(empty($data)) {
        response(200,"Data Not Found",NULL);
    }
    else {
        response(200,"Data Found",$data);
    }
}

function response($status,$status_message,$data)
{
    header("HTTP/1.1 ".$status);

    $response['status']=$status;
    $response['status_message']=$status_message;
    $response['data']=$data;

    $json_response = json_encode($response);
    echo $json_response;
}