<?php
namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::with('portals')->paginate(10);
        return view('servers.index', compact('servers'));
    }

    public function create()
    {
        return view('servers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|ip',
            'password' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'exposed' => 'required|in:internal,external',
        ]);

        Server::create($validated);

        return redirect()->route('servers.index')
                        ->with('success', 'Server created successfully.');
    }

    public function show(Server $server)
    {
        $server->load('portals');
        return view('servers.show', compact('server'));
    }

    public function edit(Server $server)
    {
        return view('servers.edit', compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|ip',
            'password' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'exposed' => 'required|in:internal,external',
        ]);

        $server->update($validated);

        return redirect()->route('servers.index')
                        ->with('success', 'Server updated successfully.');
    }

    public function destroy(Server $server)
    {
        $server->delete();
        return redirect()->route('servers.index')
                        ->with('success', 'Server deleted successfully.');
    }
}
