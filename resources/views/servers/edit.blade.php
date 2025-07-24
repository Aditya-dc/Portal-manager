@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Server: {{ $server->name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('servers.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Servers
            </a>
            <a href="{{ route('servers.show', $server) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> View Server
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Server Information</h5>
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

                <form action="{{ route('servers.update', $server) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Server Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $server->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ip" class="form-label">IP Address *</label>
                                <input type="text" class="form-control" id="ip" name="ip" 
                                       value="{{ old('ip', $server->ip) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Server Type *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Server Type</option>
                                    <option value="windows" {{ old('type', $server->type) == 'windows' ? 'selected' : '' }}>Windows</option>
                                    <option value="linux" {{ old('type', $server->type) == 'linux' ? 'selected' : '' }}>Linux</option>
                                    <option value="ubuntu" {{ old('type', $server->type) == 'ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                                    <option value="centos" {{ old('type', $server->type) == 'centos' ? 'selected' : '' }}>CentOS</option>
                                    <option value="debian" {{ old('type', $server->type) == 'debian' ? 'selected' : '' }}>Debian</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exposed" class="form-label">Exposure *</label>
                                <select class="form-select" id="exposed" name="exposed" required>
                                    <option value="">Select Exposure</option>
                                    <option value="internal" {{ old('exposed', $server->exposed) == 'internal' ? 'selected' : '' }}>Internal</option>
                                    <option value="external" {{ old('exposed', $server->exposed) == 'external' ? 'selected' : '' }}>External</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Server Password *</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Enter new password or keep existing">
                        <div class="form-text">Leave blank to keep current password, or enter new password to update.</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('servers.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Server
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Server Details</h6>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong> {{ $server->created_at->format('M d, Y') }}</p>
                <p><strong>Last Updated:</strong> {{ $server->updated_at->format('M d, Y') }}</p>
                <p><strong>Associated Portals:</strong> {{ $server->portals->count() }}</p>
                <p><strong>Active Portals:</strong> {{ $server->getActivePortalsCount() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
