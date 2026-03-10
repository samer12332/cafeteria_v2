<?php
$appSubtitle = $formHeading;
$navItems = admin_nav_items();
$activeNav = 'products';
$userRoleLabel = 'Administrator';
$headerWidthClass = 'max-w-4xl';
$mainClass = 'mx-auto w-full max-w-4xl px-6 py-8';
require_once __DIR__ . '/../layout/app_start.php';
?>
    <div class="rounded-[2rem] bg-white/90 p-6 shadow-glow">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-600">Products</p>
                <h1 class="mt-2 text-3xl font-semibold"><?= htmlspecialchars($formHeading) ?></h1>
            </div>
            <a href="<?= url('/admin/products') ?>" class="rounded-2xl border border-orange-200 px-4 py-3 text-sm font-semibold text-brand-700 transition hover:bg-orange-50">Back</a>
        </div>

        <form class="mt-8 space-y-5" method="post" action="<?= $formAction ?>" enctype="multipart/form-data">
            <?php if (!empty($product)): ?>
                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
            <?php endif; ?>

            <div>
                <label for="name" class="text-sm font-semibold text-slate-700">Product Name</label>
                <input id="name" name="name" type="text" value="<?= htmlspecialchars($old['name'] ?? '') ?>" class="mt-2 w-full rounded-2xl border <?= !empty($errors['name']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                <?php if (!empty($errors['name'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['name']) ?></p><?php endif; ?>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="price" class="text-sm font-semibold text-slate-700">Price</label>
                    <input id="price" name="price" type="number" step="0.01" min="0.01" value="<?= htmlspecialchars((string) ($old['price'] ?? '')) ?>" class="mt-2 w-full rounded-2xl border <?= !empty($errors['price']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                    <?php if (!empty($errors['price'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['price']) ?></p><?php endif; ?>
                </div>

                <div>
                    <label for="category_id" class="text-sm font-semibold text-slate-700">Category</label>
                    <select id="category_id" name="category_id" class="mt-2 w-full rounded-2xl border <?= !empty($errors['category_id']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                        <?php if (empty($categories)): ?>
                            <option value="">No categories yet. Use Add Category below.</option>
                        <?php else: ?>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= (string) ($old['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (!empty($errors['category_id'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['category_id']) ?></p><?php endif; ?>
                </div>
            </div>

            <div>
                <label for="new_category" class="text-sm font-semibold text-slate-700">Add Category</label>
                <input id="new_category" name="new_category" type="text" value="<?= htmlspecialchars($old['new_category'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-orange-100 bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100" placeholder="Optional new category name">
                <p class="mt-1 text-xs text-slate-500">If the category list is empty, type a new category here and it will be created automatically.</p>
            </div>

            <div>
                <label for="image" class="text-sm font-semibold text-slate-700">Product Image</label>
                <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.gif,.webp" class="mt-2 w-full rounded-2xl border <?= !empty($errors['image']) ? 'border-red-500' : 'border-orange-100' ?> bg-white px-4 py-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-orange-100">
                <?php if (!empty($errors['image'])): ?><p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['image']) ?></p><?php endif; ?>
                <?php if (!empty($product['product_picture'])): ?>
                    <img src="<?= url('/public/' . $product['product_picture']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="mt-3 h-20 w-20 rounded-2xl object-cover">
                <?php endif; ?>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-700"><?= htmlspecialchars($submitLabel) ?></button>
                <a href="<?= url('/admin/products') ?>" class="rounded-2xl border border-orange-200 px-5 py-3 text-sm font-semibold text-brand-700 transition hover:bg-orange-50">Reset</a>
            </div>
        </form>
    </div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
