<?php

/**
 * مدیریت انواع برد
 */
?>
<div class="space-y-6" x-data="winTypesManager()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">🏆</span>
                مدیریت انواع برد
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">مدیریت انواع برد و ضرایب امتیاز</p>
        </div>
        <button type="button"
            @click="openCreateModal()"
            class="px-4 sm:px-5 py-2 sm:py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] flex items-center gap-2 text-sm sm:text-base">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>نوع برد جدید</span>
        </button>
    </div>

    <!-- Win Types Table -->
    <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">ID</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">آیکون</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">نام</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">Slug</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">ضریب امتیاز</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">وضعیت</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($winTypes)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-500">
                                <div class="text-6xl mb-4 opacity-50">🏆</div>
                                <p class="font-bold text-base">هنوز نوع بردی ایجاد نشده است</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($winTypes as $winType): ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5 text-sm font-bold text-gray-600 whitespace-nowrap">#<?= $winType['id'] ?></td>

                                <td class="px-4 py-3.5 text-2xl whitespace-nowrap"><?= htmlspecialchars($winType['icon'] ?? '') ?></td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-800"><?= htmlspecialchars($winType['name']) ?></div>
                                    <div class="text-xs text-gray-500 font-medium truncate max-w-[120px]"><?= htmlspecialchars($winType['description'] ?? '') ?></div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded font-bold"><?= htmlspecialchars($winType['slug'] ?? '') ?></code>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-black text-indigo-600 text-lg">×<?= number_format((float)$winType['score_multiplier'], 2) ?></span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <form method="POST" action="/admin/win-types/<?= $winType['id'] ?>/toggle-active" class="inline">
                                        <input type="hidden" name="is_active" value="<?= $winType['is_active'] ? '0' : '1' ?>">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                <?= $winType['is_active'] ? 'checked' : '' ?>
                                                onchange="this.form.submit()"
                                                class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                                            <span class="mr-2 text-xs font-bold <?= $winType['is_active'] ? 'text-green-600' : 'text-red-600' ?>">
                                                <?= $winType['is_active'] ? 'فعال' : 'غیرفعال' ?>
                                            </span>
                                        </label>
                                    </form>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <button type="button"
                                            data-win-type-id="<?= $winType['id'] ?>"
                                            data-win-type-name="<?= htmlspecialchars($winType['name'], ENT_QUOTES) ?>"
                                            data-win-type-slug="<?= htmlspecialchars($winType['slug'], ENT_QUOTES) ?>"
                                            data-win-type-icon="<?= htmlspecialchars($winType['icon'] ?? '', ENT_QUOTES) ?>"
                                            data-win-type-description="<?= htmlspecialchars($winType['description'] ?? '', ENT_QUOTES) ?>"
                                            data-win-type-score-multiplier="<?= $winType['score_multiplier'] ?>"
                                            data-win-type-is-active="<?= $winType['is_active'] ?>"
                                            @click="openEditModalFromData($el)"
                                            class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            @click="deleteWinType(<?= $winType['id'] ?>, '<?= htmlspecialchars(addslashes($winType['name']), ENT_QUOTES) ?>')"
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

    <!-- Modal ایجاد/ویرایش نوع برد -->
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
                    <span x-text="isEditing ? 'ویرایش نوع برد' : 'نوع برد جدید'"></span>
                </h3>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form :action="isEditing ? '/admin/win-types/' + winTypeId : '/admin/win-types/create'"
                    method="POST"
                    id="win-type-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">نام <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="formData.name" required
                                placeholder="مثال: برد معمولی"
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Slug <span class="text-red-500">*</span></label>
                            <input type="text" name="slug" x-model="formData.slug" required
                                placeholder="مثال: normal_win"
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <p class="text-xs text-gray-500 font-medium mt-1">فقط حروف کوچک، اعداد و underscore</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">آیکون</label>
                            <input type="text" name="icon" x-model="formData.icon" maxlength="10"
                                placeholder="مثال: ✅"
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">توضیحات</label>
                            <textarea name="description" x-model="formData.description" rows="3"
                                placeholder="توضیحات نوع برد..."
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ضریب امتیاز <span class="text-red-500">*</span></label>
                            <input type="number" name="score_multiplier" x-model="formData.score_multiplier" step="0.1" min="1" required
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <p class="text-xs text-gray-500 font-medium mt-1">مقدار پیش‌فرض: 1.0</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">وضعیت</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    :checked="formData.is_active == 1 || formData.is_active === true || formData.is_active === '1'"
                                    @change="formData.is_active = $event.target.checked ? 1 : 0"
                                    class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                                <span class="mr-2 text-sm font-bold text-gray-700">فعال</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t-2 border-gray-200 flex-shrink-0">
                <div class="flex gap-2">
                    <button type="submit" form="win-type-form"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                        <span x-text="isEditing ? '💾 ذخیره تغییرات' : '➕ ایجاد نوع برد'"></span>
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
    function winTypesManager() {
        return {
            showModal: false,
            isEditing: false,
            winTypeId: null,
            formData: {
                name: '',
                slug: '',
                icon: '',
                description: '',
                score_multiplier: 1.0,
                is_active: 1,
            },

            openCreateModal() {
                this.isEditing = false;
                this.winTypeId = null;
                this.formData = {
                    name: '',
                    slug: '',
                    icon: '',
                    description: '',
                    score_multiplier: 1.0,
                    is_active: 1,
                };
                this.showModal = true;
            },

            openEditModalFromData(el) {
                this.openEditModal(
                    el.dataset.winTypeId,
                    el.dataset.winTypeName,
                    el.dataset.winTypeSlug,
                    el.dataset.winTypeIcon,
                    el.dataset.winTypeDescription,
                    parseFloat(el.dataset.winTypeScoreMultiplier),
                    parseInt(el.dataset.winTypeIsActive)
                );
            },

            openEditModal(id, name, slug, icon, description, scoreMultiplier, isActive) {
                this.isEditing = true;
                this.winTypeId = id;
                this.formData = {
                    name: name,
                    slug: slug,
                    icon: icon,
                    description: description,
                    score_multiplier: scoreMultiplier,
                    is_active: isActive,
                };
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            deleteWinType(winTypeId, winTypeName) {
                Swal.fire({
                    title: 'حذف نوع برد',
                    html: `آیا مطمئن هستید که می‌خواهید نوع برد <strong class="text-red-600">${winTypeName}</strong> را حذف کنید؟<br><br><span class="text-xs text-gray-500">این عملیات قابل بازگشت نیست!</span>`,
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
                        form.action = `/admin/win-types/${winTypeId}/delete`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        };
    }
</script>