<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Contract\Database;
use App\Mail\sendmail;
use Carbon\Carbon;
class forgotpassword
{
    protected $database;
    protected $collection;
    protected $collection2;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
        $this->collection2 = 'checkchangepass';
    }
    public function apiforgotpassword(Request $request): JsonResponse
    {
        $email = $request->input('email');
    
        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();


    
        if ($query) {
            $user = current($query);
            $code = generateOTP();
            $mail = new \App\Mail\sendmail($code);
            Mail::to($email)->send($mail);
            $mytime = Carbon::now();
            $mytime->setTimezone('Asia/Ho_Chi_Minh');
            $datetime = $mytime->format('Y-m-d H:i:s');
            $query2 = $this->database->getReference($this->collection2)
            ->orderByChild('email')
            ->equalTo($email)
            ->getSnapshot();
        
        foreach ($query2->getValue() as $key => $value) {
            $this->database->getReference($this->collection2 . '/' . $key)->remove();
        }


                $data = [
            'code' => bcrypt($code),
            'email' => $email,
            'caIchangepass' => false,
            'timecreate' =>$datetime ,
        ];

// Store the data in Firestore
    $document = $this->database->getReference($this->collection2)->push($data);


            return response()->json(['message' => 'Send mail success' ,'code' => $code ],200);
        }    
        else {
            return response()->json(['message' => 'Email not in Database'], 401);
        }
    }
    
}
function generateOTP()
{
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    return $otp;
}