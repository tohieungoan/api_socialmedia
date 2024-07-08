<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Database;

class SuggestFriend extends Controller
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }

    public function apiSuggestFriend(Request $request): JsonResponse
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

     

                $query4 = $this->database->getReference('ListRequest')
                ->orderByChild('idofme')
                ->equalTo($userId)
                ->getValue();
            if (is_array($query3) && !empty($query3)) {
                $dataArray = array_values($query3)[0];
                $idUserFriendArray = array_filter($dataArray, function ($key) {
                    return strpos($key, 'iduserfriend') === 0;
                }, ARRAY_FILTER_USE_KEY);

                $dataArray2 = array_values($query4)[0];
                $idUserFriendArray2 = array_filter($dataArray2, function ($key) {
                    return strpos($key, 'iduserrequest') === 0;
                }, ARRAY_FILTER_USE_KEY);

                $idUserFriendArray = array_values($idUserFriendArray);
                $idUserFriendArray2 = array_values($idUserFriendArray2);

                $snapshot = $this->database->getReference($this->collection)->getSnapshot();
                $data = $snapshot->getValue();

                $filteredIds = array_filter(array_keys($data), function ($id) use ($userId) {
                    return $id !== $userId;
                });

                $filteredIds = array_values($filteredIds);
                $uniqueList = array_diff($filteredIds, $idUserFriendArray);
                $finalList = array_diff($uniqueList, $idUserFriendArray2);

                $infoArray = [];
                foreach ($finalList as $userId) {
                    if ($userId !== 'Loginsection') {
                        $infoArray[] = [
                            'id' => $userId,
                            'name' => $data[$userId]['name'],
                            'avatar' => $data[$userId]['avatar'],
                        ];
                    }
                }
                return response()->json(['message' => 'get success', 'data' => $infoArray], 200);
            }
        }

        return response()->json(['message' => 'Email not found in '], 404);
    }
}