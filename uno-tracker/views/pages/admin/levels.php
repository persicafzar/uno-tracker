<?php

/**
 * 🎯 صفحه مدیریت سطوح (Player Levels)
 */

use Core\JalaliDate;

$levels = $levels ?? [];

?>

<div class="space-y-6" x-data="levelsManager()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">🎯</span>
                مدیریت سطوح
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">مدیریت سطوح بازیکنان و محدوده XP هر سطح</p>
        </div>
        <button type="button"
            @click="openCreateModal()"
            class="px-4 sm:px-5 py-2 sm:py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] flex items-center gap-2 text-sm sm:text-base">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>سطح جدید</span>
        </button>
    </div>

    <!-- Statistics Cards - کوچک‌تر در موبایل -->
    <div class="!grid !grid-cols-1 sm:!grid-cols-2 lg:!grid-cols-3 gap-2 sm:gap-4">
        <!-- کل سطوح -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl p-3 sm:p-5 border-2 border-indigo-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-12 sm:w-16 h-12 sm:h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center gap-2 sm:gap-3">
                <div class="w-9 h-9 sm:w-12 sm:h-12 bg-indigo-600/20 rounded-xl flex items-center justify-center text-xl sm:text-3xl flex-shrink-0">🎯</div>
                <div>
                    <div class="text-xl sm:text-3xl font-black text-gray-800"><?= count($levels) ?></div>
                    <div class="text-[10px] sm:text-sm font-medium text-gray-600">کل سطوح</div>
                </div>
            </div>
        </div>

        <!-- حداکثر XP -->
        <div class="relative overflow-hidden bg-gradient-to-br from-green-100 to-emerald-200 rounded-2xl p-3 sm:p-5 border-2 border-green-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-12 sm:w-16 h-12 sm:h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center gap-2 sm:gap-3">
                <div class="w-9 h-9 sm:w-12 sm:h-12 bg-green-600/20 rounded-xl flex items-center justify-center text-xl sm:text-3xl flex-shrink-0">⭐</div>
                <div>
                    <div class="text-xl sm:text-3xl font-black text-gray-800"><?= count($levels) > 0 ? max(array_column($levels, 'max_xp')) : 0 ?></div>
                    <div class="text-[10px] sm:text-sm font-medium text-gray-600">حداکثر XP</div>
                </div>
            </div>
        </div>

        <!-- کل بازیکنان -->
        <div class="relative overflow-hidden bg-gradient-to-br from-purple-100 to-violet-200 rounded-2xl p-3 sm:p-5 border-2 border-purple-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-12 sm:w-16 h-12 sm:h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center gap-2 sm:gap-3">
                <div class="w-9 h-9 sm:w-12 sm:h-12 bg-purple-600/20 rounded-xl flex items-center justify-center text-xl sm:text-3xl flex-shrink-0">👥</div>
                <div>
                    <div class="text-xl sm:text-3xl font-black text-gray-800"><?= array_sum(array_column($levels, 'users_count')) ?></div>
                    <div class="text-[10px] sm:text-sm font-medium text-gray-600">کل بازیکنان</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Levels Table -->
    <?php if (empty($levels)): ?>
        <div class="bg-white rounded-2xl p-8 sm:p-16 border-2 border-gray-200/70 shadow-md text-center">
            <div class="text-5xl sm:text-7xl mb-4 opacity-50">🎯</div>
            <h3 class="text-xl sm:text-2xl font-black text-gray-800 mb-2">هیچ سطحی تعریف نشده است</h3>
            <p class="text-gray-500 font-medium mb-4">برای شروع، اولین سطح را ایجاد کنید</p>
            <button type="button"
                @click="openCreateModal()"
                class="px-5 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] text-sm sm:text-base">
                ایجاد اولین سطح
            </button>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">ID</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">سطح</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">عنوان</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">آیکون</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">رنگ</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">محدوده XP</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">بازیکنان</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($levels as $level): ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5 text-sm font-bold text-gray-600 whitespace-nowrap">#<?= $level['id'] ?></td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-black text-lg shadow-sm"
                                        style="background-color: <?= htmlspecialchars($level['color']) ?>">
                                        <?= $level['level'] ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-bold text-gray-800"><?= htmlspecialchars($level['title'] ?? 'بدون عنوان') ?></span>
                                </td>
                                <td class="px-4 py-3.5 text-2xl whitespace-nowrap"><?= htmlspecialchars($level['icon'] ?? '⭐') ?></td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 shadow-sm"
                                            style="background-color: <?= htmlspecialchars($level['color']) ?>"></div>
                                        <span class="text-sm font-medium text-gray-600"><?= htmlspecialchars($level['color']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="text-sm font-bold">
                                        <span class="font-mono text-green-600"><?= number_format($level['min_xp']) ?></span>
                                        <span class="text-gray-400 mx-1">تا</span>
                                        <span class="font-mono text-green-600"><?= number_format($level['max_xp']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold border border-indigo-200 shadow-sm">
                                        👥 <?= $level['users_count'] ?? 0 ?> بازیکن
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <button type="button"
                                            @click='openEditModal(<?= htmlspecialchars(json_encode($level)) ?>)'
                                            class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition"
                                            title="ویرایش">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            @click='deleteLevel(<?= json_encode([
                                                                    "id" => $level["id"],
                                                                    "level" => $level["level"],
                                                                    "title" => $level["title"] ?? "",
                                                                    "users_count" => $level["users_count"] ?? 0
                                                                ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) ?>)'
                                            class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition <?= ($level['users_count'] ?? 0) > 0 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            title="<?= ($level['users_count'] ?? 0) > 0 ? 'نمی‌توان حذف کرد - ' . ($level['users_count'] ?? 0) . ' بازیکن در این سطح' : 'حذف' ?>">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

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
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] flex flex-col border-2 border-gray-200/70"
            @click.away="closeModal()">
            <!-- Header -->
            <div class="px-6 py-4 border-b-2 border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-800 flex items-center gap-2.5 tracking-tight">
                        <span class="text-2xl" x-text="isEditing ? '✏️' : '➕'"></span>
                        <span x-text="isEditing ? 'ویرایش سطح' : 'ایجاد سطح جدید'"></span>
                    </h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form :action="getFormAction()" method="POST" id="level-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">شماره سطح <span class="text-red-500">*</span></label>
                        <input type="number" name="level" x-model="formData.level" min="1" required
                            :disabled="isEditing"
                            placeholder="مثال: 1"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <p class="text-xs text-gray-500 font-medium mt-1">شماره سطح باید حداقل ۱ باشد</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">عنوان سطح <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" required
                            placeholder="مثال: تازه‌کار"
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">آیکون <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="icon" x-model="formData.icon" required
                                placeholder="مثال: ⭐"
                                class="flex-1 px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <span class="text-3xl" x-text="formData.icon || '⭐'"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رنگ <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color" x-model="formData.color"
                                class="w-14 h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                            <input type="text" x-model="formData.color"
                                placeholder="#6366f1"
                                class="flex-1 px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">حداقل XP <span class="text-red-500">*</span></label>
                            <input type="number" name="min_xp" x-model="formData.min_xp" min="0" required
                                placeholder="0"
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">حداکثر XP <span class="text-red-500">*</span></label>
                            <input type="number" name="max_xp" x-model="formData.max_xp" min="0" required
                                placeholder="99"
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                        </div>
                    </div>
                    <div class="bg-gray-50/80 rounded-2xl p-4 border-2 border-gray-200">
                        <div class="text-xs font-bold text-gray-500 mb-2">پیش‌نمایش:</div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-black text-lg shadow-sm"
                                :style="'background-color: ' + formData.color">
                                <span x-text="formData.level || '?'"></span>
                            </div>
                            <div>
                                <div class="font-black text-gray-800">
                                    <span x-text="formData.icon || '⭐'"></span>
                                    <span x-text="formData.title || 'بدون عنوان'"></span>
                                </div>
                                <div class="text-xs font-medium text-gray-500">
                                    XP: <span x-text="formData.min_xp || 0"></span> تا <span x-text="formData.max_xp || 0"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t-2 border-gray-200 flex-shrink-0">
                <div class="flex gap-2">
                    <button type="submit" form="level-form"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                        <span x-text="isEditing ? '💾 ذخیره تغییرات' : '➕ ایجاد سطح'"></span>
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
    function levelsManager() {
        return {
            showModal: false,
            isEditing: false,
            formData: {
                id: null,
                level: null,
                title: '',
                icon: '⭐',
                color: '#6366f1',
                min_xp: 0,
                max_xp: 99
            },

            openCreateModal() {
                this.isEditing = false;
                this.formData = {
                    id: null,
                    level: null,
                    title: '',
                    icon: '⭐',
                    color: '#6366f1',
                    min_xp: 0,
                    max_xp: 99
                };
                this.showModal = true;
            },

            openEditModal(level) {
                this.isEditing = true;
                this.formData = {
                    id: level.id,
                    level: level.level,
                    title: level.title || '',
                    icon: level.icon || '⭐',
                    color: level.color || '#6366f1',
                    min_xp: parseInt(level.min_xp) || 0,
                    max_xp: parseInt(level.max_xp) || 0
                };
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            getFormAction() {
                if (this.isEditing && this.formData.id) {
                    return '/admin/levels/' + this.formData.id;
                }
                return '/admin/levels/create';
            },

            deleteLevel(data) {
                const {
                    id,
                    level,
                    title,
                    users_count
                } = data;
                if (users_count > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا',
                        text: `نمی‌توان این سطح را حذف کرد زیرا ${users_count} بازیکن در این سطح قرار دارند`,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'باشه'
                    });
                    return;
                }
                Swal.fire({
                    title: 'حذف سطح',
                    html: `آیا مطمئن هستید که می‌خواهید سطح <strong>${level}</strong> (${title}) را حذف کنید؟<br><br><span class="text-xs text-red-600">این عملیات غیرقابل بازگشت است!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '🗑️ بله، حذف کن',
                    cancelButtonText: 'انصراف',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/admin/levels/' + id + '/delete';
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        };
    }
</script>