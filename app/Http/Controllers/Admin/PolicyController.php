<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index()
    {
        $policies = Policy::all();
        return view('admins.policies.index', compact('policies'));
    }

    public function create()
    {
        return view('admins.policies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
        'type' => 'required|string|in:terms,privacy',
        'content' => 'required|string',
        'effective_date' => 'required|date',
        ]);

        Policy::create($request->all());
        return redirect()->route('policies.index')->with('success', 'Политика добавлена');
    }

    public function edit(Policy $policy)
    {
        return view('admins.policies.edit', compact('policy'));
    }

    public function update(Request $request, Policy $policy)
    {
        $request->validate([
        'content' => 'required|string',
        'effective_date' => 'required|date',
        ]);

        $policy->update($request->all());
        return redirect()->route('policies.index')->with('success', 'Политика обновлена');
    }

    public function destroy(Policy $policy)
    {
        $policy->delete();
        return redirect()->route('policies.index')->with('success', 'Политика удалена');
    }
}