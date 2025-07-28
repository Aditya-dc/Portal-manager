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
        $server = Server::where('ip', $row['server_ip'])->first();
        
        if (!$server) {
            $server = Server::create([
                'ip' => $row['server_ip'],
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
            'server_ip' => 'required|ip',
            'url' => 'required|url|max:255',
            'developed_by' => 'required|string|max:255',
            'server_type' => 'nullable|in:linux,windows,ubuntu,centos,redhat,debian,freebsd,macos,other',
            'server_exposure' => 'nullable|in:internal,external'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'portal_name.required' => 'Portal name is required.',
            'server_ip.required' => 'Server IP is required.',
            'server_ip.ip' => 'Server IP must be a valid IP address.',
            'url.required' => 'Portal URL is required.',
            'url.url' => 'Portal URL must be a valid URL.',
            'developed_by.required' => 'Developer name is required.',
            'server_type.in' => 'Server type must be one of: linux, windows, ubuntu, centos, redhat, debian, freebsd, macos, other.',
            'server_exposure.in' => 'Server exposure must be either internal or external.'
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }
}
