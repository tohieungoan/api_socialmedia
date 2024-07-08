<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;

class sendmessage extends Controller
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

    public function apisendmessage(Request $request): JsonResponse
    {
        $message = $request->input('message');
        $media = $request->input('media');
        $idfriend = $request->input('idfriend');
        $userId = $request->input('idofme');
        $idwechat = $request->input('idwechat');

        // Fetch chatlist where idwechat matches the provided idwechat
        $chatlist = $this->database->getReference($this->collection2)
            ->orderByChild('idwechat')
            ->equalTo($idwechat)
            ->getValue();

        if ($chatlist) {
            $count = 0;
            
            $chatlistKey =key($chatlist);
            foreach ($chatlist as $key => $value) {
                if (isset($value['count'])) {
                    $count = $value['count'];
                   
                    break;
                }
            }

            if ($chatlistKey) {
                $datetime = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
if($message==null){
    $message = "image";
}
                $data = [
                    'contentchat' => $message,
                    'idofme' => $userId,
                    'medialink' => $media,
                    'timecreate' => $datetime,
                ];

                // Store the data in Firebase Realtime Database
                $document = $this->database->getReference($this->collection3)->push($data);
                $keycontent = $document->getKey();

                $count++;
                $datasend = [
                    "idcontent$count" => $keycontent,
                    "count" => $count,
                    "lastmessage" => $message
                ];

                $this->database
                    ->getReference($this->collection2 . '/' . $chatlistKey)
                    ->update($datasend);

                return response()->json(['message' => 'Message sent successfully', 'data' => $datasend], 200);
            }
        }

        // If no count is found or chatlistKey is not set, return a default response
        return response()->json(['message' => 'No count found or invalid chat list', 'data' => 0], 404);
    }
}
