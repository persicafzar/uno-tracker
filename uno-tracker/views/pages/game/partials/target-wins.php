<!-- Step 2: Target Wins -->
<div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-200/70 shadow-md">
    <h2 class="text-lg sm:text-xl font-black text-gray-800 mb-3 sm:mb-4 tracking-tight">مرحله ۲: هدف برد</h2>

    <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
        <label class="text-gray-700 text-sm sm:text-base font-bold flex-shrink-0">بازی تا سر</label>

        <button type="button"
            @click="targetWins = Math.max(3, targetWins - 1)"
            :disabled="targetWins <= 3"
            :class="targetWins <= 3 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-red-50 hover:border-red-300 hover:text-red-600 active:scale-95'"
            class="w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center bg-white border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-xl transition-all duration-200 flex-shrink-0 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path>
            </svg>
        </button>

        <input type="number"
            name="target_wins"
            x-model.number="targetWins"
            min="3"
            max="20"
            required
            @change="targetWins = Math.max(3, Math.min(20, targetWins))"
            class="w-16 sm:w-20 px-2 sm:px-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-800 text-center font-black focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base sm:text-lg transition-all duration-200">

        <button type="button"
            @click="targetWins = Math.min(20, targetWins + 1)"
            :disabled="targetWins >= 20"
            :class="targetWins >= 20 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-green-50 hover:border-green-300 hover:text-green-600 active:scale-95'"
            class="w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center bg-white border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-xl transition-all duration-200 flex-shrink-0 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
            </svg>
        </button>

        <label class="text-gray-700 text-sm sm:text-base font-bold flex-shrink-0">برد</label>
    </div>

    <p class="text-gray-500 text-xs mt-2 font-medium">حداقل ۳ و حداکثر ۲۰ برد</p>
</div>