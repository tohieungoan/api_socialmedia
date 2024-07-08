<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Kreait\Firebase\Contract\Database;

class postcomment
{
    protected $database;
    protected $usersCollection;
    protected $commentlistCollection;
    protected $commentsCollection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->usersCollection = 'users';
        $this->commentlistCollection = 'commentlist';
        $this->commentsCollection = 'commentcontent';
    }

    public function apipostcomment(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $linkmedia = $request->input('media');
        $content = $request->input('content');
        $idcomment = $request->input('idcomment');
    
        $query = $this->database->getReference($this->usersCollection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();
    
        if ($query) {
            $userIds = array_keys($query);
            $userId = $userIds[0];
            $datetime = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
            if ($linkmedia == "") {
                $linkmedia = 'null';
            }
            $data = [
                'content' => $content,
                'idofme' => $userId,
                'linkmedia' => $linkmedia,
                'timecomment' => $datetime,
            ];
    
            try {
                // Store the post data in Firebase
                $postRef = $this->database->getReference($this->commentsCollection)->push($data);
                $postId = $postRef->getKey();
                $query4 = $this->database->getReference($this->commentlistCollection . '/' . $idcomment)->getValue();
    
                if (isset($query4['count'])) {
                    $count = $query4['count'];
                    $count++;
                    $this->database->getReference("{$this->commentlistCollection}/{$idcomment}")->update([
                        'count' => $count,
                        "idcomment$count" => $postId
                    ]);
    
                    return response()->json(['message' => 'Success'], 200);
                } else {
                    return response()->json(['message' => 'Count not found in comment list data'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'An error occurred while posting content', 'error' => $e->getMessage()], 500);
            }
        }
    
        return response()->json(['message' => 'Email not in Database'], 401);
    }
    
}