<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Server::query();
        
        if (!$user->isSuperAdmin()) {
            $query->whereHas('assignedUsers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        if ($user->isSuperAdmin()) {
            $query->with(['portals']);
        } else {
            $query->with(['portals' => function($q) use ($user) {
                $q->whereHas('assignedUsers', function($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                });
            }]);
        }
        
        if ($request->filled('search')) {
            $query->where('ip', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        
        if ($request->filled('exposure')) {
            $query->where('exposed', 'like', '%' . $request->exposure . '%');
        }

        $servers = $query->paginate(15);
        
        
        $exposureOptions = Server::whereNotNull('exposed')
                                ->where('exposed', '!=', '')
                                ->distinct()
                                ->pluck('exposed')
                                ->sort()
                                ->values();
        
        return view('servers.index', compact('servers', 'exposureOptions'));
    }

    public function create()
    {
        return view('servers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip|unique:servers',
            'type' => 'required|in:linux,windows,ubuntu,centos,redhat,debian,freebsd,macos,other',
            'exposed' => 'nullable|string|max:500',
            'password' => 'required|string'
        ]);

        $server = Server::create([
            'ip' => $request->ip,
            'type' => $request->type,
            'exposed' => $request->exposed,
            'password' => $request->password
        ]);
        
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $user->assignedServers()->attach($server->id);
        }

        return redirect()->route('servers.index')->with('success', 'Server created successfully');
    }

    public function show(Server $server)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only Super Admin can view server details.');
        }
        
        return view('servers.show', compact('server'));
    }

    public function edit(Server $server)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. You do not have permission to edit servers.');
        }
        
        return view('servers.edit', compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. You do not have permission to update servers.');
        }
        
        $request->validate([
            'ip' => 'required|ip|unique:servers,ip,' . $server->id,
            'type' => 'required|in:linux,windows,ubuntu,centos,redhat,debian,freebsd,macos,other',
            'exposed' => 'nullable|string|max:500',
            'password' => 'required|string'
        ]);

        $server->update([
            'ip' => $request->ip,
            'type' => $request->type,
            'exposed' => $request->exposed,
            'password' => $request->password
        ]);

        return redirect()->route('servers.index')->with('success', 'Server updated successfully');
    }

    public function destroy(Server $server)
    {
        // Only SuperAdmin can delete servers
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can delete servers.');
        }
        
        $server->portals()->delete();
        $server->delete();
        
        return redirect()->route('servers.index')->with('success', 'Server deleted successfully');
    }
}
