@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Server</h1>
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
                <form method="POST" action="{{ route('servers.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="ip" class="form-label">IP Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ip') is-invalid @enderror" 
                               id="ip" name="ip" value="{{ old('ip') }}" required>
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
                            <option value="linux" {{ old('type') == 'linux' ? 'selected' : '' }}>Linux</option>
                            <option value="windows" {{ old('type') == 'windows' ? 'selected' : '' }}>Windows</option>
                            <option value="ubuntu" {{ old('type') == 'ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                            <option value="centos" {{ old('type') == 'centos' ? 'selected' : '' }}>CentOS</option>
                            <option value="redhat" {{ old('type') == 'redhat' ? 'selected' : '' }}>Red Hat</option>
                            <option value="debian" {{ old('type') == 'debian' ? 'selected' : '' }}>Debian</option>
                            <option value="freebsd" {{ old('type') == 'freebsd' ? 'selected' : '' }}>FreeBSD</option>
                            <option value="macos" {{ old('type') == 'macos' ? 'selected' : '' }}>macOS</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
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
                               value="{{ old('exposed') }}"
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
                                   id="password" name="password" value="{{ old('password') }}" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-text">Enter the server access password</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('servers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Server
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-info-circle"></i> Server Information</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6>Required Fields:</h6>
                    <ul class="mb-0">
                        <li><strong>IP Address:</strong> Valid IPv4 address</li>
                        <li><strong>Type:</strong> Operating system type</li>
                        <li><strong>Password:</strong> Server access credentials</li>
                    </ul>
                </div>
                
                <div class="alert alert-success">
                    <h6><i class="fas fa-info-circle"></i> Optional Fields:</h6>
                    <ul class="mb-0">
                        <li><strong>Exposure:</strong> Custom description of network accessibility (e.g., "Exposed to Internet", "VPN Only", etc.)</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Security Note:</h6>
                    <p class="mb-0">Server passwords are encrypted and only visible to Super Admin users.</p>
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
