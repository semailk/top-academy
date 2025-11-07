<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
        public function test(Request $request)
        {
            $imageHas = $request->has('imageHas');
            $query = User::query();

            if ($imageHas) {
                if ($request->get('imageHas') == 'image_has') {
                    $query->has('image');
                }else{
                    $query->doesntHave('image');
                }
            }
            dd($query->get());
        }
}
