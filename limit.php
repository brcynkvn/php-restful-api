<?php
session_start();
date_default_timezone_set('Europe/Istanbul');
//session_destroy();
function setSession($value,$data)
{
    $_SESSION[$value] = $data;
}

function getSession($value)
{
    return $_SESSION[$value];
}

function delSession($value)
{
    unset($_SESSION[$value]);
}

function getIp() {
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
        $ip = $_SERVER['REMOTE_ADDR'];
    return $ip;
}

function timeControl($start,$current,$count){
    $start = strtotime($start);
    $curr = strtotime($current);
    $sec =  abs($start - $curr);

    if ($sec <= 60 && $count < 10) {
        return true;
    }else{
        return false;
    }
}

function checkSession(){
    if(isset($_SESSION['ReqLimit'])){
        $jsonSession = json_decode(getSession('ReqLimit'),true);
        foreach ($jsonSession as $key=>$arrSession){
            $ips = key($jsonSession);
            if($ips == getIp()){
                if(timeControl($jsonSession[$ips]['startDate'],date("Y-m-d h:i:s"),$jsonSession[$ips]['count'])){
                    $jsonSession[$ips] = array(
                        "startDate" => $jsonSession[$ips]['startDate'],
                        "lastDate" => date("Y-m-d h:i:s"),
                        "count" => $jsonSession[$ips]['count'] + 1
                    );
                    setSession('ReqLimit',json_encode($jsonSession));
                }else{
                    $lastDate = strtotime($jsonSession[$ips]['lastDate']);
                    $curr = strtotime(date("Y-m-d h:i:s"));
                    if(abs($lastDate - $curr) >60) {
                        $jsonSession[$ips] = array(
                            "startDate" => date("Y-m-d h:i:s"),
                            "lastDate" => date("Y-m-d h:i:s"),
                            "count" => 1
                        );
                        setSession('ReqLimit',json_encode($jsonSession));
                    } else {
                        die(json_encode("Try again after 1 minute"));
                    }
                }
            }
            next($jsonSession);
        }
    }else{
        $ip = getIp();
        $newArray[$ip] = array(
            "startDate" => date("Y-m-d h:i:s"),
            "lastDate" => date("Y-m-d h:i:s"),
            "count" => 1
        );
        setSession('ReqLimit',json_encode($newArray));
    }
}

checkSession();
