<?php

function get_sensor_info($id){
    /*Pedestrian Counting System - Sensor Locations*/
    $sensor_json_url = 'https://data.melbourne.vic.gov.au/resource/h57g-5234.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$sensor_json_url . '?sensor_id=' . $id);
    $result=curl_exec($ch);
    curl_close($ch);
    if(isset(json_decode($result)[0]))
        return json_decode($result)[0];
    else return NULL;
}

function get_sensors_data_last_day(){
    /*Pedestrian Counting System - Past Hour (counts per minute)*/
    $sensor_json_url = 'https://data.melbourne.vic.gov.au/resource/d6mv-s43h.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$sensor_json_url);
    $result=curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}

function get_sensor_data_last_hour($id){
    /*Pedestrian Counting System - Past Hour (counts per minute)*/
    $sensor_json_url = 'https://data.melbourne.vic.gov.au/resource/d6mv-s43h.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$sensor_json_url . '?sensor_id=' . $id);
    $result=curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}

function get_sensors_data_by_timestamp($timestamp){
    /*Pedestrian Counting System - Monthly (counts per hour)*/
    $sensor_json_url = 'https://data.melbourne.vic.gov.au/resource/b2ak-trbp.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$sensor_json_url . '?date_time=' . $timestamp);
    $result=curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}

function get_last_available_with_time($time){
    /*Pedestrian Counting System - Monthly (counts per hour)*/
    $sensor_json_url = 'https://data.melbourne.vic.gov.au/resource/b2ak-trbp.json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$sensor_json_url . '?$limit=1&$order=:id%20DESC&time=' . $time );
    $result=curl_exec($ch);
    curl_close($ch);
    if(isset(json_decode($result)[0]))
        return json_decode($result)[0];
    else return NULL;
}

function get_busiest_in_hour($time){
    $max = 0;
    $max_sensor = NULL;
    $last_available_data_time = get_last_available_with_time($time);
    $sensors_data = get_sensors_data_by_timestamp($last_available_data_time->date_time);
    foreach ($sensors_data as $sensor_data){
        if($sensor_data->hourly_counts > $max){
            $max = $sensor_data->hourly_counts;
            $max_sensor = $sensor_data;
        }
    }
    return $max_sensor->sensor_name;
}

function get_sensor_info_last_day($id){
    return get_sensor_data_last_hour($id);
}

function get_busiest_last_day(){
    $sensors_data = get_sensors_data_last_day();
    $max_id=0;
    $max_count=0;
    $max_count_sensor = NULL;
    foreach($sensors_data as $sensor_data){
        if($sensor_data->sensor_id>$max_id)
            $max_id = $sensor_data->sensor_id;
    }
    $counts_per_sensor_id = array_fill(0,$max_id+1,0);
    foreach($sensors_data as $sensor_data){
        $counts_per_sensor_id[$sensor_data->sensor_id] += $sensor_data->total_of_directions;
    }
    foreach($sensors_data as $sensor_data){
        if($counts_per_sensor_id[$sensor_data->sensor_id]>$max_count) {
            $max_count = $counts_per_sensor_id[$sensor_data->sensor_id];
            $max_count_sensor = $sensor_data;
        }
    }
    $max_count_sensor_data = get_sensor_info($max_count_sensor->sensor_id);
    //echo print_r($max_count_sensor_data);
    return array("sensor_id" => $max_count_sensor->sensor_id,
        "sensor_description" => $max_count_sensor_data->sensor_description);
}