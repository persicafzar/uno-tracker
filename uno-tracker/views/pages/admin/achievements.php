<?php
$rarityLabels = [
    'common' => ['label' => 'معمولی', 'color' => 'gray'],
    'rare' => ['label' => 'کمیاب', 'color' => 'blue'],
    'epic' => ['label' => 'حماسی', 'color' => 'purple'],
    'legendary' => ['label' => 'افسانه‌ای', 'color' => 'yellow'],
];

$categoryLabels = [
    'general' => '🎮 عمومی',
    'winning' => '🏆 پیروزی',
    'streak' => '🔥 زنجیره پیروزی',
    'team' => '👥 تیمی',
    'special' => '⭐ ویژه',
];

$conditionTypeLabels = [
    'total_games' => 'تعداد بازی',
    'total_wins' => 'تعداد برد',
    'best_streak' => 'بهترین استریک',
    'current_streak' => 'استریک فعلی',
    'team_games' => 'بازی تیمی',
    'team_wins' => 'برد تیمی',
    'total_points' => 'امتیاز کل',
];
?>

<div class="space-y-6" x-data="achievementsManager()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">🏅</span> مدیریت نشان‌ها
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">مجموع: <strong class="text-indigo-600"><?= count($achievements) ?></strong> نشان</p>
        </div>
        <button type="button" @click="openCreateModal()"
            class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            <span>نشان جدید</span>
        </button>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <span class="text-green-700 font-bold"><?= htmlspecialchars($_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-300 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span class="text-red-700 font-bold"><?= htmlspecialchars($_SESSION['error']) ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Achievements Grid -->
    <div class="!grid !grid-cols-1 md:!grid-cols-2 lg:!grid-cols-3 gap-4">
        <?php foreach ($achievements as $achievement): ?>
            <?php $rarity = $rarityLabels[$achievement['rarity']] ?? $rarityLabels['common']; ?>
            <div class="relative overflow-hidden bg-white rounded-2xl border-2 border-<?= $rarity['color'] ?>-200 shadow-md hover:shadow-xl transition-all duration-300 hover:scale-[1.02] group">
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="bg-<?= $rarity['color'] ?>-50 px-4 py-3 border-b-2 border-<?= $rarity['color'] ?>-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2"><span class="text-2xl"><?= $achievement['icon'] ?></span><div><div class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($achievement['name']) ?></div><div class="text-xs font-medium text-gray-500"><?= $categoryLabels[$achievement['category']] ?? $achievement['category'] ?></div></div></div>
                        <span class="px-2.5 py-0.5 bg-<?= $rarity['color'] ?>-100 text-<?= $rarity['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $rarity['color'] ?>-200 shadow-sm"><?= $rarity['label'] ?></span>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <p class="text-xs font-medium text-gray-600"><?= htmlspecialchars($achievement['description'] ?? '-') ?></p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-gray-50/80 rounded-2xl p-2 border border-gray-200"><div class="font-bold text-gray-500">شرط</div><div class="font-bold text-gray-800"><?= $conditionTypeLabels[$achievement['condition_type']] ?? $achievement['condition_type'] ?></div></div>
                        <div class="bg-gray-50/80 rounded-2xl p-2 border border-gray-200"><div class="font-bold text-gray-500">مقدار</div><div class="font-bold text-gray-800"><?= $achievement['condition_value'] ?></div></div>
                        <div class="bg-gray-50/80 rounded-2xl p-2 border border-gray-200"><div class="font-bold text-gray-500">XP پاداش</div><div class="font-bold text-indigo-600">+<?= $achievement['xp_reward'] ?></div></div>
                        <div class="bg-gray-50/80 rounded-2xl p-2 border border-gray-200"><div class="font-bold text-gray-500">کسب شده</div><div class="font-bold text-green-600"><?= (int)$achievement['unlocked_count'] ?> نفر</div></div>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t-2 border-gray-200">
                        <form method="POST" action="/admin/achievements/<?= $achievement['id'] ?>/toggle-active" class="inline">
                            <input type="hidden" name="is_active" value="<?= $achievement['is_active'] ? '0' : '1' ?>">
                            <button type="button" onclick="toggleAchievementStatus(<?= $achievement['id'] ?>, <?= $achievement['is_active'] ? 1 : 0 ?>, '<?= htmlspecialchars($achievement['name']) ?>')"
                                class="text-xs font-bold <?= $achievement['is_active'] ? 'text-green-600 hover:text-green-700' : 'text-red-600 hover:text-red-700' ?> transition">
                                <?= $achievement['is_active'] ? '✅ فعال' : '❌ غیرفعال' ?>
                            </button>
                        </form>
                        <div class="flex items-center gap-1">
                            <button type="button" @click='openEditModal(<?= htmlspecialchars(json_encode($achievement)) ?>)' class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="ویرایش">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button type="button" @click="deleteAchievement(<?= $achievement['id'] ?>, '<?= htmlspecialchars(addslashes($achievement['name'])) ?>')" class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="حذف">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] flex flex-col border-2 border-gray-200/70" @click.away="closeModal()">
            <div class="px-6 py-4 border-b-2 border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-800 flex items-center gap-2.5 tracking-tight"><span class="text-2xl" x-text="isEditing ? '✏️' : '➕'"></span><span x-text="isEditing ? 'ویرایش نشان' : 'نشان جدید'"></span></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition p-1"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form :action="isEditing ? '/admin/achievements/' + formData.id : '/admin/achievements/create'" method="POST" id="achievement-form" class="space-y-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">کد یکتا <span class="text-red-500">*</span></label><input type="text" name="code" x-model="formData.code" required placeholder="مثال: first_game" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"><p class="text-xs text-gray-500 font-medium mt-1">فقط حروف کوچک، اعداد و underscore</p></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">نام نشان <span class="text-red-500">*</span></label><input type="text" name="name" x-model="formData.name" required placeholder="مثال: اولین قدم" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">توضیحات</label><textarea name="description" x-model="formData.description" rows="2" placeholder="توضیحات نشان..." class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"></textarea></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-2">آیکون (ایموجی)</label><div class="flex items-center gap-2"><input type="text" name="icon" x-model="formData.icon" maxlength="10" placeholder="🏅" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"><span class="text-2xl" x-text="formData.icon || '🏅'"></span></div></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-2">دسته‌بندی</label><select name="category" x-model="formData.category" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white"><option value="general">🎮 عمومی</option><option value="winning">🏆 پیروزی</option><option value="streak">🔥 زنجیره پیروزی</option><option value="team">👥 تیمی</option><option value="special">⭐ ویژه</option></select></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-2">کمیابی</label><select name="rarity" x-model="formData.rarity" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white"><option value="common">معمولی</option><option value="rare">کمیاب</option><option value="epic">حماسی</option><option value="legendary">افسانه‌ای</option></select></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-2">XP پاداش</label><input type="number" name="xp_reward" x-model="formData.xp_reward" min="0" required class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-bold text-gray-700 mb-2">نوع شرط <span class="text-red-500">*</span></label><select name="condition_type" x-model="formData.condition_type" required class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white"><option value="total_games">تعداد بازی</option><option value="total_wins">تعداد برد</option><option value="best_streak">بهترین استریک</option><option value="current_streak">استریک فعلی</option><option value="team_games">بازی تیمی</option><option value="team_wins">برد تیمی</option><option value="total_points">امتیاز کل</option></select></div>
                        <div><label class="block text-sm font-bold text-gray-700 mb-2">مقدار شرط <span class="text-red-500">*</span></label><input type="number" name="condition_value" x-model="formData.condition_value" min="1" required class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"></div>
                    </div>
                    <div><label class="inline-flex items-center gap-2 cursor-pointer"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" :checked="formData.is_active == 1" @change="formData.is_active = $event.target.checked ? 1 : 0" class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500"><span class="text-sm font-bold text-gray-700">فعال</span></label></div>
                </form>
            </div>
            <div class="px-6 py-4 border-t-2 border-gray-200 flex-shrink-0">
                <div class="flex gap-2">
                    <button type="submit" form="achievement-form" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]"><span x-text="isEditing ? '💾 ذخیره تغییرات' : '➕ ایجاد نشان'"></span></button>
                    <button type="button" @click="closeModal()" class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-bold transition-all duration-200 shadow-sm hover:shadow-md">انصراف</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function achievementsManager() {
    return {
        showModal: false, isEditing: false,
        formData: { id: null, code: '', name: '', description: '', icon: '🏅', category: 'general', rarity: 'common', xp_reward: 10, condition_type: 'total_games', condition_value: 1, is_active: 1 },
        openCreateModal() { this.isEditing = false; this.formData = { id: null, code: '', name: '', description: '', icon: '🏅', category: 'general', rarity: 'common', xp_reward: 10, condition_type: 'total_games', condition_value: 1, is_active: 1 }; this.showModal = true; },
        openEditModal(a) { this.isEditing = true; this.formData = { id: a.id, code: a.code || '', name: a.name || '', description: a.description || '', icon: a.icon || '🏅', category: a.category || 'general', rarity: a.rarity || 'common', xp_reward: parseInt(a.xp_reward) || 10, condition_type: a.condition_type || 'total_games', condition_value: parseInt(a.condition_value) || 1, is_active: parseInt(a.is_active) || 0 }; this.showModal = true; },
        closeModal() { this.showModal = false; },
        deleteAchievement(id, name) {
            Swal.fire({ title: 'حذف نشان', html: `آیا از حذف نشان <strong class="text-red-600">${name}</strong> مطمئن هستید؟<br><br><span class="text-xs text-gray-500">این عملیات غیرقابل بازگشت است و تمام پیشرفت‌های کاربران نیز حذف می‌شود.</span>`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280', confirmButtonText: '🗑️ بله، حذف کن', cancelButtonText: 'انصراف', reverseButtons: true })
            .then((result) => { if (result.isConfirmed) { const form = document.createElement('form'); form.method = 'POST'; form.action = `/admin/achievements/${id}/delete`; document.body.appendChild(form); form.submit(); } });
        }
    };
}

function toggleAchievementStatus(id, currentStatus, achievementName) {
    const newStatus = currentStatus === 1 ? 0 : 1;
    const actionText = newStatus === 1 ? 'فعال' : 'غیرفعال';
    const icon = newStatus === 1 ? '✅' : '❌';
    Swal.fire({
        title: 'تغییر وضعیت نشان',
        html: `آیا از <strong>${actionText}</strong> کردن نشان <strong>"${achievementName}"</strong> مطمئن هستید؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: newStatus === 1 ? '#16a34a' : '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `${icon} بله، ${actionText} کن`,
        cancelButtonText: 'انصراف',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/achievements/' + id + '/toggle-active';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'is_active';
            input.value = newStatus;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>