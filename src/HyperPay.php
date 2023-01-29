<?php
namespace maree\hyperPay;

class HyperPay {
    //$customerInfo = ['email' => 'maree@site.com' , 'country' => 'EG' , 'givenName' => 'mohamed maree' ,'surname' => 'mohamed maree' , 'street1' => '23 elmagd' ,'city' => 'almehalla' ,'state' => 'gharbia' , 'postcode' => '1234']
    //$brand = VISA MASTER || STC_PAY || MADA || APPLEPAY || AMEX
    public static function checkout($amount = 0.0 ,$brand = 'VISA MASTER',$customerInfo = []) {
            if(config('hyperPay.mode') == 'live'){
               $request_url = config('hyperPay.request_live_url');
               $curlopt     = true;
               $testMode    = "";
            }else{
               $request_url = config('hyperPay.request_test_url');
               $curlopt     = false;
               $testMode    = "&testMode=EXTERNAL";
            }
            $entityId = config('hyperPay.entityIds.'.$brand);
            $amount = number_format((float)$amount, 2, '.', '');
            $data = "entityId=".$entityId.
                "&amount=".$amount.
                "&currency=".config('hyperPay.currency').
                "&merchantTransactionId=".rand(1111111,9999999).
                "&customer.email=".$customerInfo['email'].
                "&paymentType=DB".
                "&billing.country=".$customerInfo['country'].
                "&customer.givenName=".$customerInfo['givenName'].
                "&customer.surname=".$customerInfo['surname'].
                "&billing.street1=".$customerInfo['street1'].
                "&billing.city=".$customerInfo['city'].
                "&billing.state=".$customerInfo['state'].
                "&billing.postcode=".$customerInfo['postcode'];
            $data .= $testMode;    

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:Bearer ".config('hyperPay.token')));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $curlopt);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if(curl_errno($ch)) {
                return curl_error($ch);
            }
            curl_close($ch);
            $responseData = json_decode($responseData);
            $checkoutId   = $responseData->id;
            return ['checkoutId' => $checkoutId , 'responseData' => $responseData];
    }

    //$CardInfo = ['Brand' => 'VISA' , 'number' => '4200000000000000' , 'holder' => 'mohamed maree' ,'expiryMonth' => '05' , 'expiryYear' => '2034' ,'cvv' => '123']
    public static function checkoutDirectly($amount = 0.0 ,$brand = 'VISA MASTER',$CardInfo = []) {
        if(config('hyperPay.mode') == 'live'){
           $request_url = config('hyperPay.direct_checkout_live_url');
           $curlopt     = true;
        }else{
           $request_url = config('hyperPay.direct_checkout_test_url');
           $curlopt     = false;
        }
            $entityId = config('hyperPay.entityIds.'.$brand);
            $amount = number_format((float)$amount, 2, '.', '');
            $data = "entityId=".$entityId.
                    "&amount=".$amount.
                    "&currency=".config('hyperPay.currency').
                    "&paymentBrand=".$CardInfo['Brand'].
                    "&paymentType=DB" .
                    "&card.number=".$CardInfo['number'].
                    "&card.holder=".$CardInfo['holder'].
                    "&card.expiryMonth=".$CardInfo['expiryMonth'].
                    "&card.expiryYear=".$CardInfo['expiryYear'].
                    "&card.cvv=".$CardInfo['cvv'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:Bearer ".config('hyperPay.token')));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $curlopt);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData = json_decode($responseData);
        $checkoutId   = $responseData->id;
        $code         = isset($responseData->result->code ) ? $responseData->result->code  :'-1';
        $description  = isset($responseData->result->description ) ? $responseData->result->description  :'some errors occured';
        if ( self::success($code) ){
            return  ['key' => 'success' , 'msg'=> $description ,'checkoutId' => $checkoutId , 'responseData' => $responseData];
        }else{
            return  ['key' => 'fail'    , 'msg'=> $description ,'checkoutId' => $checkoutId , 'responseData' => $responseData];
        } 
    }

    //$brand = VISA MASTER || STC_PAY || MADA || APPLEPAY || AMEX
    // $transactionid = $request->resourcePath;
    public static function checkoutResponseStatus($transactionid ='',$brand = 'VISA MASTER'){
        if(config('hyperPay.mode') == 'live'){
           $response_url = config('hyperPay.response_live_url');
           $curlopt     = true;
        }else{
           $response_url = config('hyperPay.response_test_url');
           $curlopt     = false;
        }
        $response_url .= $transactionid;
        $response_url .= "?entityId=".config('hyperPay.entityIds.'.$brand);;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $response_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Authorization:Bearer ".config('hyperPay.token')));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $curlopt);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData = json_decode( $responseData, true );
        
        $code         = isset($responseData[ 'result' ][ 'code' ] ) ? $responseData[ 'result' ][ 'code' ]  :'-1';
        $description  = isset($responseData[ 'result' ][ 'description' ] ) ? $responseData[ 'result' ][ 'description' ]  :'some errors occured';
        $checkoutId   = $responseData['ndc'];
        if ( self::success($code) ){
            return  ['key' => 'success' , 'msg'=> $description ,'checkoutId' => $checkoutId , 'responseData' => $responseData];
        }else{
            return  ['key' => 'fail'    , 'msg'=> $description ,'checkoutId' => $checkoutId , 'responseData' => $responseData];
        }
    }


    public static function success($code){
        $codePattern   = '/^(000\.000\.|000\.100\.1|000\.[36])/';
        $manualPattern = '/^(000\.400\.0|000\.400\.100)/';

        $successCodes  = [
            '000.000.000',
            '000.000.100',
            '000.100.110',
            '000.100.111',
            '000.100.112',
            '000.300.000',
            '000.300.100',
            '000.300.101',
            '000.300.102',
            '000.600.000',
            '000.200.100'
        ];
        if ( preg_match($codePattern, $code) || preg_match($manualPattern, $code)){
            return  true;
        }else if (in_array( $code, $successCodes )){
            return  true;
        }else{
            return false;
        }
    }

}