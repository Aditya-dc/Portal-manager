@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Server</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('servers.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Servers
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

                <form action="{{ route('servers.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Server Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" required placeholder="e.g. Production Server 1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ip" class="form-label">IP Address *</label>
                                <input type="text" class="form-control" id="ip" name="ip" 
                                       value="{{ old('ip') }}" required placeholder="e.g. 192.168.1.100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Server Type *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Server Type</option>
                                    <option value="windows" {{ old('type') == 'windows' ? 'selected' : '' }}>Windows</option>
                                    <option value="linux" {{ old('type') == 'linux' ? 'selected' : '' }}>Linux</option>
                                    <option value="ubuntu" {{ old('type') == 'ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                                    <option value="centos" {{ old('type') == 'centos' ? 'selected' : '' }}>CentOS</option>
                                    <option value="debian" {{ old('type') == 'debian' ? 'selected' : '' }}>Debian</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exposed" class="form-label">Exposure *</label>
                                <select class="form-select" id="exposed" name="exposed" required>
                                    <option value="">Select Exposure</option>
                                    <option value="internal" {{ old('exposed') == 'internal' ? 'selected' : '' }}>Internal</option>
                                    <option value="external" {{ old('exposed') == 'external' ? 'selected' : '' }}>External</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Server Password *</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Enter server password">
                        <div class="form-text">This password will be encrypted and stored securely.</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('servers.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Server
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Guidelines</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-info-circle text-info"></i> Use descriptive server names</li>
                    <li><i class="fas fa-info-circle text-info"></i> Ensure IP address is accessible</li>
                    <li><i class="fas fa-info-circle text-info"></i> Choose correct server type</li>
                    <li><i class="fas fa-shield-alt text-warning"></i> Password will be encrypted</li>
                    <li><i class="fas fa-network-wired text-success"></i> Set proper exposure level</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
