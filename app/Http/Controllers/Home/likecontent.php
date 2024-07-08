<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Kreait\Firebase\Contract\Database;

class likecontent
{
    protected $database;
    protected $postsCollection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->postsCollection = 'postcontent';
    }

    public function apilikecontent(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $postData = $this->database->getReference($this->postsCollection . '/' . $id)->getValue();

        if ($postData) {
            $count = $postData['like'] ?? 0;
            $count++;
            $this->database->getReference($this->postsCollection . '/' . $id . '/like')->set($count);
            $postData['like'] = $count;
            return response()->json(['message' => 'ok', 'data' => $postData], 200);
        }

        return response()->json(['message' => 'Content not found'], 404);
    }
}
