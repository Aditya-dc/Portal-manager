<?php
namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Portal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $servers = Server::with(['portals' => function($query) use ($request) {
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('url', 'like', '%' . $request->search . '%')
                      ->orWhere('developed_by', 'like', '%' . $request->search . '%');
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
        }])->get();

        $stats = [
            'total_servers' => Server::count(),
            'total_portals' => Portal::count(),
            'active_portals' => Portal::where('status', 'up')->count(),
            'inactive_portals' => Portal::where('status', 'down')->count()
        ];

        return view('dashboard', compact('servers', 'stats'));
    }
}
