<?php
namespace App\Exports;

use App\Models\Portal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PortalsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return Portal::with('server')->get();
        } else {
            return $user->assignedPortals()->with('server')->get();
        }
    }

    public function headings(): array
    {
        return [
            'Portal ID',
            'Portal Name',
            'URL',
            'Developed By',
            'VAPT',
            'Backup',
            'Status',
            'Last Checked',
            'Server IP',
            'Server Type',
            'Server Exposure',
            'Created At',
            'Updated At'
        ];
    }

    public function map($portal): array
    {
        return [
            $portal->id,
            $portal->name,
            $portal->url,
            $portal->developed_by,
            $portal->vapt ? 'Yes' : 'No',
            $portal->backup ? 'Yes' : 'No',
            ucfirst($portal->status),
            $portal->last_checked ? $portal->last_checked->format('Y-m-d H:i:s') : 'Never',
            $portal->server ? $portal->server->ip : 'N/A',
            $portal->server ? ucfirst($portal->server->type) : 'N/A',
            $portal->server ? ucfirst($portal->server->exposed) : 'N/A',
            $portal->created_at->format('Y-m-d H:i:s'),
            $portal->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
