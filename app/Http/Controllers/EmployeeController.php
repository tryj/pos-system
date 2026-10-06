<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::latest()->get();
        return view('employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,cashier',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('employees.index')->with('success', 'เพิ่มพนักงานสำเร็จ');
    }

    public function destroy(User $employee)
    {
        if ($employee->id === auth()->id()) {
            return redirect()->route('employees.index')->with('error', 'ไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้');
        }
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'ลบพนักงานเรียบร้อย');
    }
}