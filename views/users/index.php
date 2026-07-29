<section class="panel mb-4">
    <form class="row g-2">
        <div class="col-md-4"><input name="search" value="<?= Security::e($_GET['search'] ?? '') ?>" class="form-control" placeholder="Search users"></div>
        <div class="col-md-3"><select name="role" class="form-select"><option value="">All roles</option><option value="admin">Admin</option><option value="staff">Staff</option></select></div>
        <div class="col-md-5"><button class="btn btn-primary"><i class="bi bi-search"></i></button> <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#newUser"><i class="bi bi-person-plus"></i> Add user</button></div>
    </form>
</section>
<section class="panel table-responsive"><table class="table align-middle"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($users as $u): ?><tr><form method="post" action="/users/<?= (int) $u['id'] ?>">
    <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
    <td><input name="name" value="<?= Security::e($u['name']) ?>" class="form-control form-control-sm"></td>
    <td><input name="email" value="<?= Security::e($u['email']) ?>" class="form-control form-control-sm"></td>
    <td><select name="role" class="form-select form-select-sm"><option value="admin" <?= $u['role_slug'] === 'admin' ? 'selected' : '' ?>>Admin</option><option value="staff" <?= $u['role_slug'] === 'staff' ? 'selected' : '' ?>>Staff</option></select></td>
    <td><input class="form-check-input" type="checkbox" name="is_active" <?= (int) $u['is_active'] === 1 ? 'checked' : '' ?>></td>
    <td><button class="btn btn-sm btn-outline-primary">Save</button></td>
</form></tr><?php endforeach; ?>
</tbody></table></section>
<div class="modal fade" id="newUser" tabindex="-1"><div class="modal-dialog"><form method="post" action="/users" class="modal-content">
    <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>"><div class="modal-header"><h5>Add user</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><input name="name" class="form-control mb-2" placeholder="Name"><input type="email" name="email" class="form-control mb-2" placeholder="Email" required><input type="password" name="password" class="form-control mb-2" placeholder="Password" required><select name="role" class="form-select"><option value="staff">Staff</option><option value="admin">Administrator</option></select></div>
    <div class="modal-footer"><button class="btn btn-primary">Create</button></div>
</form></div></div>
