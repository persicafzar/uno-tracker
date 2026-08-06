<!-- Step 3: Select Players -->
<div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-200/70 shadow-md">
    <h2 class="text-lg sm:text-xl font-black text-gray-800 mb-3 sm:mb-4 tracking-tight">مرحله ۳: انتخاب بازیکنان</h2>

    <div class="bg-gradient-to-r from-indigo-50 to-violet-50 border-2 border-indigo-200/70 rounded-2xl p-3 sm:p-4 mb-4 shadow-sm">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-indigo-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="text-xs sm:text-sm text-indigo-800 font-medium">
                <strong>نقش شما:</strong> شما به عنوان ایجادکننده بازی، <strong>داور</strong> این بازی خواهید بود. اگر خودتان را از لیست زیر انتخاب کنید، هم داور و هم بازیکن هستید.
            </div>
        </div>
    </div>

    <div class="space-y-2 sm:space-y-3 max-h-72 overflow-y-auto pr-1">
        <?php foreach ($players as $player): ?>
            <label class="flex items-center gap-3 p-2.5 sm:p-3 bg-gray-50/80 rounded-xl cursor-pointer hover:bg-indigo-50/80 transition-all duration-200 border-2 border-gray-200 hover:border-indigo-300 group">
                <input type="checkbox"
                    name="player_ids[]"
                    value="<?= $player['id'] ?>"
                    x-model="selectedPlayers"
                    class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-2 focus:ring-indigo-300 flex-shrink-0">

                <?php if (!empty($player['avatar_path'])): ?>
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full border-2 border-gray-200 overflow-hidden flex-shrink-0 group-hover:border-indigo-300 transition-all duration-200">
                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($player['avatar_path']) ?>"
                            alt="<?= htmlspecialchars($player['nickname']) ?>"
                            class="w-full h-full aspect-square rounded-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-2 border-gray-200 group-hover:border-indigo-300 transition-all duration-200 flex-shrink-0 text-lg">
                        👤
                    </div>
                <?php endif; ?>

                <div class="flex-1 min-w-0">
                    <div class="font-bold text-gray-800 text-xs sm:text-base truncate">
                        <?= htmlspecialchars($player['nickname']) ?>
                        <?php if ($player['id'] == $currentUser['id']): ?>
                            <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full mr-1.5 font-black border border-indigo-200">(شما - داور)</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-gray-500 text-xs sm:text-sm truncate"><?= htmlspecialchars($player['real_name']) ?></div>
                </div>
            </label>
        <?php endforeach; ?>
    </div>
</div>