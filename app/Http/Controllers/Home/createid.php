<?php

namespace App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class createid 
{
    public function apicreateid(Request $request)
    {

$id2 = "-NzP3fTMuoXrsCDYleRA";
$id1 = "-NzP3ahYcAkFnIQDuQif";
        $ids = [$id1, $id2];
        sort($ids);

        $concatenatedIds = $ids[0] . $ids[1];

        $newId = hash('sha256', $concatenatedIds);

        return response()->json([
            'unique_id' => $newId,
        ]);
    }
}
