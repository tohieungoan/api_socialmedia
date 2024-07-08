<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Database;

class ListFriend extends Controller
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }

    public function apiListFriend(Request $request): JsonResponse
    {
        $email = $request->input('email');

        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();

        if ($query) {
            $userIds = array_keys($query);
            $userId = $userIds[0];
            $query3 = $this->database->getReference('ListFriend')
                ->orderByChild('idofme')
                ->equalTo($userId)
                ->getValue();

            if (is_array($query3) && !empty($query3)) {
                $dataArray = array_values($query3)[0];
                $idUserFriendArray = array_filter($dataArray, function ($key) {
                    return strpos($key, 'iduserfriend') === 0;
                }, ARRAY_FILTER_USE_KEY);

                $idUserFriendArray = array_values($idUserFriendArray);

                $snapshot = $this->database->getReference($this->collection)->getSnapshot();
                $data = $snapshot->getValue();

                $infoArray = [];
                foreach ($idUserFriendArray as $userId) {
                    $infoArray[] = [
                        'id' => $userId,
                        'name' => $data[$userId]['name'],
                        'avatar' => $data[$userId]['avatar'],
                    ];
                }

                return response()->json(['message' => 'get success', 'data' => $infoArray], 200);
            }
        }

        return response()->json(['message' => 'Email not in Database'], 401);
    }

    // else {
    //     $query3 = $this->database->getReference('ListFriend')
    //         ->orderByChild('idofme')
    //         ->equalTo($userId)
    //         ->getValue();

    //     if ($query3) {
    //         return response()->json(['message' => 'get success', 'data' => $query3], 200);
    //     }
}