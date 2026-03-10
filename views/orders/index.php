<?php
$appSubtitle = 'Orders management';
$navItems = admin_nav_items();
$activeNav = 'orders';
$userRoleLabel = 'Administrator';
require_once __DIR__ . '/../layout/app_start.php';
?>
    <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Orders</p>
                <h1 class="mt-2 text-3xl font-semibold">Manage cafeteria orders</h1>
            </div>
            <form method="get" action="<?= url('/admin/orders') ?>" class="flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 text-sm shadow-sm">
                <span>&#x1F50D;</span>
                <input type="search" name="search" value="<?= htmlspecialchars($search ?? '') ?>" class="w-44 bg-transparent text-sm focus:outline-none" placeholder="Search orders...">
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
                    No orders found yet.
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
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">User</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($order['user_name']) ?></span>
                        </div>
                        <div class="grid gap-1">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Room</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($order['delivery_room'] ?: $order['room_no']) ?></span>
                        </div>
                        <div class="grid gap-1">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Extension</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($order['ext']) ?></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $order['status'] === 'Done' ? 'bg-emerald-100 text-emerald-700' : ($order['status'] === 'Out for delivery' ? 'bg-sky-100 text-sky-700' : 'bg-orange-100 text-brand-700') ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                            <?php if ($order['status'] === 'Processing'): ?>
                                <form method="post" action="<?= url('/admin/orders/out-for-delivery') ?>">
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <button type="submit" class="rounded-2xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700">Mark Out for Delivery</button>
                                </form>
                            <?php elseif ($order['status'] === 'Out for delivery'): ?>
                                <form method="post" action="<?= url('/admin/orders/done') ?>">
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <button type="submit" class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Mark Done</button>
                                </form>
                            <?php endif; ?>
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
                            <p><span class="font-semibold text-slate-800">Amount:</span> EGP <?= htmlspecialchars(number_format((float) $order['total_amount'], 2)) ?></p>
                            <p class="mt-2"><span class="font-semibold text-slate-800">Notes:</span> <?= htmlspecialchars($order['notes'] ?: 'No notes') ?></p>
                        </div>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
