<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Member;
use App\Payment;
use App\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;


class paymentController extends Controller
{

    public function __construct()
    {
        $this->handlePayment();
    }


    public function getPayment(Request $request){

        $header = $request->input('api_header');
        $fcode = $request->input('fcode');


        if($this->checkHeader($header)){

            return $this->getPaymentData($fcode);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'User Not Allowed To Access This Data'
            ]);
        }

    }

    public function createPayment(Request $request){


        $validator = Validator::make($request->all(), [
            'api_header' => 'required',
            'range' => 'required',
            'payments' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Error , Missing inputs...'
            ],200);
        }

        $header = $request->input('api_header');

        if($this->checkHeader($header)){

            $range = explode(',',$request->input('range'));
            $paymentsJson = $request->input('payments');
            $total = 0;
            $payments = json_decode($paymentsJson);
            if (count($range) < 2){
                // if one year
                $matchYear = 0;

                foreach($payments as $payment){
                    if ($payment->yearEnd == $range[0]){
                        $total += $payment->yearValue;
                        $matchYear ++;
                    }
                }

                //if the selected year not exists on this fcode payments years
                if ($matchYear < 1){
                    return response()->json([
                        'status' => false,
                        'message' => 'Selected Years Not Valid'
                    ]);
                }

                $fcode = $request->input('fcode');
                $details = [$range[0] => $total];
                $code = date("Y").mt_rand(0,100000);

                return $this->CreateReturnPayment($request, $total, $code, $details, $fcode);

            }else{ // if many years
                $start = $range[0];
                $end = $range[1];
                $years = range($start, $end);
                $details  = array_fill_keys($years, 0); // create array the key of it is the year and the value is 0 to fill it with the year payment

                /*
                 * loop array to fill the year "key" with the payment "value"
                 */

                foreach($payments as $payment){
                    if (array_key_exists($payment->yearEnd, $details)){
                        $details[$payment->yearEnd] = $payment->yearValue;
                    }
                }

                // check if there a year with value of 0 " not existed on this fcode payment years "
                foreach ($details as $paidYear){
                    if ($paidYear == 0){
                        return response()->json([
                            'status' => false,
                            'message' => 'Selected Years Not Valid'
                        ]);
                    }
                }



                $code = date("Y").mt_rand(0,10000);
                $total += array_sum($details);
                $fcode = $request->input('fcode');

                return $this->CreateReturnPayment($request, $total, $code, $details, $fcode);

            }

            //if not one year or many year

            return response()->json([
                'status' => false,
                'message' => 'Error - No payments Ordered ...'
            ]);

        }
        // if api_header not existed
        else{
            return response()->json([
                'status' => false,
                'message' => 'User Not Allowed To Access This Data'
            ]);
        }

    }

    public function showOldPayments(Request $request){

        $header = $request->input('api_header');
        $fcode = $request->input('fcode');

        if ($this->checkHeader($header)){

            $oldPayments = Payment::where('fcode', '=',$fcode)->where('confirmed','=',0)->latest()->get();
            if ($oldPayments != null && $fcode != null){

                if (count($oldPayments) > 0){
                    return response()->json([
                        'status' => true,
                        'message' => 'payments created existed',
                        'payments' => $oldPayments
                    ],200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'There is no old payments yet ..'
                    ]);
                }

            }else{ // if old payments = null
                return response()->json([
                    'status' => false,
                    'message' => 'User didn\'t create old payments ..'
                ]);
            }

        }else{ // if api_header not existed
            return response()->json([
                'status' => false,
                'message' => 'User Not Allowed To Access This Data'
            ]);
        }

    }

    public function getExistedPayments(Request $request){
        $header = $request->input('api_header');
        $fcode = $request->input('fcode');

        if ($this->checkHeader($header)){

            $oldPayments = Payment::where('fcode', '=',$fcode)->where('confirmed','=',0)->latest()->get();

            if (count($oldPayments) > 0){
                return response()->json([
                    'status' => true,
                    'message' => 'User Have Payments',
                    'payments' => $oldPayments
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'User Dont Have Payments'
                ],200);
            }

        }else{
            return response()->json([
                'status' => false,
                'message' => 'User Not Allowed To Access This Data'
            ]);
        }
    }

    public function getPaymentData($fcode){


        // Initialize Guzzle client
        $client = new Client();

        // Create a POST request
        $response = $client->request(
            'POST',
            'https://protected-gorge-62577.herokuapp.com/check-memdiff/',
            [
                'form_params' => [
                    'fcode' => $fcode
                ]
            ]
        );

        // Parse the response object, e.g. read the headers, body, etc.
        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();
        // data returned from api as json i need to decode it to send it again in json to be encoded just one time
        $payments = json_decode($body);


        if(count($payments) > 0){
            // if returned payments not empty
            return response()->json([
                'status'=>true,
                'message' => 'Payments Existed .. ',
                'payments' => $payments
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'message' => 'No Payments Existed .. '
            ],200);
        }

    }

    public function checkHeader($header){
        if ((count(Member::where('api_header', '=', $header)->get()) > 0) || (count(Employee::where('api_header', '=', $header)->get()) > 0) || (count(Student::where('api_header', '=', $header)->get()) > 0)){
            return true;
        }else{
            return false;
        }
    }

    public function CreateReturnPayment(Request $request, $total, $code, $details, $fcode)
    {
        Payment::create([
            'fcode' => $fcode,
            'range' => $request->input('range'),
            'total' => $total,
            'payment_code' => $code

        ]);

        return response()->json([
            "status" => true,
            "message" => "payment order created ..",
            "payments" => [
                "details" => $details,
                "code" => $code,
                "total" => $total
            ]
        ], 200);
    }

    public function memberPayments(Request $request){

        $member = Member::where('fcode', '=',$request->fcode)->first();
        if (count($member) > 0){
            $payments = $member->payments;
            return response()->json([
                'response' => true,
                'message' => 'Payments Listed Successfully..',
                'payments' => $payments
            ]);
        }else{
            return response()->json([
                'response' => false,
                'message' => 'User Not Found ..'
            ]);
        }
    }

    public function handlePayment()
    {
        $date = Carbon::now()->subDays(3);

        $oldPayments = Payment::where('created_at','<',$date)->where('confirmed','=',0)->latest()->get();
        if (count($oldPayments) > 0){
            foreach ($oldPayments as $oldPayment){
                $oldPayment->delete();
            }
        }
    }
}

