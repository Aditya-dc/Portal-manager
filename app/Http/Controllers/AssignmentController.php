<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Server;
use App\Models\Portal;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display all non-superadmin users with their assignments.
     */
    public function index()
    {
        $users = User::where('role', '!=', 'superadmin')
            ->with(['assignedServers', 'assignedPortals'])
            ->get();

        return view('admin.assignments', compact('users'));
    }

    /**
     * Assign servers and portals to a user.
     */
    public function assign(Request $request, User $user)
    {
        if ($request->server_ids) {
            $user->assignedServers()->sync($request->server_ids);
        }
        if ($request->portal_ids) {
            $user->assignedPortals()->sync($request->portal_ids);
        }
        return back()->with('success', 'Resources assigned successfully');
    }

    /**
     * Get user data for AJAX assignment modal.
     * Returns user, all servers/portals, and assigned IDs.
     */
    public function getUserData(User $user)
    {
        $servers = Server::all();
        $portals = Portal::all();
        $assignedServerIds = $user->assignedServers->pluck('id')->toArray();
        $assignedPortalIds = $user->assignedPortals->pluck('id')->toArray();

        return response()->json([
            'user' => $user,
            'servers' => $servers,
            'portals' => $portals,
            'assignedServerIds' => $assignedServerIds,
            'assignedPortalIds' => $assignedPortalIds
        ]);
    }

    /**
     * Remove a specific server from user assignments.
     */
    public function removeServer(User $user, Server $server)
    {
        $user->assignedServers()->detach($server->id);
        return back()->with('success', 'Server unassigned successfully');
    }

    /**
     * Remove a specific portal from user assignments.
     */
    public function removePortal(User $user, Portal $portal)
    {
        $user->assignedPortals()->detach($portal->id);
        return back()->with('success', 'Portal unassigned successfully');
    }

    /**
     * Remove all servers from user assignments.
     */
    public function removeAllServers(User $user)
    {
        $user->assignedServers()->detach();
        return back()->with('success', 'All servers unassigned successfully');
    }

    /**
     * Remove all portals from user assignments.
     */
    public function removeAllPortals(User $user)
    {
        $user->assignedPortals()->detach();
        return back()->with('success', 'All portals unassigned successfully');
    }

    /**
     * Bulk unassign selected servers and portals from user.
     */
    public function bulkUnassign(Request $request, User $user)
    {
        $request->validate([
            'server_ids' => 'array',
            'server_ids.*' => 'exists:servers,id',
            'portal_ids' => 'array',
            'portal_ids.*' => 'exists:portals,id'
        ]);

        if ($request->server_ids) {
            $user->assignedServers()->detach($request->server_ids);
        }
        if ($request->portal_ids) {
            $user->assignedPortals()->detach($request->portal_ids);
        }
        return back()->with('success', 'Selected resources unassigned successfully');
    }

    /**
     * Get assignment statistics for dashboard or reporting.
     */
    public function getAssignmentStats()
    {
        $stats = [
            'total_users' => User::where('role', '!=', 'superadmin')->count(),
            'users_with_servers' => User::whereHas('assignedServers')->count(),
            'users_with_portals' => User::whereHas('assignedPortals')->count(),
            'total_servers' => Server::count(),
            'assigned_servers' => Server::whereHas('assignedUsers')->count(),
            'total_portals' => Portal::count(),
            'assigned_portals' => Portal::whereHas('assignedUsers')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Export user assignments as a CSV file.
     */
    public function exportAssignments()
    {
        $users = User::where('role', '!=', 'superadmin')
            ->with(['assignedServers', 'assignedPortals'])
            ->get();

        $headers = [
            'User Name',
            'Email',
            'Role',
            'Assigned Servers',
            'Assigned Portals',
            'Server Count',
            'Portal Count'
        ];

        $csvData = [];
        $csvData[] = $headers;

        foreach ($users as $user) {
            $csvData[] = [
                $user->name,
                $user->email,
                ucfirst($user->role),
                $user->assignedServers->pluck('name')->implode(', '),
                $user->assignedPortals->pluck('name')->implode(', '),
                $user->assignedServers->count(),
                $user->assignedPortals->count()
            ];
        }

        $filename = 'user_assignments_' . date('Y-m-d_H-i-s') . '.csv';

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
