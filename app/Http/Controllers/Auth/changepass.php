<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Contract\Database;
use App\Mail\sendmail;
use Carbon\Carbon;
class changepass
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
    public function changepass(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $password =  $request->input('password');
    
        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();
        
            $query2 = $this->database->getReference($this->collection2)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();


    
        if ($query) {
            $user2 = current($query2);
            $check = $user2['caIchangepass'];
            if($check==true){
                $user = current($query);
                $user['password'] = bcrypt($password);
                $this->database->getReference($this->collection . '/' . key($query))
                ->update($user);

                $query3 = $this->database->getReference($this->collection2)
    ->orderByChild('email')
    ->equalTo($email)
    ->getSnapshot();
                foreach ($query3->getValue() as $key => $value) {
                    $this->database->getReference($this->collection2 . '/' . $key)->remove();
                }

                return response()->json(['message' => 'change password success'], 200);
            }
            else {
                return response()->json(['message' => 'You need change password again with new otp'], 201);
            }

        }
        else {
            return response()->json(['message' => 'change password failed , maybe your account not in db'], 404);
        }
    }
    
}
