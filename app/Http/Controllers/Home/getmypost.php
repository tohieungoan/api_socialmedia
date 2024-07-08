<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class getmypost
{
    protected $database;
    protected $collection;
    protected $collection2;
    protected $collection3;
    protected $collection4;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
        $this->collection2 = 'ListFriend';
        $this->collection3 = 'postcontent';
        $this->collection4 = 'commentlist';
    }

    public function apigetmypost(Request $request): JsonResponse
    {
        $email = $request->input('email');

        // Query the user based on email
        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();

        if ($query) {
            $userIds = array_keys($query);
            $userId = $userIds[0]; // Assuming there's only one user per email

            $infoArray = [];

            // Query posts of the user
            $userPosts = $this->database->getReference($this->collection3)
                ->orderByChild('idofme')
                ->equalTo($userId)
                ->getValue();

            if ($userPosts) {
                foreach ($userPosts as $postId => $postData) {
                    // Retrieve comments count
                    $comments = $this->database->getReference($this->collection4 . '/' . $postData['idcomment'])
                        ->getValue();
                    $commentCount = isset($comments['count']) ? $comments['count'] : 0;

                    // Retrieve user info of the post creator
                    $userinfo = $this->database->getReference($this->collection . '/' . $userId)->getValue();

                    // Format time
                    $timeCreate = $postData['timecreate'] ?? '';
                    $relativeTime = Carbon::parse($timeCreate, 'Asia/Ho_Chi_Minh')->diffForHumans();

                    // Build post data array
                    $infoArray[] = [
                        'id' => $postId,
                        'nameuser' => $userinfo['name'] ?? '',
                        'avatar' => $userinfo['avatar'] ?? '',
                        'content' => $postData['content'] ?? '',
                        'commentcount' => $commentCount,
                        'idcomment' => $postData['idcomment'] ?? '',
                        'like' => $postData['like'] ?? '',
                        'linkmedia' => $postData['linkmedia'] ?? '',
                        'timecreate' => $relativeTime,
                    ];
                }
            }

            return response()->json(['message' => 'get success', 'data' => $infoArray], 200);
        }

        return response()->json(['message' => 'Email not found in Database'], 404);
    }
}
