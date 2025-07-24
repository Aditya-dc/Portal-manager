@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Servers Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('servers.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Server
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($servers->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
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
                                <strong>{{ $server->name }}</strong>
                            </td>
                            <td>
                                <code>{{ $server->ip }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($server->type) }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $server->exposed == 'external' ? 'bg-warning' : 'bg-success' }}">
                                    {{ ucfirst($server->exposed) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $server->portals->count() }} portals</span>
                                <small class="text-success">({{ $server->getActivePortalsCount() }} active)</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('servers.show', $server) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('servers.edit', $server) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('servers.destroy', $server) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Are you sure? This will delete all associated portals!')">
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
            
            {{ $servers->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-server fa-3x text-muted mb-3"></i>
                <h5>No Servers Found</h5>
                <p class="text-muted">Add your first server to get started.</p>
                <a href="{{ route('servers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Server
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
