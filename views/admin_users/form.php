<?php
$appSubtitle = $formHeading;
$navItems = admin_nav_items();
$activeNav = 'users';
$userRoleLabel = 'Administrator';
$headerWidthClass = 'max-w-4xl';
$mainClass = 'mx-auto w-full max-w-4xl px-6 py-8';
require_once __DIR__ . '/../layout/app_start.php';
?>
    <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Users</p>
                <h1 class="mt-2 text-3xl font-semibold"><?= htmlspecialchars($formHeading) ?></h1>
            </div>
            <a href="<?= url('/admin/users') ?>" class="rounded-2xl border border-orange-200 px-4 py-3 text-sm font-semibold text-brand-700 transition hover:bg-orange-50">Back</a>
        </div>

        <form class="mt-8 space-y-5" method="post" action="<?= $formAction ?>" enctype="multipart/form-data">
            <?php if (!empty($user)): ?>
                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
            <?php endif; ?>

            <div>
                <label for="name" class="text-sm font-semibold text-slate-700">Name</label>
                <input id="name" name="name" type="text" value="<?= htmlspecialchars($old['name'] ?? '') ?>" class="mt-2 w-full rounded-2xl border <?= !empty($errors['name']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                <?php if (!empty($errors['name'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['name']) ?></p><?php endif; ?>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="email" class="text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="mt-2 w-full rounded-2xl border <?= !empty($errors['email']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <?php if (!empty($errors['email'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['email']) ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="password" class="text-sm font-semibold text-slate-700"><?= !empty($user) ? 'Password (leave blank to keep current)' : 'Password' ?></label>
                    <input id="password" name="password" type="password" class="mt-2 w-full rounded-2xl border <?= !empty($errors['password']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <?php if (!empty($errors['password'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['password']) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="room_no" class="text-sm font-semibold text-slate-700">Room Number</label>
                    <input id="room_no" name="room_no" type="text" value="<?= htmlspecialchars($old['room_no'] ?? '') ?>" class="mt-2 w-full rounded-2xl border <?= !empty($errors['room_no']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <?php if (!empty($errors['room_no'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['room_no']) ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="ext" class="text-sm font-semibold text-slate-700">Extension</label>
                    <input id="ext" name="ext" type="text" value="<?= htmlspecialchars($old['ext'] ?? '') ?>" class="mt-2 w-full rounded-2xl border <?= !empty($errors['ext']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <?php if (!empty($errors['ext'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['ext']) ?></p><?php endif; ?>
                </div>
            </div>

            <div>
                <label for="profile_picture" class="text-sm font-semibold text-slate-700">Profile Image</label>
                <input id="profile_picture" name="profile_picture" type="file" accept=".jpg,.jpeg,.png,.gif,.webp" class="mt-2 w-full rounded-2xl border <?= !empty($errors['profile_picture']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                <?php if (!empty($errors['profile_picture'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['profile_picture']) ?></p><?php endif; ?>
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?= url('/public/' . $user['profile_picture']) ?>" alt="<?= htmlspecialchars($user['name']) ?>" class="mt-3 h-20 w-20 rounded-2xl object-cover">
                <?php endif; ?>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-700"><?= htmlspecialchars($submitLabel) ?></button>
                <a href="<?= empty($user) ? url('/admin/users/create') : url('/admin/users/edit?id=' . $user['id']) ?>" class="rounded-2xl border border-orange-200 px-5 py-3 text-sm font-semibold text-brand-700 transition hover:bg-orange-50">Reset</a>
            </div>
        </form>
    </div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
