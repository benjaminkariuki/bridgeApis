<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class dummyApi extends Controller
{
    //
    function getData(){
        return ['name'=>'anil','email'=>'anil@test.com', 'address'=>'delhi'];

    }


}
