@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Portal Details: {{ $portal->name }}</h4>
                    <div>
                        @if(auth()->user()->canModify())
                        <a href="{{ route('portals.edit', $portal) }}" class="btn btn-warning">Edit Portal</a>
                        @endif
                        <a href="{{ route('portals.index') }}" class="btn btn-secondary">Back to Portals</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Portal Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $portal->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>URL:</strong></td>
                                    <td>
                                        <a href="{{ $portal->url }}" target="_blank" class="text-decoration-none">
                                            {{ $portal->url }}
                                            <i class="fas fa-external-link-alt fa-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Developed By:</strong></td>
                                    <td>{{ $portal->developed_by }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $portal->status === 'up' ? 'success' : 'danger' }}">
                                            {{ ucfirst($portal->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>VAPT:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $portal->vapt ? 'success' : 'secondary' }}">
                                            {{ $portal->vapt ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Backup:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $portal->backup ? 'success' : 'secondary' }}">
                                            {{ $portal->backup ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Last Checked:</strong></td>
                                    <td>
                                        @if($portal->last_checked)
                                            {{ $portal->last_checked->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $portal->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $portal->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Server Information</h5>
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $portal->server->ip }}</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>IP Address:</strong></td>
                                            <td>{{ $portal->server->ip }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $portal->server->type === 'linux' ? 'success' : 'info' }}">
                                                    {{ ucfirst($portal->server->type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Exposure:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $portal->server->exposed === 'external' ? 'danger' : 'secondary' }}">
                                                    {{ ucfirst($portal->server->exposed) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if(auth()->user()->canViewPasswords())
                                        <tr>
                                            <td><strong>Server Password:</strong></td>
                                            <td>
                                                <code>{{ $portal->server->password }}</code>
                                                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $portal->server->password }}')">
                                                    <i class="fas fa-copy"></i> Copy
                                                </button>
                                            </td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td><strong>Server Password:</strong></td>
                                            <td><span class="text-muted">Hidden (Super Admin only)</span></td>
                                        </tr>
                                        @endif
                                    </table>
                                    {{-- View Full Server Details button - ONLY visible to Super Admin --}}
                                    @if(auth()->user()->isSuperAdmin())
                                        <div class="mt-3">
                                            <a href="{{ route('servers.show', $portal->server) }}" class="btn btn-sm btn-outline-primary">
                                                View Full Server Details
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Portal Actions section - Only for users who can modify --}}
                    @if(auth()->user()->canModify())
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Portal Actions</h5>
                            <div class="btn-group" role="group">
                                <form method="POST" action="{{ route('portals.check-status', $portal) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-info">
                                        <i class="fas fa-sync"></i> Check Status
                                    </button>
                                </form>
                                
                                <a href="{{ $portal->url }}" target="_blank" class="btn btn-outline-success">
                                    <i class="fas fa-external-link-alt"></i> Visit Portal
                                </a>
                                
                                <a href="{{ route('portals.edit', $portal) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i> Edit Portal
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- Read-only users can still visit the portal --}}
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Portal Actions</h5>
                            <div class="btn-group" role="group">
                                <a href="{{ $portal->url }}" target="_blank" class="btn btn-outline-success">
                                    <i class="fas fa-external-link-alt"></i> Visit Portal
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show temporary success message
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    });
}
</script>
@endsection
