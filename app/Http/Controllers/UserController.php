<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        if ($search) {
            $users = User::where('id', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('role', 'like', "%$search%")
                ->orWhere('joining_date', 'like', "%$search%")
                ->paginate(8);
        } else {
            $users = User::paginate(8);
        }

        return view('accounts.index', compact('users'));
    }

    public function create(): View
    {
        return view('accounts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required',
            'joining_date' => 'required|date',
        ]);

        $success =  User::create($request->all());

        if (!$success) {
            return redirect()->route('accounts.index')->with('success', 'User add successfully.');
        }
        else{
            return redirect()->route('accounts.create')->withErrors('User failed to add.');
        }
    }

    public function edit(User $user): View
    {
        return view('accounts.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required',
            'role' => 'required',
        ]);

        $success = $user->update($request->all());

        if ($success) {
            return redirect()->route('accounts.index')->with('success', 'User updated successfully.');
        } else {
            return redirect()->route('accounts.edit', $user->id)->withErrors('User failed to update.');
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('accounts.index')->with('success', 'User deleted successfully.');
    }

    
    public function showProfile(User $user)
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function editProfile(User $user): View
    {
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required',
        ]);

        $success = $user->update($request->all());

        if ($success) {
            return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
        } else {
            return redirect()->route('profile.edit', $user->id)->withErrors('Profile failed to update.');
        }
    }
    
}
