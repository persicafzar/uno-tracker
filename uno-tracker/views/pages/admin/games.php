<?php
use Core\JalaliDate;
$statusLabels = [
    'pending' => ['label' => 'در انتظار', 'color' => 'yellow'],
    'active' => ['label' => 'در حال بازی', 'color' => 'blue'],
    'finished' => ['label' => 'پایان یافته', 'color' => 'green'],
    'cancelled' => ['label' => 'لغو شده', 'color' => 'red'],
    'paused' => ['label' => 'متوقف', 'color' => 'orange'],
];
?>
<div class="space-y-6" x-data="gamesManager()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">🎮</span>
                مدیریت بازی‌ها
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">مجموع: <strong class="text-indigo-600"><?= number_format($total) ?></strong> بازی</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200/70 shadow-md">
        <form method="GET" action="/admin/games" class="!grid !grid-cols-1 sm:!grid-cols-2 lg:!grid-cols-4 gap-3">
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">جستجو</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                    placeholder="نام یا ID بازی"
                    class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">وضعیت</label>
                <select name="status" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>در انتظار</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>در حال بازی</option>
                    <option value="paused" <?= ($filters['status'] ?? '') === 'paused' ? 'selected' : '' ?>>متوقف</option>
                    <option value="finished" <?= ($filters['status'] ?? '') === 'finished' ? 'selected' : '' ?>>پایان یافته</option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>لغو شده</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">حالت</label>
                <select name="mode" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <option value="solo" <?= ($filters['mode'] ?? '') === 'solo' ? 'selected' : '' ?>>انفرادی</option>
                    <option value="friendly" <?= ($filters['mode'] ?? '') === 'friendly' ? 'selected' : '' ?>>تیمی</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                    🔍 فیلتر
                </button>
                <a href="/admin/games" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                    پاک کردن
                </a>
                <a href="/admin/games/export?<?= http_build_query($filters) ?>"
                    class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Bar -->
    <div x-show="selectedGames.length > 0" x-cloak
        class="bg-indigo-50 border-2 border-indigo-200 rounded-2xl p-4 shadow-md">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                    class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                <span class="text-sm font-black text-indigo-900">
                    <span x-text="selectedGames.length"></span> بازی انتخاب شده
                </span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" @click="bulkAction('finish')"
                    class="px-4 py-1.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-sm hover:shadow-md hover:scale-[1.02]">
                    ✅ پایان دادن
                </button>
                <button type="button" @click="bulkAction('cancel')"
                    class="px-4 py-1.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-sm hover:shadow-md hover:scale-[1.02]">
                    ❌ لغو کردن
                </button>
                <button type="button" @click="bulkDelete()"
                    class="px-4 py-1.5 bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-sm hover:shadow-md hover:scale-[1.02]">
                    🗑️ حذف
                </button>
                <button type="button" @click="clearSelection()"
                    class="px-4 py-1.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                    پاک کردن انتخاب
                </button>
            </div>
        </div>
    </div>

    <!-- Games Table -->
    <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 w-10">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                class="w-4 h-4 text-indigo-600 rounded">
                        </th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">ID</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">نام بازی</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">حالت</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">داور</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">برنده</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">بازیکنان</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">دور</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">وضعیت</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">تاریخ</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($games)): ?>
                        <tr>
                            <td colspan="11" class="px-4 py-16 text-center text-gray-500">
                                <div class="text-6xl mb-4 opacity-50">🔍</div>
                                <p class="font-bold text-base">بازی یافت نشد</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($games as $game): ?>
                            <?php $status = $statusLabels[$game['status']] ?? $statusLabels['pending']; ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5">
                                    <input type="checkbox" :value="<?= $game['id'] ?>" x-model="selectedGames"
                                        class="w-4 h-4 text-indigo-600 rounded">
                                </td>
                                <td class="px-4 py-3.5 text-sm font-bold text-gray-600 whitespace-nowrap">#<?= $game['id'] ?></td>
                                <td class="px-4 py-3.5">
                                    <a href="/admin/games/<?= $game['id'] ?>" class="font-bold text-gray-800 text-sm hover:text-indigo-600 transition truncate block whitespace-nowrap max-w-[120px]">
                                        <?= htmlspecialchars($game['name'] ?: 'بدون نام') ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 bg-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-100 text-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-700 rounded-full text-xs font-bold border border-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-200 shadow-sm whitespace-nowrap">
                                        <?= $game['game_mode'] === 'solo' ? '👤 انفرادی' : '👥 تیمی' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm font-bold text-gray-700 whitespace-nowrap">
                                    <?= htmlspecialchars($game['referee_name'] ?? '-') ?>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm whitespace-nowrap">
                                    <?php if (!empty($game['winner_name'])): ?>
                                        <div class="flex items-center justify-center gap-1">
                                            <?php if (empty($game['winner_user_id'])): ?>
                                                <span class="text-xs px-1.5 py-0.5 bg-gray-200 text-gray-700 rounded font-bold" title="بازیکن مهمان">👥</span>
                                            <?php endif; ?>
                                            <span class="text-yellow-600 font-black">🏆 <?= htmlspecialchars($game['winner_name']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 font-medium">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm font-bold text-gray-700 whitespace-nowrap">
                                    <?= (int)($game['total_players'] ?? 0) ?> نفر
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm font-bold text-gray-700 whitespace-nowrap">
                                    <?= (int)($game['total_rounds_played'] ?? 0) ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 bg-<?= $status['color'] ?>-100 text-<?= $status['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $status['color'] ?>-200 shadow-sm whitespace-nowrap">
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                    <?= JalaliDate::format('Y/m/d', strtotime($game['created_at'])) ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="/admin/games/<?= $game['id'] ?>"
                                            class="p-1.5 text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition"
                                            title="مشاهده جزئیات">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <?php if ($game['status'] !== 'finished' && $game['status'] !== 'cancelled'): ?>
                                            <button type="button" @click="openStatusModal(<?= $game['id'] ?>, '<?= $game['status'] ?>')"
                                                class="p-1.5 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition"
                                                title="تغییر وضعیت">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                        <form method="POST" action="/admin/games/<?= $game['id'] ?>/delete"
                                            id="delete-game-<?= $game['id'] ?>" class="inline">
                                            <button type="button"
                                                onclick="confirmDelete('آیا از حذف بازی مطمئن هستید؟', 'delete-game-<?= $game['id'] ?>')"
                                                class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition"
                                                title="حذف">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white rounded-2xl p-4 border-2 border-gray-200/70 shadow-md">
            <div class="text-sm text-gray-600 font-medium whitespace-nowrap">
                صفحه <?= $page ?> از <?= $totalPages ?>
            </div>
            <div class="flex gap-1 flex-wrap justify-center">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&<?= http_build_query($filters) ?>"
                        class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                        قبلی
                    </a>
                <?php endif; ?>
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <a href="?page=<?= $i ?>&<?= http_build_query($filters) ?>"
                        class="px-3.5 py-1.5 <?= $i === $page ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?> rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&<?= http_build_query($filters) ?>"
                        class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                        بعدی
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal تغییر وضعیت -->
    <div x-show="showStatusModal" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border-2 border-gray-200/70" @click.stop>
            <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
                <span class="text-2xl">🔄</span>
                تغییر وضعیت بازی
            </h3>
            <form method="POST" :action="'/admin/games/' + selectedGameId + '/status'">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">وضعیت جدید</label>
                    <select name="status" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                        <option value="pending">⏳ در انتظار</option>
                        <option value="active">🔴 در حال بازی</option>
                        <option value="paused">⏸️ متوقف</option>
                        <option value="finished">✅ پایان یافته</option>
                        <option value="cancelled">❌ لغو شده</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                        ذخیره
                    </button>
                    <button type="button" @click="closeStatusModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                        انصراف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function gamesManager() {
    return {
        selectedGames: [],
        selectAll: false,
        showStatusModal: false,
        selectedGameId: null,
        
        toggleSelectAll() {
            if (this.selectAll) {
                const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
                this.selectedGames = Array.from(checkboxes).map(cb => parseInt(cb.value));
            } else {
                this.selectedGames = [];
            }
        },
        
        clearSelection() {
            this.selectedGames = [];
            this.selectAll = false;
        },
        
        openStatusModal(gameId, currentStatus) {
            this.selectedGameId = gameId;
            this.showStatusModal = true;
        },
        
        closeStatusModal() {
            this.showStatusModal = false;
            this.selectedGameId = null;
        },
        
        bulkAction(action) {
            const actionLabels = { 'finish': 'پایان دادن', 'cancel': 'لغو کردن' };
            Swal.fire({
                title: `${actionLabels[action]} بازی‌ها`,
                html: `آیا مطمئن هستید که می‌خواهید <strong class="text-red-600">${this.selectedGames.length}</strong> بازی را ${actionLabels[action]} دهید؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'finish' ? '#16a34a' : '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: `بله، ${actionLabels[action]} بده`,
                cancelButtonText: 'انصراف',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/games/bulk';
                    this.selectedGames.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'game_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = action;
                    form.appendChild(actionInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        },
        
        bulkDelete() {
            Swal.fire({
                title: 'حذف بازی‌ها',
                html: `آیا مطمئن هستید که می‌خواهید <strong class="text-red-600">${this.selectedGames.length}</strong> بازی را حذف کنید؟<br><br><span class="text-xs text-gray-500">این عملیات غیرقابل بازگشت است!</span>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#1f2937',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '🗑️ بله، حذف کن',
                cancelButtonText: 'انصراف',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/games/bulk';
                    this.selectedGames.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'game_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete';
                    form.appendChild(actionInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    };
}

function confirmDelete(message, formId) {
    Swal.fire({
        title: 'حذف بازی',
        text: message,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#1f2937',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '🗑️ بله، حذف کن',
        cancelButtonText: 'انصراف',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>