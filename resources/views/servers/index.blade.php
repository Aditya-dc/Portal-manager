@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Servers Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            @if(auth()->user()->canModify())
                <a href="{{ route('servers.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add Server
                </a>
            @endif
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('servers.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search by IP..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                     <select class="form-select" name="type">
                        <option value="">All Types</option>
                        <option value="linux" {{ request('type') == 'linux' ? 'selected' : '' }}>Linux</option>
                        <option value="windows" {{ request('type') == 'windows' ? 'selected' : '' }}>Windows</option>
                        <option value="ubuntu" {{ request('type') == 'ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                        <option value="centos" {{ request('type') == 'centos' ? 'selected' : '' }}>CentOS</option>
                        <option value="redhat" {{ request('type') == 'redhat' ? 'selected' : '' }}>Red Hat</option>
                        <option value="debian" {{ request('type') == 'debian' ? 'selected' : '' }}>Debian</option>
                        <option value="freebsd" {{ request('type') == 'freebsd' ? 'selected' : '' }}>FreeBSD</option>
                        <option value="macos" {{ request('type') == 'macos' ? 'selected' : '' }}>macOS</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="exposure">
                        <option value="">All Exposure</option>
                        @foreach($exposureOptions as $exposure)
                            <option value="{{ $exposure }}" {{ request('exposure') == $exposure ? 'selected' : '' }}>
                                {{ $exposure }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('servers.index') }}" class="btn btn-outline-secondary">
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
        @if($servers->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Type</th>
                            <th>Exposure</th>
                            <th>Portals</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servers as $server)
                        <tr>
                            <td>
                                <code>{{ $server->ip }}</code>
                            </td>
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
                            <td>
                                @if($server->exposed)
                                    <span class="badge bg-{{ 
                                        str_contains(strtolower($server->exposed), 'internet') ? 'warning' : 
                                        (str_contains(strtolower($server->exposed), 'external') ? 'warning' : 'success') 
                                    }}">
                                        {{ $server->exposed }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Not specified</span>
                                @endif
                            </td>
                             <td>
                                @if(auth()->user()->isSuperAdmin())
                                    <span class="badge bg-secondary">{{ $server->portals->count() }} portals</span>
                                    <small class="text-success">({{ $server->portals->where('status', 'up')->count() }} active)</small>
                                @else
                                    <span class="badge bg-secondary">{{ $server->portals->count() }} assigned</span>
                                    @if($server->portals->count() > 0)
                                        <small class="text-success">({{ $server->portals->where('status', 'up')->count() }} active)</small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('servers.show', $server) }}" class="btn btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('servers.edit', $server) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->isSuperAdmin())
                                        <form action="{{ route('servers.destroy', $server) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Are you sure? This will delete all associated portals!')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $servers->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-server fa-3x text-muted mb-3"></i>
                <h5>No Servers Found</h5>
                <p class="text-muted">
                    @if(auth()->user()->isSuperAdmin())
                        Add your first server to get started.
                    @elseif(auth()->user()->canModify())
                        No servers have been assigned to you yet.
                    @else
                        No servers are available for viewing.
                    @endif
                </p>
                @if(auth()->user()->canModify())
                    <a href="{{ route('servers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Server
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
