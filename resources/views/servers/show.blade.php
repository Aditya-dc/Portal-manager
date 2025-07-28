@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Server Details: {{ $server->ip }}</h4>
                    <div>
                        @if(auth()->user()->canModify())
                        <a href="{{ route('servers.edit', $server) }}" class="btn btn-warning">Edit Server</a>
                        @endif
                        <a href="{{ route('servers.index') }}" class="btn btn-secondary">Back to Servers</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Server Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>IP Address:</strong></td>
                                    <td>{{ $server->ip }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $server->type === 'linux' ? 'success' : 
                                            ($server->type === 'windows' ? 'info' : 
                                            ($server->type === 'ubuntu' ? 'success' : 
                                            ($server->type === 'centos' ? 'danger' : 
                                            ($server->type === 'redhat' ? 'danger' : 
                                            ($server->type === 'debian' ? 'success' : 
                                            ($server->type === 'freebsd' ? 'warning' : 
                                            ($server->type === 'macos' ? 'dark' : 'secondary'))))))) }}">
                                            {{ ucfirst($server->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Exposure:</strong></td>
                                    <td>
                                        @if($server->exposed)
                                            <span class="badge bg-{{ 
                                                str_contains(strtolower($server->exposed), 'internet') ? 'danger' : 
                                                (str_contains(strtolower($server->exposed), 'external') ? 'warning' : 'success') 
                                            }}">
                                                {{ $server->exposed }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                @if(auth()->user()->canViewPasswords())
                                <tr>
                                    <td><strong>Password:</strong></td>
                                    <td>
                                        <code>{{ $server->password }}</code>
                                        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $server->password }}')">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td><strong>Password:</strong></td>
                                    <td><span class="text-muted">Hidden (Super Admin only)</span></td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $server->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $server->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Associated Portals</h5>
                            @if($server->portals->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Portal Name</th>
                                            <th>URL</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($server->portals as $portal)
                                        <tr>
                                            <td>{{ $portal->name }}</td>
                                            <td>
                                                <a href="{{ $portal->url }}" target="_blank" class="text-decoration-none">
                                                    {{ Str::limit($portal->url, 30) }}
                                                    <i class="fas fa-external-link-alt fa-sm"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $portal->status === 'up' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($portal->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('portals.show', $portal) }}" class="btn btn-sm btn-outline-primary">
                                                    View Portal
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No portals are currently associated with this server.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
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
