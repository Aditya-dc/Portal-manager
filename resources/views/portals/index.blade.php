@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Portals Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('portals.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Portal
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAllPortals()">
                <i class="fas fa-sync-alt"></i> Check All Status
            </button>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('portals.export') }}">
                        <i class="fas fa-download"></i> Export to Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-upload"></i> Import from Excel
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('portals.import-template') }}">
                        <i class="fas fa-file-csv"></i> Download Template
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('portals.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search portals..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="server_id">
                        <option value="">All Servers</option>
                        @foreach($servers as $server)
                            <option value="{{ $server->id }}" {{ request('server_id') == $server->id ? 'selected' : '' }}>
                                {{ $server->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="up" {{ request('status') == 'up' ? 'selected' : '' }}>Up</option>
                        <option value="down" {{ request('status') == 'down' ? 'selected' : '' }}>Down</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('portals.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($portals->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Portal Name</th>
                            <th>Server</th>
                            <th>URL</th>
                            <th>Developer</th>
                            <th>Status</th>
                            <th>VAPT</th>
                            <th>Backup</th>
                            <th>Last Checked</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($portals as $portal)
                        <tr>
                            <td>
                                <strong>{{ $portal->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $portal->server->name }}</span>
                            </td>
                            <td>
                                <a href="{{ $portal->url }}" target="_blank" class="text-decoration-none">
                                    {{ Str::limit($portal->url, 40) }}
                                    <i class="fas fa-external-link-alt fa-xs"></i>
                                </a>
                            </td>
                            <td>{{ $portal->developed_by }}</td>
                            <td>
                                <span class="badge {{ $portal->status == 'up' ? 'badge-success' : 'badge-danger' }}">
                                    <i class="fas fa-circle fa-xs"></i> {{ ucfirst($portal->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $portal->vapt ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $portal->vapt ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $portal->backup ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $portal->backup ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $portal->last_checked ? $portal->last_checked->diffForHumans() : 'Never' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button onclick="checkPortalStatus({{ $portal->id }})" class="btn btn-outline-info" title="Check Status">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <a href="{{ route('portals.edit', $portal) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('portals.destroy', $portal) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this portal?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $portals->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                <h5>No Portals Found</h5>
                <p class="text-muted">Add your first portal to get started.</p>
                <a href="{{ route('portals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Portal
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Portals from CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('portals.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="file" name="file" 
                               accept=".csv" required>
                        <div class="form-text">
                            Supported format: .csv (Max size: 2MB)
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Import Guidelines:</h6>
                       <ul class="mb-0">
        <li>✅ Portals will be created from your CSV file</li>
        <li>✅ Status of each portal will be automatically checked</li>
        <li>✅ Process may take time for more number of portals</li>
        <li>✅ No need to manually check status after import</li>
        <li>⚠️ Please wait for the import to complete</li>
    </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('portals.import-template') }}" class="btn btn-outline-info">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import Portals
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function checkPortalStatus(portalId) {
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    fetch(`/portals/${portalId}/check-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(() => {
        location.reload();
    }).catch(() => {
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}

function checkAllPortals() {
    const button = event.target;
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
    button.disabled = true;
    
    fetch('/portals/check-all-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(() => {
        location.reload();
    }).catch(() => {
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}
</script>

@endsection
