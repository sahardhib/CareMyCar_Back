<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class APIcontroller extends Controller
{
    public  function getUsers() {

        $getUsers  = User::get() ;
        /*return $getUsers;*/
        return response()->json(["user"=>$getUsers],200);
      }
     }

