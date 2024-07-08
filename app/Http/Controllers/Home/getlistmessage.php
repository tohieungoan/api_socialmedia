<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Database;

class getlistmessage extends Controller
{
    protected $database;
    protected $collection;
    protected $collection2;
    protected $collection3;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
        $this->collection2 = 'chatlist';
        $this->collection3 = 'contentchat';
    }

    public function apigetlistmessage(Request $request): JsonResponse
    {
        $idfriend = $request->input('idfriend');
        $userId = $request->input('idofme');
        $idwechat = $request->input('idwechat');
        $myname = $this->database->getReference($this->collection . '/' .$userId)
        ->getValue();
        $nameofme= $myname['name'] ?? null;
        // Fetch chatlist where idwechat matches the provided idwechat
        $chatlist = $this->database->getReference($this->collection2)
            ->orderByChild('idwechat')
            ->equalTo($idwechat)
            ->getValue();

        if ($chatlist) {
            $chatKey = null;
            $count = 0;
            foreach ($chatlist as $key => $value) {
                if (isset($value['count'])) {
                    $count = $value['count'];
                    $chatKey = $key;
                    break;
                }
            }

            $query4 = $this->database->getReference($this->collection . '/' . $idfriend)->getValue();
            $avatar = $query4['avatar'] ?? null;
            $name = $query4['name'] ?? null;

            if ($count == 0) {
                $data = [
                    'avatar' => $avatar,
                    'name' => $name,
                    'chatkey' =>$chatKey,
                    'myname' => $nameofme


                ];
                return response()->json(['message' => 'Get success, no data', 'data' => $data], 202);
            } else if ($count > 0) {
                $messages = [];
                for ($i = 1; $i <= $count; $i++) {
                    if (isset($chatlist[$chatKey]["idcontent{$i}"])) {
                        $idcontent = $chatlist[$chatKey]["idcontent{$i}"];
                        $query3 = $this->database->getReference($this->collection3 . '/' . $idcontent)->getValue();
                        if (!is_array($query3)) {
                            return response()->json(['message' => 'Invalid data format for message content'], 500);
                        }
                        $idmechat = $query3['idofme'];
                        $contentchat = $query3['contentchat'];
                        $linkmedia = $query3['medialink'];
                        $timecreate = $query3['timecreate'];
                        
                        
                        $data = [
                            'content' => $contentchat,
                            'linkmedia' => $linkmedia,
                            'timecreate' => $timecreate,
                            'idofme' => $idmechat,
                        ];
                        $messages[] = $data;
                    }
                }
                $data2 = [
                    'avatar' => $avatar,
                    'name' => $name,
                    'chatkey' =>$chatKey,
                    'myname' => $nameofme

                ];

                return response()->json([
                    'message' => 'Get success',
                    'messages' => $messages, // Danh sách tin nhắn
                    'data' => $data2 // Dữ liệu khác
                ], 200);
                
            }
        }

        // If no count is found, return a default response
        return response()->json(['message' => 'No count found', 'data' => 0], 200);
    }
}
