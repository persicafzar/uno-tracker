<!-- Add Guest Player -->
<div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm">
    <label class="text-gray-700 block mb-2 font-medium text-sm sm:text-base">افزودن بازیکن مهمان (بدون حساب کاربری)</label>
    <div class="flex space-x-2 space-x-reverse">
        <input type="text" 
               x-model="newGuestName"
               @keyup.enter="addGuest"
               placeholder="نام بازیکن مهمان"
               class="flex-1 px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-50 border-2 border-gray-200 rounded-lg text-gray-800 focus:outline-none focus:border-indigo-500 text-sm sm:text-base">
        <!-- 🆕 تغییر رنگ از سبز به indigo -->
        <button type="button" 
                @click="addGuest"
                class="px-3 sm:px-4 py-2 sm:py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm sm:text-base flex-shrink-0">
            ➕ افزودن
        </button>
    </div>
    
    <template x-if="guestPlayers.length > 0">
        <div class="mt-3 space-y-2">
            <template x-for="(guest, index) in guestPlayers" :key="'guest-' + index">
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center ml-2 flex-shrink-0 border border-gray-400">👥</div>
                        <span class="text-gray-800 text-sm truncate" x-text="guest"></span>
                        <span class="text-xs text-gray-500 mr-2 flex-shrink-0">(مهمان)</span>
                    </div>
                    <button type="button" 
                            @click="removeGuest(index)" 
                            class="text-red-600 hover:text-red-700 flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center hover:bg-red-50 transition"
                            title="حذف بازیکن مهمان">✕</button>
                </div>
            </template>
        </div>
    </template>

    <input type="hidden" name="guest_players" :value="JSON.stringify(guestPlayers)">
</div>