<?php
$firstPlayerSelection = 'random';
foreach ($currentCategory as $setting) {
    if ($setting['key'] === 'first_player_selection') {
        $firstPlayerSelection = $setting['value'] ?? 'random';
        break;
    }
}
?>

<form method="POST" action="/admin/settings" id="settings-form">
    <input type="hidden" name="category" value="<?= $activeCategory ?>">
    <div class="space-y-4">

        <!-- انتخاب هوشمند بازیکن اول -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200 rounded-2xl p-4 shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                <div>
                    <label class="block text-sm font-black text-gray-800 mb-1">🎯 انتخاب هوشمند بازیکن اول</label>
                    <p class="text-xs text-gray-600 font-medium">نحوه انتخاب بازیکن شروع‌کننده بازی را مشخص کنید.</p>
                </div>
                <div class="md:col-span-2">
                    <select name="settings[first_player_selection]" class="w-full max-w-md px-3 py-2.5 border-blue-300 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 bg-white appearance-none cursor-pointer hover:border-blue-400">
                        <option value="random" <?= $firstPlayerSelection === 'random' ? 'selected' : '' ?>>🎲 تصادفی (Random)</option>
                        <option value="by_score" <?= $firstPlayerSelection === 'by_score' ? 'selected' : '' ?>>🏆 بر اساس امتیاز (Score)</option>
                        <option value="by_xp" <?= $firstPlayerSelection === 'by_xp' ? 'selected' : '' ?>>⭐ بر اساس XP</option>
                    </select>
                    <div class="mt-2 text-xs text-gray-500 font-medium space-y-1">
                        <p><strong>🎲 تصادفی:</strong> هر بازیکن شانس برابر دارد</p>
                        <p><strong>🏆 بر اساس امتیاز:</strong> بازیکن با بیشترین امتیاز کل شروع می‌کند</p>
                        <p><strong>⭐ بر اساس XP:</strong> باتجربه‌ترین بازیکن شروع می‌کند</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- سایر تنظیمات بازی -->
        <?php foreach ($currentCategory as $setting): ?>
            <?php if ($setting['key'] === 'first_player_selection') continue; ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start p-4 bg-gray-50/80 rounded-2xl border border-gray-200 hover:border-indigo-300 transition-all duration-200 hover:shadow-md">
                <div>
                    <label class="block text-sm font-black text-gray-800 mb-1"><?= htmlspecialchars(str_replace('_', ' ', $setting['key'])) ?></label>
                    <?php if (!empty($setting['description'])): ?>
                        <p class="text-xs text-gray-500 font-medium"><?= htmlspecialchars($setting['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <?php if ($setting['type'] === 'boolean'): ?>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="settings[<?= $setting['key'] ?>]" value="0">
                            <input type="checkbox" name="settings[<?= $setting['key'] ?>]" value="1" <?= $setting['value'] ? 'checked' : '' ?> class="w-5 h-5 text-<?= $catInfo['color'] ?>-600 rounded focus:ring-<?= $catInfo['color'] ?>-500">
                            <span class="text-sm font-bold text-gray-700">فعال</span>
                        </label>
                    <?php elseif ($setting['type'] === 'integer'): ?>
                        <input type="number" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php elseif ($setting['type'] === 'float'): ?>
                        <input type="number" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" step="0.01" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php else: ?>
                        <input type="text" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-6 flex items-center justify-between bg-gray-50/80 rounded-2xl p-4 border border-gray-200 shadow-sm">
        <div class="text-sm font-bold text-gray-600">💾 تغییرات ذخیره نشده دارید</div>
        <button type="submit" class="px-6 py-2.5 bg-<?= $catInfo['color'] ?>-600 hover:bg-<?= $catInfo['color'] ?>-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">💾 ذخیره تنظیمات</button>
    </div>
</form>