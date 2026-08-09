<!-- Stats Cards - نسخه‌ی فوق‌جذاب -->
<div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 via-indigo-600 to-indigo-700 rounded-2xl p-4 sm:p-5 border border-indigo-400/30 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-[1.02] group">
    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <div class="text-3xl sm:text-4xl font-black text-white drop-shadow-lg"><?= $stats['total_games'] ?></div>
            <div class="text-white/80 text-xs sm:text-sm mt-0.5 font-medium">کل بازی‌های پایان‌یافته</div>
        </div>
        <div class="text-4xl sm:text-5xl opacity-90 drop-shadow-xl group-hover:rotate-12 transition-transform duration-300">🎮</div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
</div>

<div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700 rounded-2xl p-4 sm:p-5 border border-emerald-400/30 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-[1.02] group">
    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <div class="text-3xl sm:text-4xl font-black text-white drop-shadow-lg"><?= $stats['total_wins'] ?></div>
            <div class="text-white/80 text-xs sm:text-sm mt-0.5 font-medium">کل بردها</div>
        </div>
        <div class="text-4xl sm:text-5xl opacity-90 drop-shadow-xl group-hover:scale-110 transition-transform duration-300">🏆</div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
</div>

<div class="relative overflow-hidden bg-gradient-to-br from-violet-500 via-violet-600 to-violet-700 rounded-2xl p-4 sm:p-5 border border-violet-400/30 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-[1.02] group">
    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <div class="text-3xl sm:text-4xl font-black text-white drop-shadow-lg"><?= $stats['win_rate'] ?>%</div>
            <div class="text-white/80 text-xs sm:text-sm mt-0.5 font-medium">نرخ برد</div>
        </div>
        <div class="text-4xl sm:text-5xl opacity-90 drop-shadow-xl group-hover:-rotate-12 transition-transform duration-300">📊</div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
</div>

<div class="relative overflow-hidden bg-gradient-to-br from-rose-500 via-rose-600 to-rose-700 rounded-2xl p-4 sm:p-5 border border-rose-400/30 shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-[1.02] group">
    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <div class="text-3xl sm:text-4xl font-black text-white drop-shadow-lg"><?= $stats['total_points'] ?></div>
            <div class="text-white/80 text-xs sm:text-sm mt-0.5 font-medium">امتیاز کل</div>
        </div>
        <div class="text-4xl sm:text-5xl opacity-90 drop-shadow-xl group-hover:rotate-12 transition-transform duration-300">⭐</div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
</div>