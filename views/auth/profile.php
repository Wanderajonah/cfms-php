<div class="row g-4">
    <div class="col-lg-5">
        <section class="panel">
            <h2 class="h5 mb-3">Profile</h2>
            <form method="post" enctype="multipart/form-data" action="/profile" class="needs-validation" novalidate>
                <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
                <div class="mb-3"><label class="form-label">Name</label><input name="name" value="<?= Security::e($user['name']) ?>" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Email</label><input value="<?= Security::e($user['email']) ?>" class="form-control" disabled></div>
                <div class="mb-3"><label class="form-label">Avatar</label><input type="file" name="avatar" class="form-control" accept="image/*"></div>
                <hr>
                <div class="mb-3"><label class="form-label">Current password</label><input type="password" name="currentPassword" class="form-control"></div>
                <div class="mb-4"><label class="form-label">New password</label><input type="password" name="newPassword" minlength="6" class="form-control"></div>
                <button class="btn btn-primary">Save profile</button>
            </form>
        </section>
    </div>
</div>
