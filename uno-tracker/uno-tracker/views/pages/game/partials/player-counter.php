<!-- Player Counter -->
<div class="relative overflow-hidden bg-gradient-to-r from-indigo-50 to-violet-50 rounded-2xl p-3.5 sm:p-4.5 border-2 border-indigo-200/70 shadow-md">
    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-400/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-violet-400/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    
    <div class="relative z-10 flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-2.5">
            <span class="text-2xl sm:text-3xl">👥</span>
            <span class="font-black text-gray-800 text-sm sm:text-base">تعداد بازیکنان:</span>
        </div>
        <div class="text-left">
            <span class="text-2xl sm:text-3xl font-black text-indigo-600 drop-shadow" x-text="totalPlayers"></span>
            <span class="text-xs text-gray-500 mr-1 font-medium">نفر</span>
        </div>
    </div>
    
    <div class="relative z-10 mt-2 text-xs font-medium">
        <template x-if="gameMode === 'solo'">
            <div>
                <span x-show="totalPlayers < 2" class="text-red-600 flex items-center gap-1">
                    <span>⚠️</span> حداقل ۲ بازیکن لازم است
                </span>
                <span x-show="totalPlayers >= 2" class="text-green-600 flex items-center gap-1">
                    <span>✅</span> تعداد بازیکنان مناسب است
                </span>
            </div>
        </template>
        <template x-if="gameMode === 'friendly'">
            <div>
                <span x-show="totalPlayers < 4" class="text-red-600 flex items-center gap-1">
                    <span>⚠️</span> حداقل ۴ بازیکن لازم است
                </span>
                <span x-show="totalPlayers >= 4 && totalPlayers % 2 !== 0" class="text-orange-600 flex items-center gap-1">
                    <span>⚠️</span> تعداد فرد است، یک بازیکن دیگر اضافه کنید
                </span>
                <span x-show="totalPlayers >= 4 && totalPlayers % 2 === 0" class="text-green-600 flex items-center gap-1">
                    <span>✅</span> تعداد بازیکنان مناسب است (تیم‌ها: <span x-text="calculatedTeams" class="font-black"></span>)
                </span>
            </div>
        </template>
    </div>
</div>