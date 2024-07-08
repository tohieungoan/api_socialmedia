<?php

namespace App\Http\Controllers\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
class RequestFriend
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
    public function apiRequestFriend(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $idfriends = $request->input('friends');
        $action = $request->input('action');
        if($action=='add'){


            $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();
                if ($query) {
                    $userIds = array_keys($query);
                    $userId = $userIds[0];
             
                        $query3 = $this->database->getReference($this->collection2)
                        ->orderByChild('idofme')
                        ->equalTo($idfriends)
                        ->getValue();
                    
                        if ($query3) {
                            $user3 = current($query3);
                            $numberofFriend = $user3['numberofRequest'];
                            $idfriendExists = false;
                        
                            for ($i = 0; $i < $numberofFriend; $i++) {
                                if ($user3["iduserrequest$i"] == $idfriends) {
                                    $idfriendExists = true;
                                    break;
                                }
                            }
                        
                            if ($idfriendExists) {
                                return response()->json(['message' => 'Failed, we are already friends'], 200);
                            } else {
                                $number =$numberofFriend;
        
                                $numberofFriend = $numberofFriend + 1;
                                $data = [
                                    "iduserrequest$number" => $userId,
                                    'numberofRequest' => $numberofFriend
                                ];
                        
                                $existingKey = key($query3);
                                $this->database
                                    ->getReference($this->collection2 . '/' . $existingKey)
                                    ->update($data);
                        
                                return response()->json(['message' => 'Success', 'id friend' => $idfriends], 200);
                            }
                        } else {
                            // Handle the case when $query3 is empty or null
                            return response()->json(['message' => 'No matching records found'], 404);
                        }
                    
        
        
        
                
        
                }
            
                return response()->json(['message' => 'Email not in Database'], 401);
        }
        else if($action=="delete"){
            
        }

    }
}