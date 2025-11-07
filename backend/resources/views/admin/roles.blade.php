@extends('admin.layout')

@section('title', 'Quản lý Roles')

@section('content')
<div class="page-header">
    <h2>Quản lý Roles</h2>
    <p>Quản lý vai trò trong hệ thống</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Danh sách Roles</h3>
        <button class="btn btn-primary" onclick="openCreateModal()">Thêm Role</button>
    </div>

    <div class="filters">
        <input type="text" id="searchInput" placeholder="Tìm kiếm..." onkeyup="loadRoles()">
        <button class="btn btn-secondary" onclick="loadRoles()">Tìm kiếm</button>
    </div>

    <div id="rolesTable">
        <div class="loading">
            <div class="spinner"></div>
            <p>Đang tải dữ liệu...</p>
        </div>
    </div>

    <div class="pagination" id="pagination"></div>
</div>

<!-- Create/Edit Modal -->
<div class="modal" id="roleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm Role</h3>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <form id="roleForm" onsubmit="saveRole(event)">
            <input type="hidden" id="roleId">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" id="description" class="form-control">
            </div>
            <div class="form-group">
                <label>Level * (1-255)</label>
                <input type="number" id="level" class="form-control" min="1" max="255" required>
            </div>
            <div class="form-group">
                <label>Permissions</label>
                <div id="permissionsCheckboxes"></div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentPage = 1;
let permissionsList = [];

document.addEventListener('DOMContentLoaded', async function() {
    await loadPermissions();
    await loadRoles();
});

async function loadPermissions() {
    try {
        const data = await apiRequest('/roles?per_page=100'); // We'll need a permissions endpoint
        // For now, we'll load from roles endpoint and extract permissions
        // In a real scenario, you'd have a /permissions endpoint
    } catch (error) {
        console.error('Error loading permissions:', error);
    }
}

async function loadRoles(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;

    try {
        let url = `/roles?page=${page}&per_page=15`;
        if (search) url += `&search=${encodeURIComponent(search)}`;

        const data = await apiRequest(url);
        
        if (data.success) {
            renderRolesTable(data.data);
            renderPagination(data.data);
        }
    } catch (error) {
        document.getElementById('rolesTable').innerHTML = `<div class="alert alert-error">${error.message}</div>`;
    }
}

function renderRolesTable(paginationData) {
    const roles = paginationData.data || [];
    let html = '<table class="table"><thead><tr><th>ID</th><th>Name</th><th>Description</th><th>Level</th><th>Permissions</th><th>Thao tác</th></tr></thead><tbody>';
    
    if (roles.length === 0) {
        html += '<tr><td colspan="6" style="text-align: center;">Không có dữ liệu</td></tr>';
    } else {
        roles.forEach(role => {
            const permissions = (role.permissions || []).map(p => p.name || `${p.method} ${p.path}`).join(', ') || '-';
            html += `<tr>
                <td>${role.id}</td>
                <td>${role.name}</td>
                <td>${role.description || '-'}</td>
                <td>${role.level}</td>
                <td>${permissions}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editRole(${role.id})">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteRole(${role.id})">Xóa</button>
                </td>
            </tr>`;
        });
    }
    
    html += '</tbody></table>';
    document.getElementById('rolesTable').innerHTML = html;
}

function renderPagination(paginationData) {
    const pagination = document.getElementById('pagination');
    const current = paginationData.current_page;
    const last = paginationData.last_page;

    if (last <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';
    html += `<button ${current === 1 ? 'disabled' : ''} onclick="loadRoles(${current - 1})">Trước</button>`;
    html += `<span class="page-info">Trang ${current} / ${last}</span>`;
    html += `<button ${current === last ? 'disabled' : ''} onclick="loadRoles(${current + 1})">Sau</button>`;
    
    pagination.innerHTML = html;
}

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Role';
    document.getElementById('roleForm').reset();
    document.getElementById('roleId').value = '';
    document.getElementById('permissionsCheckboxes').innerHTML = '<p>Permissions sẽ được load từ API</p>';
    document.getElementById('roleModal').classList.add('active');
}

async function editRole(id) {
    try {
        const data = await apiRequest(`/roles/${id}`);
        if (data.success) {
            const role = data.data;
            document.getElementById('modalTitle').textContent = 'Sửa Role';
            document.getElementById('roleId').value = role.id;
            document.getElementById('name').value = role.name;
            document.getElementById('description').value = role.description || '';
            document.getElementById('level').value = role.level;

            // Render permissions checkboxes
            const rolePermissionIds = (role.permissions || []).map(p => p.id);
            let permissionsHtml = '<p>Permissions sẽ được load từ API</p>';
            // In a real scenario, you'd load all permissions and check the ones assigned to this role
            document.getElementById('permissionsCheckboxes').innerHTML = permissionsHtml;

            document.getElementById('roleModal').classList.add('active');
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

async function saveRole(event) {
    event.preventDefault();
    const roleId = document.getElementById('roleId').value;
    const formData = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value || null,
        level: parseInt(document.getElementById('level').value),
    };

    const permissionCheckboxes = document.querySelectorAll('input[name="permission_ids[]"]:checked');
    formData.permission_ids = Array.from(permissionCheckboxes).map(cb => parseInt(cb.value));

    try {
        let data;
        if (roleId) {
            data = await apiRequest(`/roles/${roleId}`, {
                method: 'PUT',
                body: JSON.stringify(formData)
            });
        } else {
            data = await apiRequest('/roles', {
                method: 'POST',
                body: JSON.stringify(formData)
            });
        }

        if (data.success) {
            showAlert(data.message || 'Lưu thành công');
            closeModal();
            loadRoles(currentPage);
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

async function deleteRole(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa role này?')) return;

    try {
        const data = await apiRequest(`/roles/${id}`, {
            method: 'DELETE'
        });

        if (data.success) {
            showAlert(data.message || 'Xóa thành công');
            loadRoles(currentPage);
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

function closeModal() {
    document.getElementById('roleModal').classList.remove('active');
}
</script>
@endsection

