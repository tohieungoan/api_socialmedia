<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class getlistrequest
{
    protected $database;
    protected $collection;
    protected $collection2;
    protected $userId;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
        $this->collection2 = 'ListRequest';
    }

    public function apigetlistrequest(Request $request): JsonResponse
    {
        $email = $request->input('email');

        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();

        if ($query) {
            $userIds = array_keys($query);
            $userId = $userIds[0];

            $query4 = $this->database->getReference($this->collection2)
                ->orderByChild('idofme')
                ->equalTo($userId)
                ->getValue();

            if ($query4) {
                $dataArray2 = array_values($query4)[0];
                $idUserFriendArray2 = array_filter($dataArray2, function ($key) {
                    return strpos($key, 'iduserrequest') === 0;
                }, ARRAY_FILTER_USE_KEY);

                $infoArray = [];
                $arraycheck = [];
                $query3 = $this->database->getReference('ListFriend')
                    ->orderByChild('idofme')
                    ->equalTo($userId)
                    ->getValue();
                
                $numberOfFriend = 0;
                
                if ($query3) {
                    foreach ($query3 as $key => $value) {
                        $numberOfFriend = $value['numberofFriend']; 
                        for ($i = 1; $i <= $numberOfFriend; $i++) {
                            $arraycheck[] = $value["iduserfriend$i"];
                            
                        }
                    }
                
                } 
      
                foreach ($idUserFriendArray2 as $userIdFriend) {
                    if ($userIdFriend !=""){
                        if(in_array($userIdFriend,$arraycheck)){

                        }
                        else {
                            $arraycheck[] = $userIdFriend;
                            $userData = $this->database->getReference($this->collection . '/' . $userIdFriend)->getValue();
                            $infoArray[] = [
                                'id' => $userIdFriend,
                                'name' => $userData['name'],
                                'avatar' => $userData['avatar'],
                            ];
                        }


                    }
               
                }

                return response()->json(['message' => 'get success', 'data' => $infoArray], 200);
            }
        }

        return response()->json(['message' => 'Email not in Database'], 401);
    }
}