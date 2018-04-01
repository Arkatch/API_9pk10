<?php
///////////////////
//Include libary
///////////////////
	include('const.php');
///////////////////
//Data validate
///////////////////
	function validateDate($date, $format = 'Y-m-d'){
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
	function isValidDateRange($start, $end){
		if(strtotime($start) > strtotime($end)){
			return false;
		}
		return true;
	}
///////////////////
//MySQL and database function
///////////////////
	function connSQL(){
		$conn =  mysqli_connect(SERVER, USER, PASSWORD, DATABASE);
		mysqli_set_charset($conn, 'utf8');
		return $conn;
	}
	function getSQL($conn, $query){
		$arr = array();
		$res = mysqli_query($conn, $query);
		if($res == true){
			while ($row = mysqli_fetch_assoc($res)){
				$arr[] = $row;
			}
			return $arr;
		}
		return false;
	}
	function setSQL($conn, $query){
		if(mysqli_query($conn, $query) === TRUE){
			return true;
		}
		return false;
	}
	function addTransitToBase($records){
		//Create database connection
		//Create query from recive records 
		//Execute query, close connection and return true/false
		$conn = connSQL(); 
        $q = 'INSERT INTO transits VALUES("", "'.$records["startAddres"].'", "'.$records["endAddres"].'", "'.$records["distanceText"].'", "'.$records["distanceValue"].'", "'.$records["price"].'", "'.$records["date"].'")';
        $event = setSQL($conn, $q);
        mysqli_close($conn);
        return $event;
	}
	function getDailyReport($start, $end){
		$conn = connSQL();
		$q = 'SELECT distance, price FROM transits WHERE date BETWEEN "'.$start.'" AND "'.$end.'"';
		$res = getSQL($conn, $q);
		mysqli_close($conn);
		return $res;
	}
	function getMonthlyTotal(){
		$Ym = date('Y-m');
		$startDay = date('Y-m-d', strtotime(date('Y-m')));
		$endDay = date('Y-m-d', strtotime(date('Y-m-d'))-DAY);
		$allDays = date('d', strtotime($endDay)-strtotime($startDay));
		$conn = connSQL();
		$arr = array();
		for($i = 1;$i<$allDays;$i++){
			$day = $Ym.'-'.$i;   
			$q = 'SELECT SUM(distance) as total_distance, AVG(distance) as avg_distance, AVG(price) as avg_price FROM transits WHERE date = "'.$day.'"';
			$res = getSQL($conn, $q)[0];
			if($res['total_distance']){
				$arr[] = array(
					'date'				=> date('F, jS', strtotime($day)),
					'total_distance'	=> round($res['total_distance']/1000, 2).'km',
					'avg_distance'		=> round($res['avg_distance']/1000, 2).'km',
					'avg_price'			=> round($res['avg_price'], 2).'PLN'
				);
			}
		}
		mysqli_close($conn);
		return $arr;
	}
///////////////////
//cURL function
///////////////////
	function get($url){
		$error = $res = '';
		// create a new cURL resource
		$request = curl_init();									//Make connection
		// set URL and other appropriate options
		curl_setopt($request, CURLOPT_URL, $url);				//Connection address
		curl_setopt($request, CURLOPT_HEADER, FALSE);			//Get header == false
		curl_setopt($request, CURLOPT_FRESH_CONNECT, TRUE);		//No cache, always get fresh data from server
		curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);	//Return only text data (curl_exec() returns false/true too, and add 0/1 in last line)
		curl_setopt($request, CURLOPT_FAILONERROR, TRUE);		//HTTP server returns an error code that is >= 400
		$res = curl_exec($request);								//Get data into variable
		if($res === false){										//If error close connection and return false
			curl_close($request);	
			return false;
		}else{													//Else close connection and return response		
			curl_close($request);
			return $res;
		}
	}
///////////////////
//Other
///////////////////
	function polishCi($string){
		$char = array(
			'\u0104', '\u0105',
			'\u0106', '\u0107',
			'\u0118', '\u0119',
			'\u0141', '\u0142',
			'\u0143', '\u0144',
			'\u00d3', '\u00f3',
			'\u015a', '\u015b',
			'\u0179', '\u017a',
			'\u017b', '\u017c'
		);
		$plchar = array(
			'Ą', 'ą',
			'Ć', 'ć',
			'Ę', 'ę',
			'Ł', 'ł',
			'Ń', 'ń',
			'Ó', 'ó',
			'Ś', 'ś',
			'Ź', 'ź',
			'Ż', 'ż'      
		);
		return str_replace($char, $plchar, $string);
	}
	function validate($start, $end, $price, $date){
		$errorArray = array("status"=>true, "source_address"=>"", "destination_address"=>"", "price"=>"", "date"=>"");
		if(!$start){
			$errorArray["status"] = false;
			$errorArray["source_address"] = 'Source address is invalid, or null.';
		}
		if(!$end){
			$errorArray["status"] = false;
			$errorArray["destination_address"] = 'Destination address is invalid, or null.'; 
		}
		if(!is_numeric($price)){
			$errorArray["status"] = false;
			$errorArray["price"] = 'Price is not number, or null.'; 
		}
		if(validateDate($date) != 1){
			$errorArray["status"] = false;
			$errorArray["date"] = 'Wrong date, or date format.'; 
		}
		return $errorArray;
	}
?>