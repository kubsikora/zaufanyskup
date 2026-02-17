<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', $request->email . '%');
        }

        $perPage = $request->get('per_page', 5);
        $page = $request->get('page', 1);

        $products = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $products->items(),
            'total' => $products->total(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'newpassword' => 'required'
        ]);
        $data = $request->all();

        $data['password'] = bcrypt($data['newpassword']);
        User::create($data);
    }
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'id' => 'required',
        ]);
        $data = $request->all();

        if (isset($data['newpassword'])) {
            $data['password'] = bcrypt($data['newpassword']);
            unset($data['newpassword']);
        }
        unset($data['created_at']);
        unset($data['updated_at']);
        User::where('id', $data['id'])->update($data);
    }

    public function changepassword(Request $request)
    {
        $request->validate([
            'newpassword' => 'required|min:9',
            'newpassword2' => 'required|same:newpassword',
        ]);
        $data = $request->all();
        User::where('id', auth()->id())->update(['password'=>bcrypt($data['newpassword'])]);
    }
    public function destroy(Request $request)
    {
        $data = $request->all();

        User::where('id', $data['id'])->delete();
    }

}
