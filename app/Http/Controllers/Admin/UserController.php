<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['orders', 'reviews', 'wishlists']);
        
        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by admin status
        if ($request->has('is_admin')) {
            $query->where('is_admin', $request->is_admin);
        }
        
        $users = $query->latest()->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }

    public function toggleAdmin(User $user)
    {
        // Prevent removing your own admin status
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mengubah status admin Anda sendiri!');
        }

        $user->update([
            'is_admin' => !$user->is_admin,
        ]);

        $message = $user->is_admin 
            ? 'User berhasil dijadikan admin!' 
            : 'Status admin berhasil dicabut!';

        return redirect()->back()->with('success', $message);
    }
}
