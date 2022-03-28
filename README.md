# HyperPay
## Installation

You can install the package via [Composer](https://getcomposer.org).

```bash
composer require maree/hyper-pay
```
Publish your hyper-pay config file with

```bash
php artisan vendor:publish --provider="maree\hyperPay\HyperPayServiceProvider" --tag="hyperPay"
```
then change your hyperPay config from config/hyperPay.php file
```php
	
	"mode" => "test" , // test||live
    "token" =>  "",
    "entityIds" => [
        'VISA MASTER' => "",
        'STC_PAY'     => "",
        'MADA'        => "",
        'APPLEPAY'    => "",
        'AMEX'        => ""
    ],
```
## Usage

## first step
```php
use maree\hyperPay\HyperPay;
$customerInfo = ['email' => 'm7mdmaree26@gmail.com' , 'country' => 'EG' , 'givenName' => 'mohamed maree' ,'surname' => 'mohamed maree' , 'street1' => '23 elmagd' ,'city' => 'almehalla' ,'state' => 'gharbia' , 'postcode' => '1234'];
$brand = 'VISA MASTER'; //you can use 'VISA MASTER' or 'STC_PAY' or 'MADA' or 'APPLEPAY' or 'AMEX'
$response = HyperPay::checkout($amount = 1.0 ,$brand, $customerInfo);  

```
## note 
- use 'VISA MASTER' as one brand or key not two different brands or keys
- this function return ['checkoutId' => $checkoutId , 'responseData' => $responseData]
- use checkoutId to save transaction in database
- use checkoutId in view page in next step

## second step
- return view page with $checkoutId to show payment proccess
```php
    <style>
        .wpwl-container{
            z-index: 99;
            color : rgb(15, 13, 13);
        }
        .wpwl-control, .wpwl-group-registration{
            color:#333;
        }
        .pay-link{
            position: relative;
            z-index: 99;
        }
        .pay-link ul{
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pay-link ul li{
            margin: 10px;
        }
        .pay-link ul li img{
            width: 120px;
            height: 50px;
            max-width: 100%;
        }
        .wpwl-group{
            direction:ltr;
        }
        .wpwl-control-cardNumber{
            direction:ltr !important;
            text-align:right;
        }        
    </style>
    <!--  //you can use 'VISA MASTER' or 'STC_PAY' or 'MADA' or 'APPLEPAY' or 'AMEX' -->
    <form action="{{route('show-response-route')}}" class="paymentWidgets" data-brands="VISA MASTER"></form>
    <script>
        var wpwlOptions = {
            locale: "ar",
            paymentTarget:"_top",
        }
    </script>
    @if (config('hyperPay.mode') == 'live') 
        <script src="{{config('hyperPay.view_live_url').$checkoutId}}"></script>
    @else{
        <script src="{{config('hyperPay.view_test_url').$checkoutId}}"></script>
    @endif
```
## note 
- create route for response url 'show-response-route' 
EX: Route::get('show-response-route', 'PaymentsController@paymentresponse')->name('show-response-route'); 
- create function for checkout response 'paymentresponse'
- use that function to check if payment failed or success

## inside 'paymentresponse' function use:
```php
use maree\hyperPay\HyperPay;
//$brand = VISA MASTER || STC_PAY || MADA || APPLEPAY || AMEX
// $transactionid = $checkoutId || $request->resourcePath;
$response = HyperPay::checkoutResponseStatus($transactionid,$brand);  

```
return response like: 
```php

['key' => 'success' , 'msg'=> $description ,'checkoutId' => $checkoutId , 'responseData' => $responseData]  

```
or 

```php

['key' => 'fail'    , 'msg'=> $description ,'checkoutId' => $checkoutId , 'responseData' => $responseData];

```
note: you can use response from data to save transactions in database 

- Test Card Details
- Card Number: Visa: 4111111111111111
- CVV: 123
- Expiry Date: 05/22
- Card Name: Test Family

## APPLEPAY note
- in apple pay you recieve text file from hyperpay then put it inside public/.well-known directory in your project without change that file name
- you must use apple device to check it work successfuly
- you must exists in country provide apple pay payments
## current hyperpay package payment ways :
- visa
- master
- mada
- stc
- apple pay
- AMEX








