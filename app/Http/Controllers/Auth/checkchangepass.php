<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Contract\Database;
use App\Mail\sendmail;
use Carbon\Carbon;
class checkchangepass
{
    protected $database;
    protected $collection;
    protected $collection2;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection2 = 'checkchangepass';
    }
    public function apichangepass(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $code = $request->input('otp');
        $query = $this->database->getReference($this->collection2)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();
        if ($query) {
            $user = current($query);
            $otpcheck = $user['code'];
            if (password_verify($code, $otpcheck)) {

                $currentTime = Carbon::now();
                $currentTime->setTimezone('Asia/Ho_Chi_Minh');
                $currentTimeFormatted = $currentTime->format('Y-m-d H:i:s');
                $mytime = Carbon::createFromFormat('Y-m-d H:i:s', $user['timecreate']);
                $diffInMinutes = $mytime->diffInMinutes($currentTimeFormatted);
                
                if ($diffInMinutes >15) {
                    $user['caIchangepass'] = false;
                    $this->database->getReference($this->collection2 . '/' . key($query))
                        ->update($user);
                    return response()->json(['message' => 'Đã vượt thời gian cho phép', 'time' => $diffInMinutes], 201);
                } else if ($diffInMinutes<15){
                    $user['caIchangepass'] = true;
                    $this->database->getReference($this->collection2 . '/' . key($query))
                        ->update($user);
                    return response()->json(['message' => 'Nhập mã hợp lệ', 'time' => $diffInMinutes], 200);
                }
                
                
            }
            else {
                return response()->json(['message' => 'Nhập mã sai' ],201);
                $user['caIchangepass'] = false;
                $this->database->getReference($this->collection2 . '/' . key($query))
                ->update($user);
            }


        }    
        else {
            return response()->json(['message' => 'Email not in Database'], 404);
        }
    }
    
}
