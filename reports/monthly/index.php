<?php
    header("Access-Control-Allow-Origin: *");
    include('../../php/function.php');
    echo json_encode(getMonthlyTotal());
?>