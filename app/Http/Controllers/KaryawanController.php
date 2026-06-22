<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    /**
     * Display a listing of employees and their attendance.
     */
    public function index(Request $request)
    {
        $employees = User::where('role', '!=', 'pemilik')
            ->orderBy('username')
            ->get();

        $attendances = Attendance::with('user')
            ->latest('login_at')
            ->paginate(15, ['*'], 'page_absen')
            ->withQueryString();

        return view('karyawan.index', compact('employees', 'attendances'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:4',
            'role'     => 'required|in:kasir',
        ]);

        $employee = User::create([
            'username'      => $request->username,
            'password_hash' => Hash::make($request->password),
            'role'          => $request->role,
        ]);

        ActivityLog::create([
            'user_id'       => auth()->id(),
            'activity_type' => 'product_add', // or custom type
            'description'   => 'Menambahkan karyawan baru: ' . $employee->username . ' (' . ucfirst($employee->role) . ')',
        ]);

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan ' . $employee->username . ' berhasil ditambahkan.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy($id)
    {
        $employee = User::findOrFail($id);

        if ($employee->role === 'pemilik') {
            return redirect()->route('karyawan.index')
                ->with('error', 'Tidak dapat menghapus akun pemilik.');
        }

        $username = $employee->username;
        $role = $employee->role;

        $employee->delete();

        ActivityLog::create([
            'user_id'       => auth()->id(),
            'activity_type' => 'product_deactivate', // or custom type
            'description'   => 'Menghapus karyawan: ' . $username . ' (' . ucfirst($role) . ')',
        ]);

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan ' . $username . ' berhasil dihapus.');
    }
}
