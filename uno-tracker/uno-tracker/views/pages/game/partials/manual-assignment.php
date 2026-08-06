<div x-show="teamAlgorithm === 'manual'" x-cloak class="mt-5 pt-5 border-t-2 border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base sm:text-lg font-black text-gray-800 flex items-center gap-2">
            <span class="text-2xl sm:text-3xl">🖐️</span>
            گروه‌بندی دستی بازیکنان
            <span class="text-xs sm:text-sm bg-indigo-100 text-indigo-700 px-2.5 py-0.5 rounded-full font-bold border border-indigo-200">
                Drag & Drop
            </span>
        </h3>
    </div>

    <!-- Players Pool -->
    <div class="mb-5">
        <h4 class="font-black text-gray-700 mb-3 flex items-center text-sm sm:text-base">
            <span class="text-2xl sm:text-3xl ml-2">🎯</span>
            بازیکنان در دسترس
            <span class="text-xs sm:text-sm text-gray-500 mr-auto font-medium">(<span x-text="availablePlayers.length + availableGuests.length"></span> نفر)</span>
        </h4>

        <div id="players-pool"
            class="bg-gradient-to-br from-gray-50 to-gray-100/80 rounded-2xl p-3 sm:p-4 border-2 border-dashed border-gray-300 min-h-[100px] no-select hover:border-indigo-400 transition-all duration-300"
            @dragover.prevent="onDragOver($event)"
            @drop.prevent="onPoolDrop($event)">

            <div class="flex flex-wrap gap-2.5">
                <!-- کاربران ثبت‌نام شده -->
                <template x-for="playerId in availablePlayers" :key="'user-' + playerId">
                    <div draggable="true"
                        @dragstart="onDragStart($event, 'user-' + playerId)"
                        @dragend="onDragEnd($event)"
                        @touchstart="onTouchStart($event, 'user-' + playerId)"
                        @touchmove="onTouchMove($event)"
                        @touchend="onTouchEnd($event)"
                        class="sortable-item no-select group flex items-center px-3.5 py-2.5 bg-white rounded-xl border-2 border-gray-200 hover:border-indigo-400 hover:shadow-xl transition-all duration-200 cursor-move hover:scale-[1.02]">

                        <div class="drag-handle text-gray-400 group-hover:text-indigo-500 ml-2 flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 2a2 2 0 100 4 2 2 0 000-4zM13 2a2 2 0 100 4 2 2 0 000-4zM7 8a2 2 0 100 4 2 2 0 000-4zM13 8a2 2 0 100 4 2 2 0 000-4zM7 14a2 2 0 100 4 2 2 0 000-4zM13 14a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                        </div>

                        <template x-if="getPlayerAvatarUrl(playerId)">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border-2 border-gray-200 overflow-hidden ml-2 flex-shrink-0">
                                <img :src="getPlayerAvatarUrl(playerId)" class="w-full h-full aspect-square rounded-full object-cover" draggable="false">
                            </div>
                        </template>
                        <template x-if="!getPlayerAvatarUrl(playerId)">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center ml-2 border-2 border-gray-200 flex-shrink-0 text-lg">👤</div>
                        </template>

                        <span class="text-gray-800 font-bold text-xs sm:text-sm no-select" x-text="getPlayerName(playerId)"></span>
                    </div>
                </template>

                <!-- مهمان‌ها -->
                <template x-for="guest in availableGuests" :key="guest.key">
                    <div draggable="true"
                        @dragstart="onDragStart($event, guest.key)"
                        @dragend="onDragEnd($event)"
                        @touchstart="onTouchStart($event, guest.key)"
                        @touchmove="onTouchMove($event)"
                        @touchend="onTouchEnd($event)"
                        class="sortable-item no-select group flex items-center px-3.5 py-2.5 bg-white rounded-xl border-2 border-gray-200 hover:border-indigo-400 hover:shadow-xl transition-all duration-200 cursor-move hover:scale-[1.02]">

                        <div class="drag-handle text-gray-400 group-hover:text-indigo-500 ml-2 flex-shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 2a2 2 0 100 4 2 2 0 000-4zM13 2a2 2 0 100 4 2 2 0 000-4zM7 8a2 2 0 100 4 2 2 0 000-4zM13 8a2 2 0 100 4 2 2 0 000-4zM7 14a2 2 0 100 4 2 2 0 000-4zM13 14a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                        </div>

                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-indigo-300 to-purple-300 flex items-center justify-center ml-2 border-2 border-gray-200 flex-shrink-0 text-lg">👥</div>
                        <span class="text-gray-800 font-bold text-xs sm:text-sm no-select" x-text="guest.name"></span>
                        <span class="text-xs text-gray-500 mr-1 font-medium">(مهمان)</span>
                    </div>
                </template>
            </div>

            <div x-show="availablePlayers.length + availableGuests.length === 0" class="text-gray-500 text-xs sm:text-sm text-center py-8">
                <div class="text-5xl mb-3 opacity-50">✅</div>
                <div class="font-bold">همه بازیکنان به تیم‌ها اختصاص داده شده‌اند</div>
            </div>
        </div>
    </div>

    <!-- Teams با حاشیه همرنگ -->
    <div class="!grid !grid-cols-1 md:!grid-cols-2 gap-3 sm:gap-4">
        <template x-for="team in teamsData" :key="'team-zone-' + team.number">
            <div class="team-drop-zone bg-white rounded-2xl p-3 sm:p-4 border-2 transition-all min-h-[200px] sm:min-h-[220px] shadow-sm hover:shadow-xl hover:scale-[1.01]"
                :style="'border-color: ' + team.color + '; box-shadow: 0 4px 12px ' + team.color + '25;'">

                <!-- هدر تیم -->
                <h4 class="font-black mb-3 flex items-center justify-between text-sm sm:text-base pb-2.5 border-b-2"
                    :style="'color: ' + team.color + '; border-color: ' + team.color + '30'">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-black ml-2 flex-shrink-0 shadow-md"
                            :style="'background-color: ' + team.color">
                            <span x-text="team.number"></span>
                        </div>
                        <span class="mr-2 truncate font-black" x-text="team.name"></span>
                    </div>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-bold">
                        <span x-text="team.members.length"></span> / 2
                    </span>
                </h4>

                <!-- لیست بازیکنان -->
                <div :id="'team-drop-list-' + team.number"
                    class="space-y-2.5 min-h-[140px]"
                    @dragover.prevent="onDragOver($event)"
                    @dragenter.prevent="onDragEnter($event, team.number)"
                    @dragleave.prevent="onDragLeave($event, team.number)"
                    @drop.prevent="onDrop($event, team.number)">

                    <template x-for="playerKey in team.members" :key="playerKey">
                        <div draggable="true"
                            @dragstart="onDragStart($event, playerKey)"
                            @dragend="onDragEnd($event)"
                            @touchstart="onTouchStart($event, playerKey)"
                            @touchmove="onTouchMove($event)"
                            @touchend="onTouchEnd($event)"
                            class="sortable-item no-select group text-sm text-gray-700 px-3.5 py-2.5 bg-gradient-to-r from-gray-50 to-white rounded-xl flex items-center justify-between border-2 transition-all duration-200 cursor-move hover:shadow-md"
                            :style="'border-color: ' + team.color + '40; border-right: 4px solid ' + team.color">

                            <div class="flex items-center min-w-0 flex-1 gap-2">
                                <div class="drag-handle text-gray-400 group-hover:text-indigo-500 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 2a2 2 0 100 4 2 2 0 000-4zM13 2a2 2 0 100 4 2 2 0 000-4zM7 8a2 2 0 100 4 2 2 0 000-4zM13 8a2 2 0 100 4 2 2 0 000-4zM7 14a2 2 0 100 4 2 2 0 000-4zM13 14a2 2 0 100 4 2 2 0 000-4z" />
                                    </svg>
                                </div>

                                <template x-if="playerKey.startsWith('user-')">
                                    <template x-if="getPlayerAvatarUrlByKey(playerKey)">
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border-2 border-gray-200 overflow-hidden flex-shrink-0">
                                            <img :src="getPlayerAvatarUrlByKey(playerKey)" class="w-full h-full aspect-square rounded-full object-cover" draggable="false">
                                        </div>
                                    </template>
                                    <template x-if="!getPlayerAvatarUrlByKey(playerKey)">
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-2 border-gray-200 flex-shrink-0 text-lg">👤</div>
                                    </template>
                                </template>
                                <template x-if="playerKey.startsWith('guest-')">
                                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-indigo-300 to-purple-300 flex items-center justify-center border-2 border-gray-200 flex-shrink-0 text-lg">👥</div>
                                </template>

                                <span x-text="getPlayerNameByKey(playerKey)" class="truncate text-xs sm:text-sm font-bold no-select"></span>
                            </div>

                            <button @click.stop="removePlayerFromTeam(playerKey)"
                                class="text-red-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200 flex-shrink-0 ml-2 w-7 h-7 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 hover:scale-110">
                                ✕
                            </button>
                        </div>
                    </template>

                    <div x-show="team.members.length === 0"
                        class="text-gray-400 text-xs text-center py-10 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50/50">
                        <div class="text-4xl mb-2 opacity-50">🎯</div>
                        <div class="font-bold">بازیکن را اینجا رها کنید</div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<style>
    .no-select {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
        -webkit-touch-callout: none !important;
    }

    .drag-handle {
        cursor: grab;
    }
    .drag-handle:active {
        cursor: grabbing;
    }

    .sortable-item.dragging {
        opacity: 0.5;
        transform: scale(0.95);
    }

    .team-drop-zone {
        transition: all 0.3s ease;
    }
    .team-drop-zone.drag-over {
        transform: scale(1.02);
        box-shadow: 0 8px 30px rgba(99, 102, 241, 0.3) !important;
        border-color: #6366f1 !important;
        background: linear-gradient(to bottom right, #eef2ff, #e0e7ff);
    }

    #players-pool {
        transition: all 0.3s ease;
    }
    #players-pool.drag-over {
        border-color: #6366f1 !important;
        background: linear-gradient(to bottom right, #eef2ff, #e0e7ff);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }
    #players-pool:hover {
        border-color: #818cf8;
    }

    .sortable-item.long-pressing {
        animation: longPressPulse 0.4s ease-out;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.4);
    }

    @keyframes longPressPulse {
        0% { transform: scale(1); }
        50% { transform: scale(0.95); box-shadow: 0 0 0 6px rgba(99, 102, 241, 0.6); }
        100% { transform: scale(1); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.4); }
    }

    .sortable-item {
        touch-action: manipulation;
        cursor: move;
    }

    @media (max-width: 640px) {
        .sortable-item {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
            -webkit-tap-highlight-color: transparent;
        }
        .team-drop-zone {
            min-height: 180px;
        }
    }
</style>