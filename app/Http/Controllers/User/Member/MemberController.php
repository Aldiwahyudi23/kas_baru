<?php

namespace App\Http\Controllers\User\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberType;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with(['user', 'memberType'])->get();
        return view('user.member.akses.index', compact('members'));
    }
    public function create()
    {
        $users = User::doesntHave('member')->get(); // Hanya user yang belum menjadi member
        $memberTypes = MemberType::all();
        return view('user.member.akses.create', compact('users', 'memberTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:members,user_id',
            'member_type_id' => 'required|exists:member_types,id',
            'is_active' => 'required|boolean',
        ]);

        Member::create($request->all());

        return redirect()->route('members.index')->with('success', 'Member created successfully.');
    }

    public function edit(Member $member)
    {
        $users = User::all();
        $memberTypes = MemberType::all();
        return view('user.member.akses.edit', compact('member', 'users', 'memberTypes'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:members,user_id,' . $member->id,
            'member_type_id' => 'required|exists:member_types,id',
            'is_active' => 'required|boolean',
        ]);

        $member->update($request->all());

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    public function toggleActive($id)
    {
        $member = Member::findOrFail($id);
        $member->is_active = !$member->is_active;
        $member->save();

        return response()->json([
            'success' => true,
            'is_active' => $member->is_active
        ]);
    }
}