@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Server: {{ $server->ip }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('servers.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Servers
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('servers.update', $server) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="ip" class="form-label">IP Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ip') is-invalid @enderror" 
                               id="ip" name="ip" value="{{ old('ip', $server->ip) }}" required>
                        @error('ip')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Server Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Select Server Type</option>
                            <option value="linux" {{ old('type', $server->type) == 'linux' ? 'selected' : '' }}>Linux</option>
                            <option value="windows" {{ old('type', $server->type) == 'windows' ? 'selected' : '' }}>Windows</option>
                            <option value="ubuntu" {{ old('type', $server->type) == 'ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                            <option value="centos" {{ old('type', $server->type) == 'centos' ? 'selected' : '' }}>CentOS</option>
                            <option value="redhat" {{ old('type', $server->type) == 'redhat' ? 'selected' : '' }}>Red Hat</option>
                            <option value="debian" {{ old('type', $server->type) == 'debian' ? 'selected' : '' }}>Debian</option>
                            <option value="freebsd" {{ old('type', $server->type) == 'freebsd' ? 'selected' : '' }}>FreeBSD</option>
                            <option value="macos" {{ old('type', $server->type) == 'macos' ? 'selected' : '' }}>macOS</option>
                            <option value="other" {{ old('type', $server->type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="exposed" class="form-label">Server Exposure</label>
                        <input type="text" class="form-control @error('exposed') is-invalid @enderror" 
                               id="exposed" name="exposed" 
                               value="{{ old('exposed', $server->exposed) }}"
                               placeholder="e.g., Exposed to Internet, Internal Only, VPN Access, etc.">
                        @error('exposed')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            Describe how this server is exposed (e.g., "Exposed to Internet", "Exposed to Intranet", "Not published to DNS. accessed through host entries", etc.)
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Server Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" value="{{ old('password', $server->password) }}" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-text">Update the server access password</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('servers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
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
                <h6><i class="fas fa-info-circle"></i> Current Server Info</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>IP:</strong></td>
                        <td>{{ $server->ip }}</td>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong></td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($server->type) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Exposure:</strong></td>
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
                    </tr>
                    <tr>
                        <td><strong>Portals:</strong></td>
                        <td>{{ $server->portals->count() }}</td>
                    </tr>
                </table>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Security Note:</h6>
                    <p class="mb-0">Changing server details may affect associated portals.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
