<?php
namespace App\Imports;

use App\Models\Portal;
use App\Models\Server;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PortalsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $importedCount = 0;

    public function model(array $row)
    {
        $server = Server::where('name', $row['server_name'])->first();
        
        if (!$server) {
            $server = Server::create([
                'name' => $row['server_name'],
                'ip' => $row['server_ip'] ?? '192.168.1.1',
                'password' => 'default_password',
                'type' => strtolower($row['server_type'] ?? 'linux'),
                'exposed' => strtolower($row['server_exposure'] ?? 'internal')
            ]);
        }

       
        $existingPortal = Portal::where('name', $row['portal_name'])
                                ->where('server_id', $server->id)
                                ->first();

        if ($existingPortal) {
            
            $existingPortal->update([
                'url' => $row['url'],
                'developed_by' => $row['developed_by'],
                'vapt' => strtolower($row['vapt'] ?? 'no') === 'yes',
                'backup' => strtolower($row['backup'] ?? 'no') === 'yes',
            ]);
            return null;
        }

        $this->importedCount++;

        return new Portal([
            'server_id' => $server->id,
            'name' => $row['portal_name'],
            'url' => $row['url'],
            'developed_by' => $row['developed_by'],
            'vapt' => strtolower($row['vapt'] ?? 'no') === 'yes',
            'backup' => strtolower($row['backup'] ?? 'no') === 'yes',
            'status' => 'down' 
        ]);
    }

    public function rules(): array
    {
        return [
            'portal_name' => 'required|string|max:255',
            'server_name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'developed_by' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'portal_name.required' => 'Portal name is required.',
            'server_name.required' => 'Server name is required.',
            'url.required' => 'Portal URL is required.',
            'url.url' => 'Portal URL must be a valid URL.',
            'developed_by.required' => 'Developer name is required.',
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }
}
