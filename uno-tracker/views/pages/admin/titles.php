<?php

/**
 * مدیریت عناوین (Titles)
 */
?>
<div class="space-y-6" x-data="titlesManager()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">🏆</span>
                مدیریت عناوین
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">مدیریت عناوین قابل کسب توسط بازیکنان</p>
        </div>
        <button type="button"
            @click="openCreateModal()"
            class="px-4 sm:px-5 py-2 sm:py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] flex items-center gap-2 text-sm sm:text-base">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>عنوان جدید</span>
        </button>
    </div>

    <!-- Info Card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-4 shadow-md">
        <div class="flex items-start gap-3">
            <span class="text-3xl">ℹ️</span>
            <div class="flex-1">
                <h3 class="font-black text-blue-800 text-base mb-1">درباره عناوین</h3>
                <p class="text-sm text-blue-700 font-medium">
                    عناوین نشان‌های افتخاری هستند که بازیکنان با کسب رکوردهای خاص به دست می‌آورند.
                    هر عنوان می‌تواند بونوس امتیاز داشته باشد که به امتیاز هر دور اضافه می‌شود.
                </p>
            </div>
        </div>
    </div>

    <!-- Titles Table -->
    <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">آیکون</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">نام</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">توضیحات</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">شرط کسب</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">بونوس</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">وضعیت</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($titles)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-gray-500">
                                <div class="text-6xl mb-4 opacity-50">🏆</div>
                                <p class="font-bold text-base">هنوز عنوانی ایجاد نشده است</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($titles as $title): ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5 text-2xl whitespace-nowrap"><?= htmlspecialchars($title['icon'] ?? '🏆') ?></td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-800"><?= htmlspecialchars($title['name']) ?></div>
                                    <div class="text-xs text-gray-500 font-medium">کد: <?= htmlspecialchars($title['code']) ?></div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="text-sm text-gray-600 font-medium truncate max-w-[120px]"><?= htmlspecialchars($title['description'] ?? '-') ?></div>
                                </td>
                                <?php
                                $conditionLabels = [
                                    'total_games' => 'تعداد بازی',
                                    'total_wins' => 'تعداد برد',
                                    'win_streak' => 'برد متوالی',
                                    'best_streak' => 'بهترین زنجیره',
                                    'team_wins' => 'برد تیمی',
                                    'total_points' => 'امتیاز کل',
                                    'max_consecutive_wins_with_card' => 'برد متوالی با کارت',
                                ];
                                $conditionLabel = $conditionLabels[$title['condition_type']] ?? $title['condition_type'];
                                ?>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="text-sm font-bold">
                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded font-bold"><?= htmlspecialchars($conditionLabel) ?></span>
                                        <span class="text-gray-600 ml-1">≥ <?= $title['condition_value'] ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-black text-green-600 text-lg">+<?= $title['bonus_points'] ?? 0 ?></span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm <?= $title['is_active'] ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' ?>">
                                        <?= $title['is_active'] ? '✅ فعال' : '❌ غیرفعال' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <button type="button"
                                            @click="openEditModal(<?= $title['id'] ?>, '<?= htmlspecialchars(addslashes($title['code']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($title['name']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($title['description'] ?? ''), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($title['icon'] ?? ''), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($title['condition_type']), ENT_QUOTES) ?>', <?= $title['condition_value'] ?>, <?= $title['bonus_points'] ?? 0 ?>, <?= (int)$title['is_active'] ?>)"
                                            class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            @click="deleteTitle(<?= $title['id'] ?>, '<?= htmlspecialchars(addslashes($title['name']), ENT_QUOTES) ?>')"
                                            class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] flex flex-col border-2 border-gray-200/70"
            @click.away="closeModal()">
            <!-- Header -->
            <div class="px-6 py-4 border-b-2 border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-black text-gray-800 flex items-center gap-2.5 tracking-tight">
                    <span class="text-2xl" x-text="isEditing ? '✏️' : '➕'"></span>
                    <span x-text="isEditing ? 'ویرایش عنوان' : 'عنوان جدید'"></span>
                </h3>
            </div>
            <!-- Content -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form :action="isEditing ? '/admin/titles/' + titleId : '/admin/titles/create'" method="POST" id="title-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">کد یکتا <span class="text-red-500">*</span></label>
                        <input type="text" name="code" x-model="formData.code" required
                            placeholder="مثال: champion"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">نام عنوان <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="formData.name" required
                            placeholder="مثال: قهرمان"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">آیکون</label>
                        <input type="text" name="icon" x-model="formData.icon" maxlength="10"
                            placeholder="مثال: 🏆"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">توضیحات</label>
                        <textarea name="description" x-model="formData.description" rows="3"
                            placeholder="توضیحات عنوان..."
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">نوع شرط <span class="text-red-500">*</span></label>
                        <select name="condition_type" x-model="formData.condition_type" required
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                            <option value="total_games">تعداد کل بازی‌ها</option>
                            <option value="total_wins">تعداد کل بردها</option>
                            <option value="win_streak">زنجیره پیروزی</option>
                            <option value="best_streak">بهترین زنجیره</option>
                            <option value="team_wins">بردهای تیمی</option>
                            <option value="max_consecutive_wins_with_card">بردهای متوالی با کارت</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">مقدار شرط <span class="text-red-500">*</span></label>
                        <input type="number" name="condition_value" x-model="formData.condition_value" min="1" required
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">بونوس امتیاز</label>
                        <input type="number" name="bonus_points" x-model="formData.bonus_points" min="0"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                        <p class="text-xs text-gray-500 font-medium mt-1">امتیازی که به هر برد اضافه می‌شود</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">وضعیت</label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                :checked="formData.is_active == 1"
                                @change="formData.is_active = $event.target.checked ? 1 : 0"
                                class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                            <span class="mr-2 text-sm font-bold text-gray-700">فعال</span>
                        </label>
                    </div>
                </form>
            </div>
            <!-- Footer -->
            <div class="px-6 py-4 border-t-2 border-gray-200 flex-shrink-0">
                <div class="flex gap-2">
                    <button type="submit" form="title-form"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                        <span x-text="isEditing ? '💾 ذخیره تغییرات' : '➕ ایجاد عنوان'"></span>
                    </button>
                    <button type="button" @click="closeModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                        انصراف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function titlesManager() {
        return {
            showModal: false,
            isEditing: false,
            titleId: null,
            formData: {
                code: '',
                name: '',
                description: '',
                icon: '🏆',
                condition_type: 'total_wins',
                condition_value: 10,
                bonus_points: 0,
                is_active: 1,
            },

            openCreateModal() {
                this.isEditing = false;
                this.titleId = null;
                this.formData = {
                    code: '',
                    name: '',
                    description: '',
                    icon: '🏆',
                    condition_type: 'total_wins',
                    condition_value: 10,
                    bonus_points: 0,
                    is_active: 1,
                };
                this.showModal = true;
            },

            openEditModal(id, code, name, description, icon, conditionType, conditionValue, bonusPoints, isActive) {
                this.isEditing = true;
                this.titleId = id;
                this.formData = {
                    code: code,
                    name: name,
                    description: description,
                    icon: icon,
                    condition_type: conditionType,
                    condition_value: parseInt(conditionValue),
                    bonus_points: parseInt(bonusPoints),
                    is_active: parseInt(isActive),
                };
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            deleteTitle(titleId, titleName) {
                Swal.fire({
                    title: 'حذف عنوان',
                    html: `آیا مطمئن هستید که می‌خواهید عنوان <strong class="text-red-600">${titleName}</strong> را حذف کنید؟<br><br><span class="text-xs text-gray-500">این عملیات قابل بازگشت نیست!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '🗑️ بله، حذف کن',
                    cancelButtonText: 'انصراف',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/titles/${titleId}/delete`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        };
    }
</script>