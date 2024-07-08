<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Hash;

class acceptOrdelete
{
    protected $database;
    protected $collection;
    protected $collection2;
    protected $collection3;
    protected $collection4;
    protected $userId;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
        $this->collection2 = 'ListRequest';
        $this->collection3 = 'ListFriend';
        $this->collection4 = 'chatlist';
    }

    public function apiacceptOrdelete(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $idfriend = $request->input('friends');
        $action = $request->input('action');

        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();
            // return response()->json(['message' => 'success', 'Data' => $query], 200);

        if ($query) {
            $userIds = array_keys($query);
            $userId = $userIds[0];

            $query4 = $this->database->getReference($this->collection2)
                ->orderByChild('idofme')
                ->equalTo($userId)
                ->getValue();

            if ($query4) {
                $user3 = current($query4);
                $numberofFriend = $user3['numberofRequest'];

                if ($action == "accept") {
                   
                    for ($i = 0; $i < $numberofFriend; $i++) {
                        if ($user3["iduserrequest$i"] == $idfriend) {
                            $this->updateRequestIdToEmpty($this->collection2, 'idofme', $userId, $i);
                            $this->addfriend($this->collection3, 'idofme',$userId, $idfriend);
                            $this->addfriend($this->collection3, 'idofme',$idfriend, $userId);
                      
                                    $ids = [$userId, $idfriend];
                                    sort($ids);
                            
                                    $concatenatedIds = $ids[0] . $ids[1];
                               
                            
                                    $newId = hash('sha256', $concatenatedIds);
                                    $data = [
                                        "idwechat" =>$newId,
                                        "count" => 0,
                                        "lastmessage" => "No message , Say hi to my friend!"
                                        
                                    ];
                            $document = $this->database->getReference($this->collection4)->push($data);
                            
                            break;
                        }
                    }
                    
                      
                    
                } else if ($action == "delete") {
                    for ($i = 0; $i < $numberofFriend; $i++) {
                        if ($user3["iduserrequest$i"] == $idfriend) {
                            $this->updateRequestIdToEmpty($this->collection2, 'idofme', $userId, $i);
                            break;
                        }
                    }
                }
            }

            return response()->json(['message' => 'success', 'Data' => $query4], 200);
        }

        return response()->json(['message' => 'Email not in Database'], 401);
    }
    private function updateRequestIdToEmpty($collection, $childKey, $parentId, $index)
    {
        $query = $this->database->getReference($collection)
            ->orderByChild($childKey)
            ->equalTo($parentId)
            ->getValue();
          
        if ($query) {
            $existingKey = key($query);
            $data = [
                
                'iduserrequest'.$index => ''
            ];
            $this->database
            ->getReference($this->collection2 . '/' . $existingKey)
            ->update($data);
        }
    }
    

    private function addfriend($collection, $childKey, $parentId,$idfriend)
    {
        $query = $this->database->getReference($collection)
            ->orderByChild($childKey)
            ->equalTo($parentId)
            ->getValue();
    
        if ($query) {
            $user4 = current($query);
            $numberFriend = $user4['numberofFriend'];
            // Update the iduserrequest to an empty value
            $numberFriend = $numberFriend + 1;
        $data = [
            "iduserfriend$numberFriend" => $idfriend,
            'numberofFriend' => $numberFriend
        ];
        $existingKey = key($query);
        $this->database
            ->getReference($this->collection3 . '/' . $existingKey)
            ->update($data);
        }
    }
 
    
}