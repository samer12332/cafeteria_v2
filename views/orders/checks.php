<?php require_once __DIR__ . '/../layout/header.php'; ?>
<header class="border-b border-orange-100 bg-white/70 backdrop-blur">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-500 text-xl text-white shadow-glow">&#x2615;</div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Cafeteria</p>
                <p class="text-xs text-slate-500">Checks and reports</p>
            </div>
        </div>

        <nav class="hidden items-center gap-5 text-sm font-medium text-slate-600 md:flex">
            <a href="<?= url('/admin') ?>" class="transition hover:text-brand-600">Home</a>
            <a href="<?= url('/admin/orders') ?>" class="transition hover:text-brand-600">Orders</a>
            <a href="<?= url('/admin/products') ?>" class="transition hover:text-brand-600">Products</a>
            <a href="<?= url('/admin/users') ?>" class="transition hover:text-brand-600">Users</a>
            <a href="<?= url('/admin/manual-order') ?>" class="transition hover:text-brand-600">Manual Order</a>
            <a href="<?= url('/admin/checks') ?>" class="text-brand-700">Checks</a>
        </nav>

        <div class="flex items-center gap-3 rounded-full border border-orange-100 bg-white px-3 py-2 text-sm shadow-sm">
            <?php if (!empty($currentUser['profile_picture'])): ?>
                <img src="<?= auth_profile_image() ?>" alt="<?= htmlspecialchars($currentUser['name']) ?>" class="h-9 w-9 rounded-full object-cover">
            <?php else: ?>
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">&#x1F464;</span>
            <?php endif; ?>
            <div>
                <p class="font-medium text-slate-800"><?= htmlspecialchars($currentUser['name']) ?></p>
                <p class="text-xs text-slate-500">Administrator</p>
            </div>
            <a href="<?= url('/logout') ?>" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">Logout</a>
        </div>
    </div>
</header>

<main class="mx-auto w-full max-w-6xl px-6 py-8">
    <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Checks</p>
                <h1 class="mt-2 text-3xl font-semibold">Financial reports by user</h1>
            </div>

            <form method="get" action="<?= url('/admin/checks') ?>" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="date_from" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Date From</label>
                    <input id="date_from" name="date_from" type="date" value="<?= htmlspecialchars($dateFrom) ?>" class="mt-2 rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                </div>
                <div>
                    <label for="date_to" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Date To</label>
                    <input id="date_to" name="date_to" type="date" value="<?= htmlspecialchars($dateTo) ?>" class="mt-2 rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                </div>
                <div>
                    <label for="user_id" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">User</label>
                    <select id="user_id" name="user_id" class="mt-2 rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                        <option value="">All users</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= (int) $user['id'] ?>" <?= (string) $selectedUserId === (string) $user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">Filter</button>
            </form>
        </div>

        <div class="mt-6 space-y-4">
            <?php if (empty($reports)): ?>
                <div class="rounded-3xl border border-dashed border-orange-200 bg-orange-50 px-6 py-10 text-center text-sm text-slate-500">
                    No report data found for the selected filters.
                </div>
            <?php endif; ?>

            <?php foreach ($reports as $report): ?>
                <details class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm">
                    <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3">
                        <div class="grid gap-1">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">User</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($report['user_name']) ?></span>
                        </div>
                        <div class="grid gap-1">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Email</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($report['email']) ?></span>
                        </div>
                        <div class="grid gap-1">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Total Amount</span>
                            <span class="font-semibold text-brand-700">EGP <?= htmlspecialchars(number_format((float) $report['total_amount'], 2)) ?></span>
                        </div>
                    </summary>

                    <div class="mt-5 space-y-4 border-t border-orange-100 pt-5">
                        <?php foreach ($report['orders'] as $order): ?>
                            <div class="rounded-2xl border border-orange-100 bg-orange-50/60 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Order Date</p>
                                        <p class="font-semibold text-slate-800"><?= htmlspecialchars($order['order_date']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Amount</p>
                                        <p class="font-semibold text-slate-800">EGP <?= htmlspecialchars(number_format((float) $order['total_amount'], 2)) ?></p>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-2">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 text-sm">
                                            <span class="font-medium text-slate-700"><?= htmlspecialchars($item['name']) ?> x<?= (int) $item['quantity'] ?></span>
                                            <span class="text-slate-600">EGP <?= htmlspecialchars(number_format($item['quantity'] * $item['unit_price'], 2)) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>

        <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2 text-sm">
                <a href="<?= query_url('/admin/checks', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'user_id' => $selectedUserId, 'page' => $pagination['prev_page']]) ?>" class="rounded-xl border border-orange-200 px-3 py-2 <?= $pagination['has_prev'] ? 'text-brand-700 hover:bg-orange-50' : 'pointer-events-none text-slate-300' ?>">&lt;</a>
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="<?= query_url('/admin/checks', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'user_id' => $selectedUserId, 'page' => $i]) ?>" class="rounded-xl px-3 py-2 <?= $i === $pagination['page'] ? 'bg-brand-600 text-white' : 'border border-orange-200 text-brand-700 hover:bg-orange-50' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <a href="<?= query_url('/admin/checks', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'user_id' => $selectedUserId, 'page' => $pagination['next_page']]) ?>" class="rounded-xl border border-orange-200 px-3 py-2 <?= $pagination['has_next'] ? 'text-brand-700 hover:bg-orange-50' : 'pointer-events-none text-slate-300' ?>">&gt;</a>
            </div>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
