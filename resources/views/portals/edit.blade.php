@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Portal: {{ $portal->name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('portals.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Portals
            </a>
            <button onclick="checkPortalStatus({{ $portal->id }})" class="btn btn-sm btn-outline-info">
                <i class="fas fa-sync-alt"></i> Check Status
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Portal Information</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('portals.update', $portal) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Portal Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $portal->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="server_id" class="form-label">Server *</label>
                                <select class="form-select" id="server_id" name="server_id" required>
                                    <option value="">Select Server</option>
                                    @foreach($servers as $server)
                                        <option value="{{ $server->id }}" 
                                                {{ old('server_id', $portal->server_id) == $server->id ? 'selected' : '' }}>
                                            {{ $server->name }} ({{ $server->ip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">Portal URL *</label>
                        <input type="url" class="form-control" id="url" name="url" 
                               value="{{ old('url', $portal->url) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="developed_by" class="form-label">Developed By *</label>
                        <input type="text" class="form-control" id="developed_by" name="developed_by" 
                               value="{{ old('developed_by', $portal->developed_by) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="vapt" name="vapt" value="1" 
                                           {{ old('vapt', $portal->vapt) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vapt">
                                        VAPT Completed
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="backup" name="backup" value="1" 
                                           {{ old('backup', $portal->backup) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="backup">
                                        Backup Available
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('portals.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Portal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Portal Status</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <span class="badge {{ $portal->status == 'up' ? 'badge-success' : 'badge-danger' }} fs-6">
                        <i class="fas fa-circle"></i> {{ ucfirst($portal->status) }}
                    </span>
                </div>
                <p><strong>Last Checked:</strong><br>
                   <small>{{ $portal->last_checked ? $portal->last_checked->format('M d, Y H:i:s') : 'Never' }}</small></p>
                <p><strong>Current URL:</strong><br>
                   <a href="{{ $portal->url }}" target="_blank" class="text-decoration-none">
                       {{ Str::limit($portal->url, 30) }} <i class="fas fa-external-link-alt fa-xs"></i>
                   </a></p>
                <p><strong>Server:</strong><br>{{ $portal->server->name }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Details</h6>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong> {{ $portal->created_at->format('M d, Y') }}</p>
                <p><strong>Last Updated:</strong> {{ $portal->updated_at->format('M d, Y') }}</p>
                <p><strong>VAPT Status:</strong> {{ $portal->vapt ? 'Completed' : 'Pending' }}</p>
                <p><strong>Backup Status:</strong> {{ $portal->backup ? 'Available' : 'Not Available' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function checkPortalStatus(portalId) {
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
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
</script>
@endsection
