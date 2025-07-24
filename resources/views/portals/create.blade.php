@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Portal</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('portals.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Portals
            </a>
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

                <form action="{{ route('portals.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Portal Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" required placeholder="e.g. Company Dashboard">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="server_id" class="form-label">Server *</label>
                                <select class="form-select" id="server_id" name="server_id" required>
                                    <option value="">Select Server</option>
                                    @foreach($servers as $server)
                                        <option value="{{ $server->id }}" {{ old('server_id') == $server->id ? 'selected' : '' }}>
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
                               value="{{ old('url') }}" required placeholder="https://example.com">
                        <div class="form-text">Enter the complete URL including http:// or https://</div>
                    </div>

                    <div class="mb-3">
                        <label for="developed_by" class="form-label">Developed By *</label>
                        <input type="text" class="form-control" id="developed_by" name="developed_by" 
                               value="{{ old('developed_by') }}" required placeholder="Developer/Company Name">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="vapt" name="vapt" value="1" 
                                           {{ old('vapt') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vapt">
                                        VAPT Completed
                                    </label>
                                    <div class="form-text">Vulnerability Assessment and Penetration Testing</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="backup" name="backup" value="1" 
                                           {{ old('backup') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="backup">
                                        Backup Available
                                    </label>
                                    <div class="form-text">Regular backups are configured</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('portals.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Portal
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
                    <li><i class="fas fa-info-circle text-info"></i> Use descriptive portal names</li>
                    <li><i class="fas fa-link text-primary"></i> Ensure URL is accessible</li>
                    <li><i class="fas fa-server text-success"></i> Select appropriate server</li>
                    <li><i class="fas fa-user-tie text-warning"></i> Include developer information</li>
                    <li><i class="fas fa-shield-alt text-danger"></i> Mark VAPT status accurately</li>
                </ul>
            </div>
        </div>

        @if($servers->count() == 0)
        <div class="card mt-3">
            <div class="card-body">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>No servers available!</strong><br>
                    <a href="{{ route('servers.create') }}">Add a server</a> first before creating portals.
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
