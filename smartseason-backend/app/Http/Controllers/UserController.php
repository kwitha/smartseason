<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    //get/api/agents ,returns all field agents,used by admin when assigning agents to fields
    public function agents():JsonResponse
    {
        $agents=User::where('role','agent')
               ->select('id','name','email')
               ->orderBy('name')
               ->get();

               return response()->json($agents);
    }
}
