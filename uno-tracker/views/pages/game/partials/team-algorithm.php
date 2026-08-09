<!-- Team Building Algorithm -->
<div class="mb-5">
    <label class="text-gray-700 block mb-2 font-black text-sm sm:text-base">روش گروه‌بندی</label>
    <div class="space-y-2">
        <label class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl cursor-pointer border-2 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-200 group">
            <input type="radio" name="team_algorithm" value="manual" x-model="teamAlgorithm" class="w-5 h-5 text-indigo-600 mt-1 flex-shrink-0">
            <div>
                <span class="text-gray-800 font-bold text-xs sm:text-base">🖐️ دستی</span>
                <span class="text-gray-500 text-xs sm:text-sm block font-medium">خودم بازیکنان را در تیم‌ها قرار می‌دهم</span>
            </div>
        </label>
        <label class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl cursor-pointer border-2 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-200 group">
            <input type="radio" name="team_algorithm" value="random" x-model="teamAlgorithm" class="w-5 h-5 text-indigo-600 mt-1 flex-shrink-0">
            <div>
                <span class="text-gray-800 font-bold text-xs sm:text-base">🎲 کاملاً تصادفی</span>
                <span class="text-gray-500 text-xs sm:text-sm block font-medium">بازیکنان به صورت تصادفی پخش می‌شوند</span>
            </div>
        </label>
        <label class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl cursor-pointer border-2 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-200 group">
            <input type="radio" name="team_algorithm" value="balanced" x-model="teamAlgorithm" class="w-5 h-5 text-indigo-600 mt-1 flex-shrink-0">
            <div>
                <span class="text-gray-800 font-bold text-xs sm:text-base">⚖️ بالانس (ضعیف با قوی)</span>
                <span class="text-gray-500 text-xs sm:text-sm block font-medium">قوی‌ترین با ضعیف‌ترین هم‌تیمی می‌شود</span>
            </div>
        </label>
        <label class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl cursor-pointer border-2 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-200 group">
            <input type="radio" name="team_algorithm" value="anti_synergy" x-model="teamAlgorithm" class="w-5 h-5 text-indigo-600 mt-1 flex-shrink-0">
            <div>
                <span class="text-gray-800 font-bold text-xs sm:text-base">🔀 جداسازی یاران</span>
                <span class="text-gray-500 text-xs sm:text-sm block font-medium">یاران همیشگی را در تیم‌های مختلف قرار می‌دهد</span>
            </div>
        </label>
        <label class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl cursor-pointer border-2 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-200 group">
            <input type="radio" name="team_algorithm" value="anti_repetition" x-model="teamAlgorithm" class="w-5 h-5 text-indigo-600 mt-1 flex-shrink-0">
            <div>
                <span class="text-gray-800 font-bold text-xs sm:text-base">🔄 جلوگیری از تکرار</span>
                <span class="text-gray-500 text-xs sm:text-sm block font-medium">از تکرار ترکیب‌های قبلی جلوگیری می‌کند</span>
            </div>
        </label>
    </div>
</div>