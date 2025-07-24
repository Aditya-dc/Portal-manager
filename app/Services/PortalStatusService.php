<?php
namespace App\Services;

use App\Models\Portal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PortalStatusService
{
    public function checkPortalStatus(Portal $portal)
    {
        try {
            $response = Http::timeout(5)->get($portal->url);
            $status = $response->successful() ? 'up' : 'down';
            
        } catch (\Exception $e) {
            $status = 'down';
            Log::info("Portal status check failed for {$portal->name}: " . $e->getMessage());
        }

        $portal->update([
            'status' => $status,
            'last_checked' => now()
        ]);

        return $status;
    }

    public function checkAllPortals()
    {
        $portals = Portal::all();
        $results = [];

        foreach ($portals as $portal) {
            $results[$portal->id] = $this->checkPortalStatus($portal);
        }

        return $results;
    }
}
