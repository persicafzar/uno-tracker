<!-- Team Names -->
<div class="mb-5">
    <label class="text-gray-700 block mb-2 font-black text-sm sm:text-base">نام تیم‌ها</label>
    <div class="space-y-2.5">
        <template x-for="i in calculatedTeams" :key="'team-name-' + i">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-black text-sm flex-shrink-0 shadow-md" 
                     :style="'background-color: ' + getTeamColor(i-1)">
                    <span x-text="i"></span>
                </div>
                <input type="text" 
                       name="team_names[]"
                       :placeholder="'نام تیم ' + i"
                       x-model="teamNames[i-1]"
                       class="flex-1 px-3 sm:px-4 py-2.5 bg-gray-50/80 border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base font-medium">
            </div>
        </template>
    </div>
</div>