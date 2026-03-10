<?php require_once __DIR__ . '/../layout/header.php'; ?>
<header class="border-b border-orange-100 bg-white/70 backdrop-blur">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-500 text-xl text-white shadow-glow">&#x2615;</div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Cafeteria</p>
                <p class="text-xs text-slate-500">My orders</p>
            </div>
        </div>

        <nav class="hidden items-center gap-5 text-sm font-medium text-slate-600 md:flex">
            <a href="<?= url('/user') ?>" class="transition hover:text-brand-600">Home</a>
            <a href="<?= url('/user/orders') ?>" class="text-brand-700">My Orders</a>
        </nav>

        <div class="flex items-center gap-3 rounded-full border border-orange-100 bg-white px-3 py-2 text-sm shadow-sm">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">&#x1F464;</span>
            <div>
                <p class="font-medium text-slate-800"><?= htmlspecialchars($currentUser['name']) ?></p>
                <p class="text-xs text-slate-500">Office Team</p>
            </div>
            <a href="<?= url('/logout') ?>" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">Logout</a>
        </div>
    </div>
</header>

<main class="mx-auto w-full max-w-6xl px-6 py-8">
    <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Orders</p>
                <h1 class="mt-2 text-3xl font-semibold">Track your cafeteria orders</h1>
            </div>

            <form method="get" action="<?= url('/user/orders') ?>" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="date_from" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Date From</label>
                    <input id="date_from" name="date_from" type="date" value="<?= htmlspecialchars($dateFrom) ?>" class="mt-2 rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                </div>
                <div>
                    <label for="date_to" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Date To</label>
                    <input id="date_to" name="date_to" type="date" value="<?= htmlspecialchars($dateTo) ?>" class="mt-2 rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                </div>
                <button type="submit" class="rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">Filter</button>
            </form>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <div class="mt-6 space-y-4">
            <?php if (empty($orders)): ?>
                <div class="rounded-3xl border border-dashed border-orange-200 bg-orange-50 px-6 py-10 text-center text-sm text-slate-500">
                    No orders found for the selected range.
                </div>
            <?php endif; ?>

            <?php foreach ($orders as $order): ?>
                <details class="rounded-3xl border border-orange-100 bg-white p-5 shadow-sm">
                    <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3">
                        <div class="grid gap-1">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Order Date</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($order['order_date']) ?></span>
                        </div>
                        <div class="grid gap-1">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</span>
                            <span class="font-semibold <?= $order['status'] === 'Done' ? 'text-emerald-600' : ($order['status'] === 'Out for delivery' ? 'text-sky-700' : 'text-brand-700') ?>"><?= htmlspecialchars($order['status']) ?></span>
                        </div>
                        <div class="grid gap-1">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Amount</span>
                            <span class="font-semibold text-slate-800">EGP <?= htmlspecialchars(number_format((float) $order['total_amount'], 2)) ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <?php if ($order['status'] === 'Processing'): ?>
                                <form method="post" action="<?= url('/user/orders/cancel') ?>">
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <button type="submit" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100">Cancel</button>
                                </form>
                            <?php endif; ?>
                            <span class="text-sm font-medium text-slate-500">Details</span>
                        </div>
                    </summary>

                    <div class="mt-5 grid gap-4 border-t border-orange-100 pt-5 lg:grid-cols-[1.3fr_0.7fr]">
                        <div class="space-y-3">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="flex items-center justify-between rounded-2xl bg-orange-50 px-4 py-3 text-sm">
                                    <span class="font-medium text-slate-700"><?= htmlspecialchars($item['name']) ?> x<?= (int) $item['quantity'] ?></span>
                                    <span class="text-slate-600">EGP <?= htmlspecialchars(number_format($item['quantity'] * $item['unit_price'], 2)) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="rounded-2xl border border-orange-100 bg-white/80 p-4 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-800">Room:</span> <?= htmlspecialchars($order['delivery_room'] ?: '-') ?></p>
                            <p class="mt-2"><span class="font-semibold text-slate-800">Notes:</span> <?= htmlspecialchars($order['notes'] ?: 'No notes') ?></p>
                        </div>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</main>
</body>
</html>
