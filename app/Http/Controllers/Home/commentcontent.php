<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class CommentContent
{
    protected $database;
    protected $collection2;
    protected $collection3;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection3 = 'commentlist';
        $this->collection2 = 'commentcontent';
        $this->collection = 'users';
    }

    public function apiCommentContent(Request $request): JsonResponse
    {
        $idcomment = $request->input('idcomment');

        $query4 = $this->database->getReference($this->collection3 . '/' . $idcomment)->getValue();
        if (!$query4) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $count = $query4['count'] ?? 0;

        if ($count == 0) {
            return response()->json(['message' => 'nodata', 'Data' => 'no data'], 202);
        }

        // Lấy ra tất cả các idcomment
        $comments = [];
        for ($i = 1; $i <= $count; $i++) {
            if (isset($query4["idcomment{$i}"])) {
                $idcontent = $query4["idcomment{$i}"];
                $query3 = $this->database->getReference($this->collection2 . '/' . $idcontent)->getValue();

                // Kiểm tra nếu query3 trả về không phải là mảng
                if (!is_array($query3)) {
                    return response()->json(['message' => 'Invalid data format for comment content'], 500);
                }
       

                // Truy cập vào phần "Data" của query3
                $iduser = $query3['idofme'] ;
                $content = $query3['content'];
                $linkmedia = $query3['linkmedia'] ;
                $timecomment = $query3['timecomment'] ;
                $query2 = $this->database->getReference($this->collection . '/' . $iduser)->getValue();

                // Kiểm tra nếu query2 trả về không phải là mảng
                if (!is_array($query2)) {
                    return response()->json(['message' => 'Invalid data format for user profile'], 500);
                }

                $name = $query2['name'];
                $avatar = $query2['avatar'];

                $data = [
                    'nameuser' => $name,
                    'avatar' => $avatar,
                    'content' => $content,
                    'linkmedia' => $linkmedia,
                    'timecomment' => $timecomment
                ];
                $comments[] = $data;
            }
        }

        return response()->json(['message' => 'success', 'Data' => $comments], 200);
    }
}
