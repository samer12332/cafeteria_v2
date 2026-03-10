<?php
$appSubtitle = 'Users management';
$navItems = admin_nav_items();
$activeNav = 'users';
$userRoleLabel = 'Administrator';
require_once __DIR__ . '/../layout/app_start.php';
?>
    <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Users</p>
                <h1 class="mt-2 text-3xl font-semibold">Manage cafeteria users</h1>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <form method="get" action="<?= url('/admin/users') ?>" class="flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm shadow-sm">
                    <span>&#x1F50D;</span>
                    <input type="search" name="search" value="<?= htmlspecialchars($search ?? '') ?>" class="w-44 bg-transparent text-sm focus:outline-none" placeholder="Search users...">
                </form>
                <a href="<?= url('/admin/users/create') ?>" class="rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">Add User</a>
            </div>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <?php if (empty($users)): ?>
            <div class="mt-6 rounded-3xl border border-dashed border-orange-200 bg-orange-50 px-6 py-10 text-center text-sm text-slate-500">
                No normal users found yet.
            </div>
        <?php else: ?>
            <div class="mt-6 overflow-hidden rounded-3xl border border-orange-100">
                <table class="min-w-full divide-y divide-orange-100 bg-white text-sm">
                    <thead class="bg-orange-50 text-left text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Image</th>
                            <th class="px-4 py-3 font-semibold">Name</th>
                            <th class="px-4 py-3 font-semibold">Email</th>
                            <th class="px-4 py-3 font-semibold">Room</th>
                            <th class="px-4 py-3 font-semibold">Extension</th>
                            <th class="px-4 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-100">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <?php if (!empty($user['profile_picture'])): ?>
                                        <img src="<?= url('/public/' . $user['profile_picture']) ?>" alt="<?= htmlspecialchars($user['name']) ?>" class="h-12 w-12 rounded-2xl object-cover">
                                    <?php else: ?>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-brand-700">&#x1F464;</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($user['name']) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($user['room_no']) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($user['ext']) ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="<?= url('/admin/users/edit?id=' . $user['id']) ?>" class="rounded-xl border border-orange-200 px-3 py-2 text-xs font-semibold text-brand-700 transition hover:bg-orange-50">Edit</a>
                                        <form method="post" action="<?= url('/admin/users/delete') ?>" onsubmit="return confirm('Delete this user?');">
                                            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                            <button type="submit" class="rounded-xl border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2 text-sm">
                <a href="<?= query_url('/admin/users', ['search' => $search ?? '', 'page' => $pagination['prev_page']]) ?>" class="rounded-xl border border-orange-200 px-3 py-2 <?= $pagination['has_prev'] ? 'text-brand-700 hover:bg-orange-50' : 'pointer-events-none text-slate-300' ?>">&lt;</a>
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="<?= query_url('/admin/users', ['search' => $search ?? '', 'page' => $i]) ?>" class="rounded-xl px-3 py-2 <?= $i === $pagination['page'] ? 'bg-brand-600 text-white' : 'border border-orange-200 text-brand-700 hover:bg-orange-50' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <a href="<?= query_url('/admin/users', ['search' => $search ?? '', 'page' => $pagination['next_page']]) ?>" class="rounded-xl border border-orange-200 px-3 py-2 <?= $pagination['has_next'] ? 'text-brand-700 hover:bg-orange-50' : 'pointer-events-none text-slate-300' ?>">&gt;</a>
            </div>
        <?php endif; ?>
    </div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
