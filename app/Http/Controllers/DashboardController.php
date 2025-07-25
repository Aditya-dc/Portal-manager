<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Portal;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = trim($request->input('search', ''));
        $stats = [
            'total_servers'    => Server::count(),
            'total_portals'    => Portal::count(),
            'active_portals'   => Portal::where('status', 'up')->count(),
            'inactive_portals' => Portal::where('status', 'down')->count(),
        ];
        $foundType = null;
        $matchedPortalId = null;

        // Default: paginated servers
        $serversPaginator = Server::with('portals')
            ->orderBy('name')->paginate(5);// Adjust per-page as needed

        // If search active, only show block for that server or for matched portal's parent server
        if ($searchTerm) {
            $server = Server::with('portals')
                ->where('name', 'like', "%{$searchTerm}%")
                ->first();

            if ($server) {
                $serversPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                    [$server], 1, 1, $request->input('page', 1),
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                $foundType = 'server';
            } else {
                $portal = Portal::with('server')
                    ->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('url', 'like', "%{$searchTerm}%")
                    ->first();
                if ($portal && $portal->server) {
                    $parentServer = $portal->server()->with('portals')->first();
                    $serversPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                        [$parentServer], 1, 1, $request->input('page', 1),
                        ['path' => $request->url(), 'query' => $request->query()]
                    );
                    $foundType = 'portal';
                    $matchedPortalId = $portal->id;
                } else {
                    $serversPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                        [], 0, 1, $request->input('page', 1),
                        ['path' => $request->url(), 'query' => $request->query()]
                    );
                }
            }
        }

        return view('dashboard', [
            'stats'         => $stats,
            'servers'       => $serversPaginator,
            'searchTerm'    => $searchTerm,
            'foundType'     => $foundType,
            'matchedPortalId' => $matchedPortalId,
        ]);
    }
}
