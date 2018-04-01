<?php
    header("Access-Control-Allow-Origin: *");
    header('Content-type:application/json; charset=utf-8');
    include('../php/function.php');
    if($_SERVER['REQUEST_METHOD'] == "GET"){
        echo 
        'API require POST request. ('.$_SERVER['REQUEST_METHOD'].' is current)
        Exemple request:
        {
            "source_address": "ul. Zakręt 8, Poznań",
            "destination_address": "Złota 44, Warszawa",
            "price": 450,
            "date": "2018-03-15"
        }
        Request adress: 
        https://localhost/transits/
        
        If you are using POST request and get this mesage in response,
        make sure the addres of is valid.
        Valid address:
        https://localhost/transits/
        ';
        return;
    }
    //Get json from AJAX request and set object to array
    $jsonData = (array)json_decode(file_get_contents('php://input'));
    //Data variable
    $start = $jsonData['source_address'];
    $end = $jsonData['destination_address'];
    $price = $jsonData['price'] ;
    $date = $jsonData['date'] ;
    //Validate data
    $status = validate($start, $end, $price, $date);
    //If validate fail do nothing and return errors
    if(!$status['status']){
        echo implode('|', $status);
        return;
    }

    //Make request URL, and get json with cURL function
    $url = 'https://maps.googleapis.com/maps/api/directions/json?origin='.urlencode($start).'&destination='.urlencode($end).'&key='.APIKEY;
    $res = get($url);
    if($res){
        //Get value to new array
        $res = (array)json_decode(getJson($url));
        $res = (array)($res['routes'][0]);
        $add = (array)($res['legs'][0]);
        $dis = (array)($add['distance']);
        $arr = array(
            'startAddres'   => $add['start_address'],
            'endAddres'     => $add['end_address'],
            'distanceText'  => $dis['text'],
            'distanceValue' => $dis['value'],
            'price'         => $price,
            'date'          => $date
        );
        //Add records to database
        if(addTransitToBase($arr) === TRUE){
            echo 'Success!';
        }else{
            echo 'Error!';
        }
    }
?>