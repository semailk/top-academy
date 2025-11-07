<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SearchController extends Controller
{
    public function searchAjax(Request $request)
    {
        if ($request->has('search') && $request->get('search') != '') {
            $users = User::query()
                ->with('image')
                ->where('name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('email', 'like', '%' . $request->get('search') . '%')
                ->get();

            return response()->json($users);
        } elseif ($request->has('search') && is_null($request->get('search'))) {
            $users = User::query()
                ->with('image.socset')
                ->get();

            foreach ($users as $user) {
                $user->image->socset;
            }
            return response()->json($users);
        }
        throw new BadRequestHttpException();
    }

    public function search()
    {
        return view('search');
    }
}

//public function search(Request $request)
//{
//    $name = 'r';
//    $email = 'e';
//    $query = User::query();
//    $fromDate = $request->get('from_date', Carbon::now()->addYears(-1)->toDateString());
//    $endDate = $request->get('end_date', Carbon::now()->toDateString());
//
//    $query->where('name', 'like', '%' . $name . '%');
//    $query->where('email', 'like', '%' . $email . '%');
//    $query->whereBetween('created_at', [$fromDate, $endDate]);
//
//    $phone = 5;
//    $query->where('phone', 'like' . '%' . $phone . '%');
//
//    $age = 22;
//    $query->where('age', '=', $age);
//
//    $lastName = 'b';
//    $query->where('last_name', '=', $lastName);
//
//    dd($query->toSql());
//    return $query->get();
//    return view('search');
//}
