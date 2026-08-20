<section class="panel mb-4">
    <form class="row g-2">
        <div class="col-md-4"><input name="search" value="<?= Security::e($_GET['search'] ?? '') ?>" class="form-control" placeholder="Search users"></div>
        <div class="col-md-3"><select name="role" class="form-select"><option value="">All roles</option><option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option><option value="staff" <?= ($_GET['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option></select></div>
        <div class="col-md-5"><button class="btn btn-primary"><i class="bi bi-search"></i></button> <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#newUser"><i class="bi bi-person-plus"></i> Add user</button></div>
    </form>
</section>
<section class="panel table-responsive"><table class="table align-middle"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Category</th><th>Assignments</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($users as $u): $stats = $u['assignmentStats'] ?? ['total' => 0, 'pending' => 0, 'inProgress' => 0, 'resolved' => 0, 'escalated' => 0]; ?>
<tr>
    <td><strong><?= Security::e($u['name'] ?? $u['email']) ?></strong></td>
    <td><?= Security::e($u['email']) ?></td>
    <td><span class="badge bg-<?= $u['role_slug'] === 'admin' ? 'danger' : 'primary' ?>"><?= Security::e($u['role_slug']) ?></span></td>
    <td><?= $u['category'] ? Security::e($u['category']) : '<span class="text-secondary">Unassigned</span>' ?></td>
    <td class="text-nowrap">
        <span class="badge bg-warning text-dark" title="Pending"><?= $stats['pending'] ?> pending</span>
        <span class="badge bg-info text-dark" title="In progress"><?= $stats['inProgress'] ?> in-progress</span>
        <span class="badge bg-success" title="Resolved"><?= $stats['resolved'] ?> resolved</span>
        <span class="badge bg-secondary" title="Escalated"><?= $stats['escalated'] ?> escalated</span>
    </td>
    <td><?= (int) $u['is_active'] === 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
    <td class="text-nowrap">
        <button type="button" class="btn btn-sm btn-outline-primary edit-user-btn" title="Edit"
                data-bs-toggle="modal" data-bs-target="#editUser"
                data-id="<?= (int) $u['id'] ?>"
                data-name="<?= Security::e($u['name'] ?? '') ?>"
                data-email="<?= Security::e($u['email']) ?>"
                data-role="<?= Security::e($u['role_slug']) ?>"
                data-category="<?= Security::e($u['category'] ?? '') ?>"
                data-active="<?= (int) $u['is_active'] ?>"><i class="bi bi-pencil"></i></button>
        <form method="post" action="/users/<?= (int) $u['id'] ?>/delete" class="d-inline" data-confirm="Deactivate <?= Security::e($u['name'] ?: $u['email']) ?>?">
            <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
            <button class="btn btn-sm btn-outline-danger" title="Deactivate"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></section>

<div class="modal fade" id="newUser" tabindex="-1"><div class="modal-dialog"><form method="post" action="/users" class="modal-content">
    <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>"><div class="modal-header"><h5>Add user</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><input name="name" class="form-control mb-2" placeholder="Name"><input type="email" name="email" class="form-control mb-2" placeholder="Email" required><input type="password" name="password" class="form-control mb-2" placeholder="Password" required><select name="role" class="form-select mb-2"><option value="staff">Staff</option><option value="admin">Administrator</option></select><select name="category" class="form-select"><option value="">Category (staff)</option><?php foreach (Feedback::CATEGORIES as $c): ?><option value="<?= Security::e($c) ?>"><?= Security::e($c) ?></option><?php endforeach; ?></select></div>
    <div class="modal-footer"><button class="btn btn-primary">Create</button></div>
</form></div></div>

<div class="modal fade" id="editUser" tabindex="-1"><div class="modal-dialog"><form method="post" action="/users" class="modal-content" id="editUserForm">
    <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>"><div class="modal-header"><h5>Edit user</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input name="name" id="editName" class="form-control mb-2" placeholder="Name">
        <input type="email" name="email" id="editEmail" class="form-control mb-2" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-2" placeholder="New password (leave blank to keep)">
        <select name="role" id="editRole" class="form-select mb-2"><option value="staff">Staff</option><option value="admin">Administrator</option></select>
        <select name="category" id="editCategory" class="form-select mb-2"><option value="">Category (staff)</option><?php foreach (Feedback::CATEGORIES as $c): ?><option value="<?= Security::e($c) ?>"><?= Security::e($c) ?></option><?php endforeach; ?></select>
        <div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" id="editActive" checked><label class="form-check-label" for="editActive">Active</label></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary">Save changes</button></div>
</form></div></div>