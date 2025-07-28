@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>User Resource Assignments</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Assigned Servers</th>
                                    <th>Assigned Portals</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}<br><small class="text-muted">{{ $user->email }}</small></td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $user->assignedServers->count() }} servers
                                    </td>
                                    <td>
                                        {{ $user->assignedPortals->count() }} portals
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-primary" onclick="openAssignModal({{ $user->id }})">
                                                Manage Assignments
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="openUnassignModal({{ $user->id }})">
                                                Unassign Resources
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Assign Resources to <span id="userName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Servers</h6>
                            <div id="serversList" style="max-height: 300px; overflow-y: auto;">
                               
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Portals</h6>
                            <div id="portalsList" style="max-height: 300px; overflow-y: auto;">
                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Assignments</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unassign Modal -->
<div class="modal fade" id="unassignModal" tabindex="-1" aria-labelledby="unassignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unassignModalLabel">Unassign Resources from <span id="unassignUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="unassignForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6>Assigned Servers</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="selectAllUnassign('servers')">
                                    Select All
                                </button>
                            </div>
                            <div id="assignedServersList" style="max-height: 300px; overflow-y: auto;">
                               
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6>Assigned Portals</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="selectAllUnassign('portals')">
                                    Select All
                                </button>
                            </div>
                            <div id="assignedPortalsList" style="max-height: 300px; overflow-y: auto;">
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Unassign Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAssignModal(userId) {
    fetch(`/admin/user-data/${userId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('userName').textContent = data.user ? data.user.name : '';
            document.getElementById('assignForm').action = `/admin/assign/${userId}`;
            let serversHtml = '';
            data.servers.forEach(server => {
                const checked = data.assignedServerIds.includes(server.id) ? 'checked' : '';
                const serverDisplay = server.ip + (server.type ? ` (${server.type})` : '');
                serversHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="server_ids[]" value="${server.id}" ${checked}>
                        <label class="form-check-label">${serverDisplay}</label>
                    </div>
                `;
            });
            document.getElementById('serversList').innerHTML = serversHtml;
            let portalsHtml = '';
            data.portals.forEach(portal => {
                const checked = data.assignedPortalIds.includes(portal.id) ? 'checked' : '';
                portalsHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="portal_ids[]" value="${portal.id}" ${checked}>
                        <label class="form-check-label">${portal.name}</label>
                    </div>
                `;
            });
            document.getElementById('portalsList').innerHTML = portalsHtml;
            const modalElement = document.getElementById('assignModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
            alert('Error loading user data: ' + error.message);
        });
}

function openUnassignModal(userId) {
    fetch(`/admin/user-data/${userId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('unassignUserName').textContent = data.user ? data.user.name : '';
            document.getElementById('unassignForm').action = `/admin/bulk-unassign/${userId}`;
            let assignedServersHtml = '';
            if (data.assignedServerIds.length > 0) {
                data.servers.forEach(server => {
                    if (data.assignedServerIds.includes(server.id)) {
                        const serverDisplay = server.ip + (server.type ? ` (${server.type})` : '');
                        assignedServersHtml += `
                            <div class="form-check">
                                <input class="form-check-input server-unassign" type="checkbox" name="server_ids[]" value="${server.id}">
                                <label class="form-check-label">${serverDisplay}</label>
                            </div>
                        `;
                    }
                });
            } else {
                assignedServersHtml = '<p class="text-muted">No servers assigned</p>';
            }
            document.getElementById('assignedServersList').innerHTML = assignedServersHtml;
            let assignedPortalsHtml = '';
            if (data.assignedPortalIds.length > 0) {
                data.portals.forEach(portal => {
                    if (data.assignedPortalIds.includes(portal.id)) {
                        assignedPortalsHtml += `
                            <div class="form-check">
                                <input class="form-check-input portal-unassign" type="checkbox" name="portal_ids[]" value="${portal.id}">
                                <label class="form-check-label">${portal.name}</label>
                            </div>
                        `;
                    }
                });
            } else {
                assignedPortalsHtml = '<p class="text-muted">No portals assigned</p>';
            }
            document.getElementById('assignedPortalsList').innerHTML = assignedPortalsHtml;
            const modalElement = document.getElementById('unassignModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
            alert('Error loading user data: ' + error.message);
        });
}

function selectAllUnassign(type) {
    const checkboxes = document.querySelectorAll(`.${type.slice(0, -1)}-unassign`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}
</script>
@endsection
