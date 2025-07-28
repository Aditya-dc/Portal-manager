<?php

namespace App\Http\Controllers;

use App\Models\Portal;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $searchTerm = $request->get('search');

        if ($user->isSuperAdmin()) {
            $serversQuery = Server::with(['portals' => function($query) {
                $query->orderBy('name');
            }]);
            $totalServers = Server::count();
            $totalPortals = Portal::count();
            $activePortals = Portal::where('status', 'up')->count();
            $inactivePortals = Portal::where('status', 'down')->count();
        } else {
            $serversQuery = $user->assignedServers()->with(['portals' => function($query) use ($user) {
                $query->whereHas('assignedUsers', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->orderBy('name');
            }]);
            
            $assignedPortals = $user->assignedPortals;
            $totalServers = $user->assignedServers->count();
            $totalPortals = $assignedPortals->count();
            $activePortals = $assignedPortals->where('status', 'up')->count();
            $inactivePortals = $assignedPortals->where('status', 'down')->count();
        }

        $foundType = null;
        $matchedPortalId = null;
        
        if ($searchTerm) {
            $serversQuery->where(function($query) use ($searchTerm, $user) {
                $query->where('ip', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('portals', function($q) use ($searchTerm, $user) {
                          $q->where('name', 'like', '%' . $searchTerm . '%');
                          if (!$user->isSuperAdmin()) {
                              $q->whereHas('assignedUsers', function($subQ) use ($user) {
                                  $subQ->where('user_id', $user->id);
                              });
                          }
                      });
            });

            if ($user->isSuperAdmin()) {
                $matchedPortal = Portal::where('name', 'like', '%' . $searchTerm . '%')->first();
            } else {
                $matchedPortal = $user->assignedPortals()->where('name', 'like', '%' . $searchTerm . '%')->first();
            }
            
            if ($matchedPortal) {
                $foundType = 'portal';
                $matchedPortalId = $matchedPortal->id;
            }
        }

        $servers = $serversQuery->paginate(10);

        $stats = [
            'total_servers' => $totalServers,
            'total_portals' => $totalPortals,
            'active_portals' => $activePortals,
            'inactive_portals' => $inactivePortals,
        ];

        return view('dashboard', compact(
            'servers', 
            'stats', 
            'searchTerm', 
            'foundType', 
            'matchedPortalId'
        ));
    }
}
