<?php
/**
 * Created by PhpStorm.
 * User: NAJMUL AHMED
 * Date: 12/3/2018
 * Time: 9:42 PM
 */
include_once 'config.php';

class BkashApi
{
    public $endpoint;
    public $appKey;
    public $appSecret;
    public $username;
    public $password;
    public $createPayment;
    public $executePayment;
    public $capturePayment;
    public $tokenGrant;
    public $tokenRefresh;

    public function __construct()
    {

        $this->endpoint = 'https://checkout.sandbox.bka.sh/v1.0.0-beta/checkout/';
        $this->createPayment = 'payment/create';
        $this->executePayment = 'payment/execute/';
        $this->capturePayment = 'payment/capture/';
        $this->tokenGrant = 'token/grant';
        $this->tokenRefresh = 'token/refresh';
        $this->appKey = '';
        $this->appSecret = '';
        $this->username = '';
        $this->password = '';
        $this->intent = "sale";
        $this->token = "";
        $this->paymentID =  "";

    }

    public function init($postData = array())
    {
        $tokenRequest = $this->generateToken();
        $this->token = $tokenRequest['id_token'];
        $amount  =  10;

        if($amount > 0){
            $paymentRequest = $this->createPayment($amount);
            $this->paymentID = $paymentRequest['paymentID'];
            $payRequest =  $this->executePayment($this->paymentID);

            echo "<pre>";
            print_r($payRequest);
        }



    }

    public function generateToken()
    {
        $post_token=array(
            'app_key'=> $this->appKey,
            'app_secret'=>$this->appSecret
        );
        $requestUrl = $this->endpoint. $this->tokenGrant;

        $postToken = json_encode($post_token);
        $headers = array(
            'Content-Type:application/json',
            'password:'.$this->password,
            'username:'. $this->username);
       return $this->curlRequest($requestUrl, $headers, $postToken);

    }

    public function createPayment($amount)
    {
        $createpaybody = array(
            'amount'=> $amount,
            'currency'=>'BDT',
            'intent'=> $this->intent,
            'merchantInvoiceNumber'=> uniqid()
        );
        $requestUrl = $this->endpoint. $this->createPayment;
        $createpaybodyx = json_encode($createpaybody);
        $headers = array(
            'Content-Type:application/json',
            'authorization:'.$this->token,
            'x-app-key:'. $this->appKey);

        return $this->curlRequest($requestUrl, $headers, $createpaybodyx);
    }

    public function executePayment($paymentID)
    {
        $requestUrl = $this->endpoint. $this->executePayment . $paymentID;
        $headers = array(
            'Content-Type:application/json',
            'authorization:'.$this->token,
            'x-app-key:'.$this->appKey);

       return $this->curlRequest($requestUrl, $headers);

    }
    public function curlRequest($requestUrl, $headers, $data = "")
    {
        $url = curl_init($requestUrl);
        curl_setopt($url, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        if (!empty($data)) {
            curl_setopt($url, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $results = curl_exec($url);
        curl_close($url);
        return json_decode($results, true);
    }

}