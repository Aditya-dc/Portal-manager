@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAllPortals()">
                <i class="fas fa-sync-alt"></i> Check All Status
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h4">{{ $stats['total_servers'] }}</div>
                        <div>Total Servers</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-server fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h4">{{ $stats['active_portals'] }}</div>
                        <div>Active Portals</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h4">{{ $stats['inactive_portals'] }}</div>
                        <div>Inactive Portals</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="h4">{{ $stats['total_portals'] }}</div>
                        <div>Total Portals</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-globe fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Quick Actions</h5>
                <div class="btn-group" role="group">
                    <a href="{{ route('servers.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Add Server
                    </a>
                    <a href="{{ route('portals.create') }}" class="btn btn-outline-success">
                        <i class="fas fa-plus"></i> Add Portal
                    </a>
                    <a href="{{ route('servers.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-list"></i> View All Servers
                    </a>
                    <a href="{{ route('portals.index') }}" class="btn btn-outline-warning">
                        <i class="fas fa-list"></i> View All Portals
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Servers and Portals Overview -->
@if($servers->count() > 0)
    @foreach($servers as $server)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-server"></i> {{ $server->name }}
                <span class="badge bg-secondary">{{ $server->portals->count() }} portals</span>
            </h5>
            <div>
                <small class="text-muted">{{ $server->portals->where('status', 'up')->count() }} active</small>
                <span class="badge bg-info">{{ ucfirst($server->type) }}</span>
                <span class="badge {{ $server->exposed == 'external' ? 'bg-warning' : 'bg-success' }}">
                    {{ ucfirst($server->exposed) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <small><strong>IP:</strong> {{ $server->ip }}</small>
                </div>
                <div class="col-md-6">
                    <small><strong>Type:</strong> {{ ucfirst($server->type) }}</small>
                </div>
            </div>
            
            @if($server->portals->count() > 0)
                <div class="row">
                    @foreach($server->portals as $portal)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card portal-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">{{ $portal->name }}</h6>
                                    <span class="badge {{ $portal->status == 'up' ? 'badge-success' : 'badge-danger' }}">
                                        {{ ucfirst($portal->status) }}
                                    </span>
                                </div>
                                <p class="card-text small text-muted mb-2">
                                    <i class="fas fa-link"></i> 
                                    <a href="{{ $portal->url }}" target="_blank">{{ Str::limit($portal->url, 30) }}</a>
                                </p>
                                <div class="row text-sm">
                                    <div class="col-12">
                                        <small><strong>Developer:</strong> {{ $portal->developed_by }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small><strong>VAPT:</strong> {{ $portal->vapt ? 'Yes' : 'No' }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small><strong>Backup:</strong> {{ $portal->backup ? 'Yes' : 'No' }}</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="{{ route('portals.edit', $portal) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="checkPortalStatus({{ $portal->id }})" class="btn btn-outline-info">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No portals found for this server.</p>
            @endif
        </div>
    </div>
    @endforeach
@else
    <div class="card">
        <div class="card-body text-center">
            <i class="fas fa-server fa-3x text-muted mb-3"></i>
            <h5>No Servers Found</h5>
            <p class="text-muted">Get started by adding your first server.</p>
            <a href="{{ route('servers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Your First Server
            </a>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
function checkPortalStatus(portalId) {
    fetch(`/portals/${portalId}/check-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(() => {
        location.reload();
    });
}

function checkAllPortals() {
    fetch('/portals/check-all-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(() => {
        location.reload();
    });
}
</script>
@endsection
