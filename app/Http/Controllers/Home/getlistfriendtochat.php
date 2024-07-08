<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Database;

class GetListFriendToChat extends Controller
{
    protected $database;
    protected $collection;
    protected $collection2;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
        $this->collection2 = 'chatlist';
    }

    public function apigetlistfriendtochat(Request $request): JsonResponse
    {
        $email = $request->input('email');

        $userSnapshot = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();

        if ($userSnapshot) {
            $userId = array_key_first($userSnapshot);
            $friendsSnapshot = $this->database->getReference('ListFriend')
                ->orderByChild('idofme')
                ->equalTo($userId)
                ->getValue();

            if (is_array($friendsSnapshot) && !empty($friendsSnapshot)) {
                $friendData = reset($friendsSnapshot);
                $friendIds = array_values(array_filter($friendData, function ($key) {
                    return strpos($key, 'iduserfriend') === 0;
                }, ARRAY_FILTER_USE_KEY));

                $userListSnapshot = $this->database->getReference($this->collection)->getSnapshot();
                $allUsers = $userListSnapshot->getValue();

                $chatInfo = [];
                foreach ($friendIds as $friendId) {
                    $sortedIds = [$userId, $friendId];
                    sort($sortedIds);
                    $concatenatedIds = implode('', $sortedIds);
                    $chatId = hash('sha256', $concatenatedIds);

                    $chatSnapshot = $this->database->getReference($this->collection2)
                        ->orderByChild('idwechat')
                        ->equalTo($chatId)
                        ->getValue();

                    if ($chatSnapshot) {
                        $chatData = reset($chatSnapshot);
                        $lastMessage = $chatData['lastmessage'] ?? 'No messages yet';
                        $friendInfo = $allUsers[$friendId] ?? [];

                        $chatInfo[] = [
                            'id' => $friendId,
                            'idofme'=>$userId,
                            'name' => $friendInfo['name'] ?? 'Unknown',
                            'avatar' => $friendInfo['avatar'] ?? 'null',
                            'idwechat' => $chatId,
                            'lastmessage' => $lastMessage
                        ];
                    }
                }

                return response()->json(['message' => 'Get success', 'data' => $chatInfo], 200);
            } else {
                return response()->json(['message' => 'No friends found for this user'], 404);
            }
        }

        return response()->json(['message' => 'Email not found in database'], 404);
    }
}
