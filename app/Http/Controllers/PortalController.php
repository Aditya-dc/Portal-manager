<?php
namespace App\Http\Controllers;

use App\Models\Portal;
use App\Models\Server;
use App\Services\PortalStatusService;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    protected $statusService;

    public function __construct(PortalStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Portal::with('server')->forUser($user);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('url', 'like', '%' . $request->search . '%')
                  ->orWhere('developed_by', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('server_id')) {
            $query->where('server_id', $request->server_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $portals = $query->paginate(15);
        
        if ($user->isSuperAdmin()) {
            $servers = Server::all();
        } else {
            $servers = $user->assignedServers;
        }
        
        return view('portals.index', compact('portals', 'servers'));
    }

    public function create()
    {
        if (!auth()->user()->canModify()) {
            abort(403, 'Access denied');
        }

        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $servers = Server::all();
        } else {
            $servers = $user->assignedServers;
        }
        
        return view('portals.create', compact('servers'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canModify()) {
            abort(403, 'Access denied');
        }

        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'developed_by' => 'required|string|max:255',
            'vapt' => 'boolean',
            'backup' => 'boolean',
        ]);

        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $userServerIds = $user->assignedServers->pluck('id')->toArray();
            if (!in_array($validated['server_id'], $userServerIds)) {
                abort(403, 'Access denied to this server');
            }
        }

        $portal = Portal::create($validated);
        
        if (!$user->isSuperAdmin()) {
            $user->assignedPortals()->attach($portal->id);
        }
        
        $this->statusService->checkPortalStatus($portal);
        
        return redirect()->route('portals.index')
                        ->with('success', 'Portal created successfully.');
    }

    public function show(Portal $portal)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            $userPortalIds = $user->assignedPortals->pluck('id')->toArray();
            if (!in_array($portal->id, $userPortalIds)) {
                abort(403, 'Access denied to this portal');
            }
        }
        
        return view('portals.show', compact('portal'));
    }

    public function edit(Portal $portal)
    {
        if (!auth()->user()->canModify()) {
            abort(403, 'Access denied');
        }

        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            $userPortalIds = $user->assignedPortals->pluck('id')->toArray();
            if (!in_array($portal->id, $userPortalIds)) {
                abort(403, 'Access denied to this portal');
            }
        }
        
        if ($user->isSuperAdmin()) {
            $servers = Server::all();
        } else {
            $servers = $user->assignedServers;
        }
        
        return view('portals.edit', compact('portal', 'servers'));
    }

    public function update(Request $request, Portal $portal)
    {
        if (!auth()->user()->canModify()) {
            abort(403, 'Access denied');
        }

        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            $userPortalIds = $user->assignedPortals->pluck('id')->toArray();
            if (!in_array($portal->id, $userPortalIds)) {
                abort(403, 'Access denied to this portal');
            }
        }

        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'developed_by' => 'required|string|max:255',
            'vapt' => 'boolean',
            'backup' => 'boolean',
        ]);

        if (!$user->isSuperAdmin()) {
            $userServerIds = $user->assignedServers->pluck('id')->toArray();
            if (!in_array($validated['server_id'], $userServerIds)) {
                abort(403, 'Access denied to this server');
            }
        }

        $portal->update($validated);
        
        return redirect()->route('portals.index')
                        ->with('success', 'Portal updated successfully.');
    }

    public function destroy(Portal $portal)
    {
        if (!auth()->user()->canModify()) {
            abort(403, 'Access denied');
        }

        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            $userPortalIds = $user->assignedPortals->pluck('id')->toArray();
            if (!in_array($portal->id, $userPortalIds)) {
                abort(403, 'Access denied to this portal');
            }
        }

        $portal->delete();
        return redirect()->route('portals.index')
                        ->with('success', 'Portal deleted successfully.');
    }

    public function checkStatus(Portal $portal)
    {
        if (!auth()->user()->canModify()) {
            abort(403, 'Access denied');
        }

        $user = auth()->user();
        
        if (!$user->isSuperAdmin()) {
            $userPortalIds = $user->assignedPortals->pluck('id')->toArray();
            if (!in_array($portal->id, $userPortalIds)) {
                abort(403, 'Access denied to this portal');
            }
        }

        $this->statusService->checkPortalStatus($portal);
        return back()->with('success', 'Portal status updated.');
    }

    public function checkAllStatus()
    {
        if (!auth()->user()->canModify()) {
            abort(403, 'Access denied');
        }

        $user = auth()->user();
        $portals = Portal::forUser($user)->get();
        
        foreach ($portals as $portal) {
            $this->statusService->checkPortalStatus($portal);
        }
        
        return back()->with('success', 'All accessible portal statuses updated.');
    }

    public function export()
    {
        $user = auth()->user();
        $portals = Portal::with('server')->forUser($user)->get();
        
        $headers = [
            'portal_name',
            'url',
            'developed_by',
            'vapt',
            'backup',
            'server_ip',
            'server_type',
            'server_exposure',
        ];
        
        $csvData = [];
        $csvData[] = $headers;
        
        foreach ($portals as $portal) {
            $csvData[] = [
                $portal->name,
                $portal->url,
                $portal->developed_by,
                $portal->vapt ? 'yes' : 'no',
                $portal->backup ? 'yes' : 'no',
                $portal->server->ip,
                $portal->server->type ?: '',
                $portal->server->exposed ?: '',
            ];
        }
        
        $filename = 'portals_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
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
    
    public function import(Request $request)
    {
        if (!auth()->user()->canModify()) {
            abort(403, 'Access denied');
        }

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        try {
            $user = auth()->user();
            $file = $request->file('file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            
            $headers = array_shift($csvData);
            
            $importedCount = 0;
            $updatedCount = 0;
            $errors = [];
            
            foreach ($csvData as $rowIndex => $row) {
                if (empty(array_filter($row))) {
                    continue;
                }
                
                if (count($row) < 6) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Insufficient columns (minimum 6 required)";
                    continue;
                }

                try {
                    $portalName = trim($row[0]);
                    $url = trim($row[1]);
                    $developedBy = trim($row[2]);
                    $vapt = trim($row[3] ?? 'no');
                    $backup = trim($row[4] ?? 'no');
                    $serverIp = trim($row[5]);
                    $serverType = trim($row[6] ?? '');
                    $serverExposure = trim($row[7] ?? '');
                    
                    if (!filter_var($serverIp, FILTER_VALIDATE_IP)) {
                        throw new \Exception("Invalid IP address: {$serverIp}");
                    }
                    
                    $server = Server::where('ip', $serverIp)->first();
                    $serverWasCreated = false;

                    if (!$server) {
                        // Create new server with custom exposure text
                        $server = Server::create([
                            'ip' => $serverIp,
                            'password' => 'default_password',
                            'type' => $serverType ?: null,
                            'exposed' => $serverExposure ?: null
                        ]);
                        $serverWasCreated = true;
                        
                        if (!$user->isSuperAdmin()) {
                            $user->assignedServers()->attach($server->id);
                        }
                    } else {
                        // Server exists - check access
                        if (!$user->isSuperAdmin()) {
                            $userServerIds = $user->assignedServers->pluck('id')->toArray();
                            if (!in_array($server->id, $userServerIds)) {
                                throw new \Exception("Access denied to existing server {$serverIp}. Contact SuperAdmin for assignment.");
                            }
                        }
                    }
                    
                    if (empty($portalName)) {
                        throw new \Exception("Portal name is required");
                    }
                    if (!filter_var($url, FILTER_VALIDATE_URL)) {
                        throw new \Exception("Invalid URL format: {$url}");
                    }
                    if (empty($developedBy)) {
                        $developedBy = 'Not Specified';
                    }
                    
                    
                    $existingPortal = Portal::where('name', $portalName)
                        ->where('server_id', $server->id)
                        ->first();

                    if (!$existingPortal) {
                        
                        $portal = Portal::create([
                            'name' => $portalName,
                            'server_id' => $server->id,
                            'url' => $url,
                            'developed_by' => $developedBy,
                            'vapt' => strtolower($vapt) === 'yes',
                            'backup' => strtolower($backup) === 'yes',
                            'status' => 'down'
                        ]);
                        
                        if (!$user->isSuperAdmin()) {
                            $user->assignedPortals()->attach($portal->id);
                        }

                        $importedCount++;
                    } else {
                        
                        if (!$user->isSuperAdmin()) {
                            $userPortalIds = $user->assignedPortals->pluck('id')->toArray();
                            if (!in_array($existingPortal->id, $userPortalIds)) {
                                throw new \Exception("Access denied to existing portal '{$portalName}'. Contact SuperAdmin for assignment.");
                            }
                        }
                        
                
                        $existingPortal->update([
                            'url' => $url,
                            'developed_by' => $developedBy,
                            'vapt' => strtolower($vapt) === 'yes',
                            'backup' => strtolower($backup) === 'yes',
                        ]);

                        $updatedCount++;
                    }

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }
            
            if (!empty($errors)) {
                $successPart = "";
                if ($importedCount > 0 || $updatedCount > 0) {
                    $successPart = "Processed: {$importedCount} new, {$updatedCount} updated. ";
                }
                $errorMessage = $successPart . "Errors: " . implode(' | ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $errorMessage .= " (and " . (count($errors) - 5) . " more errors)";
                }
                return back()->with('error', $errorMessage);
            }
            
            $message = "Successfully imported {$importedCount} new portals";
            if ($updatedCount > 0) {
                $message .= " and updated {$updatedCount} existing portals";
            }
            $message .= "! Use 'Check All Status' button to verify portal connectivity.";
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'portal_name',
            'url', 
            'developed_by',
            'vapt',
            'backup',
            'server_ip',
            'server_type',
            'server_exposure'
        ];

        $sampleData = [
            [
                'Company Website',
                'https://www.example.com',
                'Web Development Team',
                'yes',
                'yes',
                '192.168.1.100',
                'linux',
                'Exposed to Internet'
            ],
            [
                'Admin Dashboard',
                'https://admin.example.com', 
                'Internal IT Team',
                'no',
                'yes',
                '192.168.1.101',
                '',
                'Exposed to Intranet'
            ]
        ];

        $filename = 'portal_import_template.csv';
        
        $callback = function() use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');
            
            fwrite($file, "\xEF\xBB\xBF");
            
            fputcsv($file, $headers);
            
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
