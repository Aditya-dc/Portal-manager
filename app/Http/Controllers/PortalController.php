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
        $query = Portal::with('server');
        
       
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

        $portals = $query->paginate(10);
        $servers = Server::all();
        
        return view('portals.index', compact('portals', 'servers'));
    }

    public function create()
    {
        $servers = Server::all();
        return view('portals.create', compact('servers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'developed_by' => 'required|string|max:255',
            'vapt' => 'boolean',
            'backup' => 'boolean',
        ]);

        $portal = Portal::create($validated);
        
        
        $this->statusService->checkPortalStatus($portal);
        
        return redirect()->route('portals.index')
                        ->with('success', 'Portal created successfully.');
    }

    public function show(Portal $portal)
    {
        return view('portals.show', compact('portal'));
    }

    public function edit(Portal $portal)
    {
        $servers = Server::all();
        return view('portals.edit', compact('portal', 'servers'));
    }

    public function update(Request $request, Portal $portal)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'developed_by' => 'required|string|max:255',
            'vapt' => 'boolean',
            'backup' => 'boolean',
        ]);

        $portal->update($validated);
        
        return redirect()->route('portals.index')
                        ->with('success', 'Portal updated successfully.');
    }

    public function destroy(Portal $portal)
    {
        $portal->delete();
        return redirect()->route('portals.index')
                        ->with('success', 'Portal deleted successfully.');
    }

    public function checkStatus(Portal $portal)
    {
        $this->statusService->checkPortalStatus($portal);
        return back()->with('success', 'Portal status updated.');
    }

    public function checkAllStatus()
    {
        $portals = Portal::all();
        foreach ($portals as $portal) {
            $this->statusService->checkPortalStatus($portal);
        }
        return back()->with('success', 'All portal statuses updated.');
    }

    
    public function export()
    {
        $portals = Portal::with('server')->get();
        
        $headers = [
            'Server Name',
            'Portal Name', 
            'URL',
            'Developed By',
            'VAPT',
            'Backup',
            'Status',
            'Server IP',
            'Server Type',
            'Server Exposure',
        ];
        
        $csvData = [];
        $csvData[] = $headers;
        
        foreach ($portals as $portal) {
            $csvData[] = [
                $portal->server->name,
                $portal->name,
                $portal->url,
                $portal->developed_by,
                $portal->vapt ? 'Yes' : 'No',
                $portal->backup ? 'Yes' : 'No',
                ucfirst($portal->status),
                $portal->server->ip,
                ucfirst($portal->server->type),
                ucfirst($portal->server->exposed),

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
    $request->validate([
        'file' => 'required|mimes:csv,txt|max:2048'
    ]);

    try {
        $file = $request->file('file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        
      
        $headers = array_shift($csvData);
        
        $importedCount = 0;
        $errors = [];
        
        foreach ($csvData as $rowIndex => $row) {
            if (count($row) < 4) {
                continue; 
            }
            
            try {
                
                $server = Server::firstOrCreate(
                    ['name' => $row[0]], 
                    [
                        'ip' => $row[6] ?? '192.168.1.1',
                        'password' => 'default_password',
                        'type' => strtolower($row[7] ?? 'linux'),
                        'exposed' => strtolower($row[8] ?? 'internal')
                    ]
                );
                
              
                $portal = Portal::updateOrCreate(
                    [
                        'name' => $row[1], 
                        'server_id' => $server->id
                    ],
                    [
                        'url' => $row[2],
                        'developed_by' => $row[3], 
                        'vapt' => isset($row[4]) ? (strtolower($row[4]) === 'yes') : false,
                        'backup' => isset($row[5]) ? (strtolower($row[5]) === 'yes') : false,
                        'status' => 'down' 
                    ]
                );
                
             
                $this->statusService->checkPortalStatus($portal);
                
                $importedCount++;
                
            } catch (\Exception $e) {
                $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }
        
        if (!empty($errors)) {
            return back()->with('error', "Import completed with {$importedCount} portals. Errors: " . implode(', ', $errors));
        }
        
        return back()->with('success', "Successfully imported {$importedCount} portals with status automatically checked and updated!");
        
    } catch (\Exception $e) {
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}

    
    public function downloadTemplate()
    {
        $headers = [
            'server_name',
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
                'Production Server',
                'Company Website',
                'https://www.example.com',
                'Web Development Team',
                'yes',
                'yes',
                '192.168.1.100',
                'linux',
                'external'
            ],
            [
                'Development Server',
                'Admin Dashboard',
                'https://admin.example.com', 
                'Internal IT Team',
                'no',
                'yes',
                '192.168.1.101',
                'windows',
                'internal'
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
