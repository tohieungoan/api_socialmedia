<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Kreait\Firebase\Contract\Database;

class postcontent
{
    protected $database;
    protected $usersCollection;
    protected $postsCollection;
    protected $commentsCollection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->usersCollection = 'users';
        $this->postsCollection = 'postcontent';
        $this->commentsCollection = 'commentlist';
    }

    public function apipostcontent(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $linkmedia = $request->input('media');
        $content = $request->input('content');

        $query = $this->database->getReference($this->usersCollection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();

        if ($query) {
            $userIds = array_keys($query);
            $userId = $userIds[0];
            $datetime = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
            if($linkmedia==""){
                $linkmedia = 'null';
            }
            $data = [
                'content' => $content,
                'idcomment' => null,
                'idofme' => $userId,
                'like' => 0,
                'linkmedia' => $linkmedia,
                'timecreate' => $datetime,
            ];

            try {
                // Store the post data in Firebase
                $postRef = $this->database->getReference($this->postsCollection)->push($data);
                $postId = $postRef->getKey();

                $commentData = [
                    'count'=>0
                ];

                // Store the comment data in Firebase
                $commentRef = $this->database->getReference($this->commentsCollection)->push($commentData);
                $commentId = $commentRef->getKey();

                // Update the post with the comment ID
                $this->database->getReference("{$this->postsCollection}/{$postId}")->update([
                    'idcomment' => $commentId,
                ]);

                return response()->json(['message' => 'Success'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'An error occurred while posting content', 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Email not in Database'], 401);
    }
}
