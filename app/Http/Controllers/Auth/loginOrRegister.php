<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
class LoginOrRegister
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }

    public function apiRegisterOrLogin(Request $request): JsonResponse
    {
        $name = $request->input('name');
        $avatar = $request->input('avatar');
        $birthday = $request->input('birthday');
        $sociallogin = $request->input('sociallogin');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $password = $request->input('password');

        $query = $this->database->getReference($this->collection)->orderByChild('email')->equalTo($email)->getValue();

        if ($query) {
            $user = current($query);
            $phone = $user['phone'];
            $birthday = $user['birthday'];
            
            if($phone=="." || $birthday=="."){
                $user['Loginsection'] = false; 
                $this->database->getReference($this->collection . '/' . key($query))
                    ->update($user);
                return response()->json(['message' => 'login success in first time'], 200);
            }

            {
                $user['Loginsection'] = true;
                $this->database->getReference($this->collection . '/' . key($query))
                    ->update($user);
                return response()->json(['message' => 'Login success'], 400);

            }
        }
        else {
  // Create an array with the data received from the request
  $data = [
    'name' => $name,
    'avatar' => $avatar,
    'birthday' => $birthday,
    'sociallogin' => $sociallogin,
    'phone' => $phone,
    'Loginsection' => false,
    'keylistfriend' =>"null",
    'keylistrequest' => "null",
    'email' => $email,
    'password' => bcrypt($password),
];

$document = $this->database->getReference($this->collection)->push($data);


$data1 = [
    'idofme' => $document->getKey(),
    'numberofFriend' => 0
];
$document1 = $this->database->getReference("ListFriend")->push($data1);

$data2 = [
    'idofme' => $document->getKey(),
    'numberofRequest' => 0
];
$document2 = $this->database->getReference("ListRequest")->push($data2);

$this->database->getReference($this->collection . '/' . $document->getKey())->update([
    'keylistfriend' => $document1->getKey(),
    'keylistrequest' => $document2->getKey(),
]);


if ($document&&$document1&&$document2) {
    // return new JsonResponse($document, 200);
    $user['Loginsection'] = false;
    $this->database->getReference($this->collection . '/' . key($query))
        ->update($user);
    return response()->json(['message' => 'login success in first time'], 200);
} else {
    return response()->json(['message' => 'Data storage failed'], 202);
}
         }
      
    }
}