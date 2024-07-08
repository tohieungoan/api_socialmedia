<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
class getlistpost
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
        $this->collection2 = 'ListFriend';
        $this->collection3 = 'postcontent';
        $this->collection4 = 'commentlist';
    }

    public function apigetlistpost(Request $request): JsonResponse
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
                    return strpos($key, 'iduserfriend') === 0;
                }, ARRAY_FILTER_USE_KEY);

                $infoArray = [];
                foreach ($idUserFriendArray2 as $userIdFriend) {
                    if ($userIdFriend != "") {
                        $userPosts = $this->database->getReference($this->collection3)
                            ->orderByChild('idofme')
                            ->equalTo($userIdFriend)
                            ->getValue();

                        if ($userPosts) {
                            foreach ($userPosts as $postId => $postData) {
                                
                                $comments = $this->database->getReference($this->collection4 . '/'.$postData['idcomment'])
                                ->getValue();
                                $commentCount = $comments['count'];
                            
                            // Extract count from comments
                          
                            $timeCreate = $postData['timecreate'] ?? '';
                            $relativeTime = Carbon::parse($timeCreate, 'Asia/Ho_Chi_Minh')->diffForHumans();

                                $userinfo = $this->database->getReference($this->collection . '/' . $userIdFriend)->getValue();
                                $infoArray[] = [
                                    'id' => $postId,
                                    'nameuser' =>$userinfo['name'],
                                    'avatar' =>$userinfo['avatar'],
                                    'content' => $postData['content'],
                                    'commentcount'=>$commentCount,
                                    'idcomment' => $postData['idcomment'],
                                    'like' => $postData['like'],
                                    'linkmedia' => $postData['linkmedia'],
                                    'timecreate' => $relativeTime,
                                ];
                            }
                        }
                    }
                }

                return response()->json(['message' => 'get success', 'data' => $infoArray], 200);
            }
        }

        return response()->json(['message' => 'Email not in Database'], 401);
    }
}
