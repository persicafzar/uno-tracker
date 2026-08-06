<!-- Step 1: Game Mode -->
<div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-200/70 shadow-md">
    <h2 class="text-lg sm:text-xl font-black text-gray-800 mb-3 sm:mb-4 tracking-tight">مرحله ۱: نوع بازی</h2>

    <div class="!grid !grid-cols-2 gap-3 sm:gap-4">
        <label class="cursor-pointer group">
            <input type="radio" name="game_mode" value="solo" x-model="gameMode" class="peer sr-only">
            <div class="p-3 sm:p-6 bg-gray-50/80 rounded-2xl border-3 border-transparent peer-checked:border-indigo-500 peer-checked:bg-indigo-50/80 transition-all duration-300 group-hover:shadow-md">
                <div class="text-3xl sm:text-5xl mb-2">👤</div>
                <div class="font-black text-gray-800 text-xs sm:text-base">انفرادی</div>
                <div class="text-gray-500 text-xs sm:text-sm font-medium">هر نفر مستقل بازی می‌کند</div>
            </div>
        </label>

        <label class="cursor-pointer group">
            <input type="radio" name="game_mode" value="friendly" x-model="gameMode" class="peer sr-only">
            <div class="p-3 sm:p-6 bg-gray-50/80 rounded-2xl border-3 border-transparent peer-checked:border-indigo-500 peer-checked:bg-indigo-50/80 transition-all duration-300 group-hover:shadow-md">
                <div class="text-3xl sm:text-5xl mb-2">👥</div>
                <div class="font-black text-gray-800 text-xs sm:text-base">دوستانه (تیمی)</div>
                <div class="text-gray-500 text-xs sm:text-sm font-medium">بازی تیمی با هم‌تیمی</div>
            </div>
        </label>
    </div>
</div>