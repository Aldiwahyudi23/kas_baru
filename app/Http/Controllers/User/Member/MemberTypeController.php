<?php

namespace App\Http\Controllers\User\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberType;
use Illuminate\Http\Request;

class MemberTypeController extends Controller
{
    public function index()
    {
        $types = MemberType::all();
        return view('user.member.type.index', compact('types'));
    }

    public function create()
    {
        return view('user.member.type.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:member_types,name', // Validasi unik
            'description' => 'nullable|string',
        ]);

        MemberType::create($request->all());

        return redirect()->route('member-types.index')->with('success', 'Member type created successfully.');
    }

    public function edit(MemberType $memberType)
    {
        return view('user.member.type.edit', compact('memberType'));
    }

    public function update(Request $request, MemberType $memberType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:member_types,name,' . $memberType->id, // Validasi unik kecuali data yang sedang diubah
            'description' => 'nullable|string',
        ]);

        $memberType->update($request->all());

        return redirect()->route('member-types.index')->with('success', 'Member type updated successfully.');
    }

    public function destroy(MemberType $memberType)
    {
        $memberType->delete();

        return redirect()->route('member-types.index')->with('success', 'Member type deleted successfully.');
    }
}