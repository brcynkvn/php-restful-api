<?php
require_once ("limit.php");
$row = 1;
$array = [];
$queryArr = [];
$queryArray = [];

if (($handle = fopen("places.csv", "r")) !== FALSE) {
    $i = 0;
    //Read csv file by line
    while (($lineArray = fgetcsv($handle, 4000, ",")) !== FALSE) {
        for ($j=0; $j<count($lineArray); $j++) {
            if($i != 0){
                $queryArray[$i][$j] = $lineArray[$j];
            }
        }
        $i++;
    }

    //Group city name as array
    $result = array();
    foreach ($queryArray as $key =>$element) {
        $result[$element[0]][] = $element[1];
    }

    // Call Request Get
    if($_SERVER['REQUEST_METHOD'] == "GET") {
        if(isset($_GET['city'])) {
            if(isset($_GET['limit'])) {
                $limit = $_GET['limit'];
            }else{
                $limit = 10;
            }
            if(isset($_GET['sort'])) {
                $sort = $_GET['sort'];
            }else{
                $sort = "DESC";
            }
            foreach ($queryArray as $key=>$data) {
                if($data[0] == $_GET['city']) {
                    $queryArr['city'] = $data[0];
                    $queryArr['places'][] = $data[1];
                    if($limit <= $key) {
                        break;
                    }
                }
            }
            if(isset($queryArr['places'])) {
                if($sort == "DESC") {
                    asort($queryArr['places']);
                } else {
                    arsort($queryArr['places']);
                }
            }

        }else {
            $new =[];
            foreach ($result as $key=>$data) {
                $queryArr['city'] = $key;
                foreach ($data as $place) {
                    $queryArr['places'][] = $place;

                }
                array_push($new,$queryArr);
                unset($queryArr['places']);
            }
            $queryArr = $new;
        }

    }
    // Return json query
    print_r(json_encode($queryArr));
    fclose($handle);
}


