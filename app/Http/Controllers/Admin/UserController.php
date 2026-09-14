<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('pegawai');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function ($sub) use ($search) {
                      $sub->where('nama', 'like', "%{$search}%")
                          ->orWhere('nrg', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:aktif,nonaktif'],
            'role' => ['required', 'in:admin,staff'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        // Prevent self-deactivation or self-demotion
        if ($user->id === Auth::id() && ($request->status === 'nonaktif' || $request->role !== 'admin')) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan atau mengubah role akun Anda sendiri saat sedang login.');
        }

        $data = [
            'status' => $request->status,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', "Akun pengguna {$user->username} berhasil diperbarui.");
    }
}
