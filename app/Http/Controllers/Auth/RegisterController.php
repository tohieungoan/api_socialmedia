<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class RegisterController
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }

    public function apiRegister(Request $request): JsonResponse
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
            return response()->json(['message' => 'Email already exists'], 400);
        }
        else {
  // Create an array with the data received from the request
  $data = [
    'name' => $name,
    'avatar' => $avatar,
    'birthday' => $birthday,
    'sociallogin' => $sociallogin,
    'phone' => $phone,
    'keylistfriend' =>"null",
    'Loginsection' => false,
    'keylistrequest' => "null",
    'email' => $email,
    'password' => bcrypt($password),
];

// Store the data in Firestore
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
    $user['Loginsection'] = false;
    $this->database->getReference($this->collection . '/' . key($query))
        ->update($user);
    return response()->json(['message' => 'Create account success'], 200);
} else {
    return response()->json(['message' => 'Data storage failed'], 202);
}
         }
      
    }
}