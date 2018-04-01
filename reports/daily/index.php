<?php
    header("Access-Control-Allow-Origin: *");
    include('../../php/function.php');
    $errors = array('status'=>true, 'msg_1'=>null, 'msg_2'=>null);
    $startDate = $endDate = '';

    //Check date is exist
    if(!isset($_GET['start_date']) or !isset($_GET['end_date'])){
        echo json_encode(array('status'=>false, 'msg' =>
        'HTTP request method GET with parametrs [start_date] and [end_date] is needed.||Example request: http://example.com/reports/daily?start_date=YYYY-MM-DD&end_date=YYYY-MM_DD'));
        return false;
    }

    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
    //Validate date
    //Check format
    if(!validateDate($startDate) or !validateDate($endDate)){
        $errors['status'] = false;
        $errors['msg_1']= 'Date format is invalid. Valid date format: YYYY-MM_DD';
    }
    //Check range of date
    if(!isValidDateRange($startDate, $endDate)){
        $errors['status'] = false;
        $errors['msg_2']= 'Date range is invalid. Start date must be older than end date.';
    }
    //If there is any error, show them and return false
    //Else get data from database and show them
    if(!$errors['status']){
        echo json_encode($errors);
        return false;
    }else{
        $res = getDailyReport($startDate, $endDate);
        $distance = 0;
        $price = 0;
        if(!$res){
            echo json_encode(array('status'=>false, 'msg'=>'Error or no data.'));
            return false;
        }
        foreach($res as $val){
            $distance += $val['distance'];
            $price += $val['price'];
        }
        echo json_encode(array(
            'total_distance' => ($distance/1000).'km',
            'total_price' => ($price).'PLN'
        ));
        return true;    
    }
?>