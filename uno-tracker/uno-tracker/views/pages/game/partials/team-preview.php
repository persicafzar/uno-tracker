<!-- Team Preview (For Auto Algorithms) -->
<div x-show="teamAlgorithm !== 'manual' && selectedPlayers.length >= 4"
    x-cloak
    class="mt-5 bg-white rounded-2xl p-4 sm:p-6 border-2 border-indigo-200 shadow-xl"
    x-init="$watch('teamAlgorithm', () => { if (teamAlgorithm !== 'manual') loadTeamPreview() })">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3 sm:gap-0">
        <h3 class="text-base sm:text-lg font-black text-gray-800 flex items-center gap-2">
            <span class="text-2xl sm:text-3xl">🎲</span>
            پیش‌نمایش گروه‌بندی خودکار
            <span class="text-xs sm:text-sm bg-indigo-100 text-indigo-700 px-2.5 py-0.5 rounded-full font-bold border border-indigo-200">
                <span x-text="teamAlgorithm"></span>
            </span>
        </h3>
        <button type="button"
            @click="loadTeamPreview()"
            :disabled="isLoadingTeams"
            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-xs sm:text-sm font-bold transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg hover:scale-[1.02]">
            <svg x-show="!isLoadingTeams" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <svg x-show="isLoadingTeams" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="isLoadingTeams ? 'در حال بارگذاری...' : 'تازه‌سازی'"></span>
        </button>
    </div>

    <!-- Loading State -->
    <div x-show="isLoadingTeams" class="text-center py-10">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-600 border-t-transparent mx-auto"></div>
        <p class="text-gray-600 font-medium mt-4">در حال گروه‌بندی بازیکنان...</p>
    </div>

    <!-- Teams Preview -->
    <div x-show="!isLoadingTeams && previewTeams.length > 0" class="!grid !grid-cols-1 md:!grid-cols-2 gap-4">
        <template x-for="(team, index) in previewTeams" :key="'preview-team-' + index">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 transition-all hover:shadow-xl hover:scale-[1.01]"
                :style="'border-color: ' + getTeamColor(index) + '; box-shadow: 0 4px 12px ' + getTeamColor(index) + '30;'">
                
                <!-- هدر تیم با حاشیه همرنگ -->
                <h4 class="font-black mb-3 flex items-center justify-between text-sm sm:text-base pb-2.5 border-b-2"
                    :style="'color: ' + getTeamColor(index) + '; border-color: ' + getTeamColor(index) + '40'">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-black ml-2 flex-shrink-0 shadow-md"
                            :style="'background-color: ' + getTeamColor(index)">
                            <span x-text="index + 1"></span>
                        </div>
                        <span class="mr-2 truncate font-black" x-text="teamNames[index] || ('تیم ' + (index + 1))"></span>
                    </div>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-bold">
                        <span x-text="team.players.length"></span> بازیکن
                    </span>
                </h4>

                <!-- بازیکنان با حاشیه همرنگ -->
                <div class="space-y-2.5">
                    <template x-for="player in team.players" :key="'preview-player-' + player.id">
                        <div class="flex items-center gap-2.5 px-3.5 py-2.5 bg-white rounded-xl border-2 transition-all duration-200 hover:shadow-md"
                            :style="'border-color: ' + getTeamColor(index) + '40'">
                            <template x-if="player.avatar_path">
                                <div class="w-9 h-9 rounded-full border-2 border-gray-200 overflow-hidden flex-shrink-0">
                                    <img :src="'/storage/uploads/avatars/' + player.avatar_path"
                                        class="w-full h-full aspect-square rounded-full object-cover">
                                </div>
                            </template>
                            <template x-if="!player.avatar_path">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-2 border-gray-200 flex-shrink-0 text-lg">👤</div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-gray-800 text-xs sm:text-sm truncate" x-text="player.nickname"></div>
                                <div class="text-gray-500 text-xs truncate" x-text="player.real_name"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="!isLoadingTeams && previewTeams.length === 0" class="text-center py-10">
        <div class="text-5xl mb-3 opacity-50">⚠️</div>
        <p class="text-gray-500 font-medium">برای مشاهده پیش‌نمایش، حداقل ۴ بازیکن انتخاب کنید</p>
    </div>
</div>