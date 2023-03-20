<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::latest()->paginate(5);
    
        return view('users.index',compact('users'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    public function create()
    {
        return view('users.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'image' => 'required',
            // 'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        
        
        $input = $request->all();
        $input['password']=Hash::make($request->password);
  
        if ($image = $request->file('image')) {
            $destinationPath = 'image/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['image'] = "$profileImage";
        }

        User::create($input);
     
        return redirect()->route('users.index')
                        ->with('success','User created successfully.');
    }
    
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }
    
    public function edit(User $user)
    {
        return view('users.edit',compact('user'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => Hash::make($request['password']),
            'image' => 'required',
        ]);

        $input = $request->all();
        $input['password']=Hash::make($request->password);
  
        if ($image = $request->file('image')) {
            $destinationPath = 'image/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['image'] = "$profileImage";
        }else{
            unset($input['image']);
        }
        
        $user->update($input);
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }
    
    public function destroy(User $user)
    {
        $user->delete();
    
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }
}
