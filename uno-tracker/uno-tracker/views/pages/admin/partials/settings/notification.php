<?php
// اسکن داینامیک صداها با پسوند
$soundsDir = PUBLIC_PATH . '/assets/sounds';
$availableSounds = [];
$extensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];

if (is_dir($soundsDir)) {
    $files = scandir($soundsDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $extensions)) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $availableSounds[$name] = [
                'path' => '/assets/sounds/' . $file,
                'filename' => $file,
                'extension' => $ext,
            ];
        }
    }
    ksort($availableSounds);
}

// تعریف رویدادها
$sseEvents = [
    'game_started' => ['label' => 'شروع بازی', 'icon' => '🎮', 'description' => 'وقتی بازی شروع می‌شود', 'color' => 'green'],
    'round_recorded' => ['label' => 'ثبت دور (تماشاچیان)', 'icon' => '📊', 'description' => 'وقتی یک دور ثبت می‌شود (فقط تماشاچیان)', 'color' => 'blue'],
    'round_winner' => ['label' => '🏆 برنده دور', 'icon' => '🎉', 'description' => 'وقتی شما یا تیم شما برنده یک دور می‌شوید', 'color' => 'yellow'],
    'round_loser' => ['label' => '💔 بازنده دور', 'icon' => '😔', 'description' => 'وقتی شما یا تیم شما بازنده یک دور می‌شوید', 'color' => 'red'],
    'round_undone' => ['label' => 'لغو دور', 'icon' => '↩️', 'description' => 'وقتی آخرین دور لغو می‌شود', 'color' => 'orange'],
    'game_finished' => ['label' => 'پایان بازی (عمومی)', 'icon' => '🏁', 'description' => 'وقتی بازی تمام می‌شود (پیام عمومی)', 'color' => 'purple'],
    'game_winner' => ['label' => '🏆 برنده بازی', 'icon' => '🎊', 'description' => 'وقتی شما یا تیم شما برنده بازی می‌شوید', 'color' => 'green'],
    'game_loser' => ['label' => '💔 بازنده بازی', 'icon' => '😢', 'description' => 'وقتی شما یا تیم شما بازنده بازی می‌شوید', 'color' => 'red'],
    'game_status_changed' => ['label' => 'تغییر وضعیت بازی', 'icon' => '🔄', 'description' => 'توقف یا ادامه بازی (زیرمجموعه دارد)', 'color' => 'orange', 'has_children' => true],
    'score_updated' => ['label' => 'به‌روزرسانی امتیاز', 'icon' => '⭐', 'description' => 'وقتی امتیاز تغییر می‌کند', 'color' => 'yellow'],
    'notification' => ['label' => 'نوتیفیکیشن کاربر', 'icon' => '🔔', 'description' => 'دستاوردها، سطح و...', 'color' => 'pink'],
    'system_message' => ['label' => 'پیام سیستمی', 'icon' => '📢', 'description' => 'پیام‌های عمومی', 'color' => 'indigo'],
];

// گرفتن تنظیمات فعلی
$sseSoundSettings = [];
foreach ($currentCategory as $s) {
    if ($s['key'] === 'sse_sound_settings') {
        $sseSoundSettings = $s['value'] ?? [];
        if (is_string($sseSoundSettings)) {
            $sseSoundSettings = json_decode($sseSoundSettings, true) ?: [];
        }
        break;
    }
}

$defaultSettings = [
    'game_started' => ['enabled' => true, 'sound' => 'game-start.mp3'],
    'round_recorded' => ['enabled' => true, 'sound' => 'round-recorded.mp3'],
    'round_winner' => ['enabled' => true, 'sound' => 'round-win.mp3'],
    'round_loser' => ['enabled' => true, 'sound' => 'round-lose.mp3'],
    'round_undone' => ['enabled' => true, 'sound' => 'default.mp3'],
    'game_finished' => ['enabled' => true, 'sound' => 'default.mp3'],
    'game_winner' => ['enabled' => true, 'sound' => 'game-win.mp3'],
    'game_loser' => ['enabled' => true, 'sound' => 'round-lose.mp3'],
    'game_status_changed' => [
        'paused' => ['enabled' => true, 'sound' => 'game-pause.mp3'],
        'resumed' => ['enabled' => true, 'sound' => 'game-resume.mp3'],
    ],
    'score_updated' => ['enabled' => false, 'sound' => 'default.mp3'],
    'notification' => ['enabled' => true, 'sound' => 'default.mp3'],
    'system_message' => ['enabled' => true, 'sound' => 'default.mp3'],
];

if (empty($sseSoundSettings)) {
    $sseSoundSettings = $defaultSettings;
} else {
    foreach ($defaultSettings as $key => $defaultValue) {
        if (!isset($sseSoundSettings[$key])) {
            $sseSoundSettings[$key] = $defaultValue;
        }
    }
}
?>

<form method="POST" action="/admin/settings" id="settings-form">
    <input type="hidden" name="category" value="<?= $activeCategory ?>">
    <div class="space-y-4">

        <!-- بخش ویژه: تنظیمات صدای SSE -->
        <div class="bg-gradient-to-r from-orange-50 via-amber-50 to-yellow-50 border-2 border-orange-300 rounded-2xl p-5 shadow-lg mb-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-amber-200/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>

            <div class="relative z-10">
                <div class="flex items-start justify-between mb-5 pb-4 border-b-2 border-orange-200/50">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-3xl">🎵</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-800 flex items-center gap-2">
                                تنظیمات صدای رویدادهای زنده (SSE)
                            </h3>
                            <p class="text-xs text-gray-600 font-medium mt-1">
                                برای هر رویداد، صدا را انتخاب و فعال/غیرفعال کنید.
                                <span class="text-orange-700 font-bold"><?= count($availableSounds) ?></span> فایل صوتی در سیستم یافت شد.
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="testAllSSESounds()"
                        class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-xl text-xs font-bold shadow hover:shadow-md transition">
                        <span>🎶</span>
                        <span>تست همه</span>
                    </button>
                </div>

                <input type="hidden" name="settings[sse_sound_settings]" id="sse_sound_settings_input"
                    value='<?= htmlspecialchars(json_encode($sseSoundSettings, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php foreach ($sseEvents as $eventKey => $eventMeta):
                        if (isset($eventMeta['has_children']) && $eventMeta['has_children']) continue;

                        $currentEnabled = $sseSoundSettings[$eventKey]['enabled'] ?? true;
                        $currentSound = $sseSoundSettings[$eventKey]['sound'] ?? 'default';
                        $color = $eventMeta['color'];
                    ?>
                        <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-<?= $color ?>-400 transition-all duration-200 hover:shadow-md"
                            data-sse-event="<?= $eventKey ?>">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 bg-gradient-to-br from-<?= $color ?>-400 to-<?= $color ?>-600 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <span class="text-2xl"><?= $eventMeta['icon'] ?></span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="mb-2">
                                        <div class="font-bold text-gray-800 text-sm"><?= $eventMeta['label'] ?></div>
                                        <div class="text-xs text-gray-500"><?= $eventMeta['description'] ?></div>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                                            <input type="checkbox"
                                                class="sse-event-enabled w-4 h-4 text-<?= $color ?>-600 rounded focus:ring-<?= $color ?>-500"
                                                <?= $currentEnabled ? 'checked' : '' ?>
                                                data-event="<?= $eventKey ?>"
                                                onchange="updateSSESettings()">
                                            <span class="text-xs font-bold text-gray-700">فعال</span>
                                        </label>
                                        <select class="sse-event-sound flex-1 min-w-[120px] max-w-[220px] px-2 py-1.5 border-2 border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-<?= $color ?>-500 bg-white"
                                            data-event="<?= $eventKey ?>"
                                            onchange="updateSSESettings()">
                                            <?php foreach ($availableSounds as $soundName => $soundData):
                                                $soundValue = $soundData['filename'];
                                                $isSelected = ($currentSound === $soundValue) ||
                                                    ($currentSound === $soundName) ||
                                                    (pathinfo($currentSound, PATHINFO_FILENAME) === $soundName);
                                            ?>
                                                <option value="<?= htmlspecialchars($soundValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($soundData['filename']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button type="button"
                                            onclick="playSSETestSound('<?= htmlspecialchars($eventKey) ?>')"
                                            class="px-2.5 py-1.5 bg-gradient-to-r from-<?= $color ?>-500 to-<?= $color ?>-600 text-white rounded-lg text-xs font-bold shadow hover:shadow-md hover:scale-105 transition flex items-center gap-1"
                                            title="پخش تست">
                                            <span>▶</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- کارت ویژه: game_status_changed -->
                    <div class="group bg-white rounded-xl p-4 border-2 border-orange-300 hover:border-orange-500 transition-all duration-200 hover:shadow-md md:col-span-2"
                        data-sse-event="game_status_changed">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-11 h-11 bg-gradient-to-br from-orange-400 to-red-600 rounded-lg flex items-center justify-center shadow flex-shrink-0">
                                <span class="text-2xl">🔄</span>
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-gray-800 text-sm">تغییر وضعیت بازی</div>
                                <div class="text-xs text-gray-500">برای هر وضعیت، صدای جداگانه تنظیم کنید</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mr-14">
                            <?php
                            $pausedEnabled = $sseSoundSettings['game_status_changed']['paused']['enabled'] ?? true;
                            $pausedSound = $sseSoundSettings['game_status_changed']['paused']['sound'] ?? 'game-pause';
                            ?>
                            <div class="bg-orange-50 rounded-lg p-3 border border-orange-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-lg">⏸️</span>
                                    <span class="font-bold text-sm text-gray-800">توقف بازی (Paused)</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox"
                                            class="sse-event-enabled w-4 h-4 text-orange-600 rounded"
                                            <?= $pausedEnabled ? 'checked' : '' ?>
                                            data-event="game_status_changed.paused"
                                            onchange="updateSSESettings()">
                                        <span class="text-xs font-bold">فعال</span>
                                    </label>
                                    <select class="sse-event-sound flex-1 min-w-[100px] px-2 py-1.5 border-2 border-orange-200 rounded-lg text-xs bg-white"
                                        data-event="game_status_changed.paused"
                                        onchange="updateSSESettings()">
                                        <?php foreach ($availableSounds as $soundName => $soundData):
                                            $soundValue = $soundData['filename'];
                                            $isSelected = ($pausedSound === $soundValue) ||
                                                ($pausedSound === $soundName) ||
                                                (pathinfo($pausedSound, PATHINFO_FILENAME) === $soundName);
                                        ?>
                                            <option value="<?= htmlspecialchars($soundValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($soundData['filename']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" onclick="playSSETestSound('game_status_changed.paused')"
                                        class="px-2 py-1.5 bg-orange-500 text-white rounded-lg text-xs font-bold hover:bg-orange-600">▶</button>
                                </div>
                            </div>

                            <?php
                            $resumedEnabled = $sseSoundSettings['game_status_changed']['resumed']['enabled'] ?? true;
                            $resumedSound = $sseSoundSettings['game_status_changed']['resumed']['sound'] ?? 'game-resume';
                            ?>
                            <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-lg">▶️</span>
                                    <span class="font-bold text-sm text-gray-800">ادامه بازی (Resumed)</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox"
                                            class="sse-event-enabled w-4 h-4 text-green-600 rounded"
                                            <?= $resumedEnabled ? 'checked' : '' ?>
                                            data-event="game_status_changed.resumed"
                                            onchange="updateSSESettings()">
                                        <span class="text-xs font-bold">فعال</span>
                                    </label>
                                    <select class="sse-event-sound flex-1 min-w-[100px] px-2 py-1.5 border-2 border-green-200 rounded-lg text-xs bg-white"
                                        data-event="game_status_changed.resumed"
                                        onchange="updateSSESettings()">
                                        <?php foreach ($availableSounds as $soundName => $soundData):
                                            $soundValue = $soundData['filename'];
                                            $isSelected = ($resumedSound === $soundValue) ||
                                                ($resumedSound === $soundName) ||
                                                (pathinfo($resumedSound, PATHINFO_FILENAME) === $soundName);
                                        ?>
                                            <option value="<?= htmlspecialchars($soundValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($soundData['filename']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" onclick="playSSETestSound('game_status_changed.resumed')"
                                        class="px-2 py-1.5 bg-green-500 text-white rounded-lg text-xs font-bold hover:bg-green-600">▶</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="flex items-start gap-2">
                        <span class="text-lg flex-shrink-0">💡</span>
                        <div class="text-xs text-blue-800 space-y-1">
                            <p class="font-bold">راهنما:</p>
                            <ul class="list-disc list-inside space-y-0.5 text-blue-700">
                                <li>صداها به صورت داینامیک از پوشه <code class="bg-blue-100 px-1 rounded">public/assets/sounds/</code> خوانده می‌شوند</li>
                                <li>فرمت‌های پشتیبانی‌شده: MP3, WAV, OGG, M4A, AAC, WebM</li>
                                <li>🏆 <strong>رویدادهای شخصی‌سازی‌شده:</strong> برنده/بازنده دور و بازی اعلان‌های جداگانه دارند</li>
                                <li>تنظیمات برای همه کاربران سایت اعمال می‌شود</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <audio id="sse-test-audio" preload="auto"></audio>

        <script>
            const availableSounds = <?= json_encode($availableSounds, JSON_UNESCAPED_UNICODE) ?>;

            function updateSSESettings() {
                const settings = {};

                document.querySelectorAll('[data-sse-event]').forEach(card => {
                    const eventKey = card.dataset.sseEvent;
                    if (eventKey === 'game_status_changed') return;

                    const enabled = card.querySelector('.sse-event-enabled')?.checked ?? true;
                    const sound = card.querySelector('.sse-event-sound')?.value ?? 'default.mp3';

                    settings[eventKey] = {
                        enabled,
                        sound
                    };
                });

                settings.game_status_changed = {
                    paused: {
                        enabled: document.querySelector('[data-event="game_status_changed.paused"]')?.checked ?? true,
                        sound: document.querySelector('select[data-event="game_status_changed.paused"]')?.value ?? 'game-pause.mp3'
                    },
                    resumed: {
                        enabled: document.querySelector('[data-event="game_status_changed.resumed"]')?.checked ?? true,
                        sound: document.querySelector('select[data-event="game_status_changed.resumed"]')?.value ?? 'game-resume.mp3'
                    }
                };

                document.getElementById('sse_sound_settings_input').value = JSON.stringify(settings);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'تغییرات در حافظه - برای ذخیره دکمه پایین را بزنید',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            }

            function playSSETestSound(eventKey) {
                let soundName = 'default';

                if (eventKey.includes('.')) {
                    const select = document.querySelector(`select[data-event="${eventKey}"]`);
                    soundName = select?.value ?? 'default.mp3';
                } else {
                    const select = document.querySelector(`[data-sse-event="${eventKey}"] .sse-event-sound`);
                    soundName = select?.value ?? 'default.mp3';
                }

                const soundData = availableSounds[soundName];
                let audioUrl;

                if (soundData && soundData.path) {
                    audioUrl = soundData.path;
                } else {
                    if (soundName.includes('.')) {
                        audioUrl = '/assets/sounds/' + soundName;
                    } else {
                        audioUrl = '/assets/sounds/' + soundName + '.mp3';
                    }
                }

                const audio = document.getElementById('sse-test-audio');
                audio.src = audioUrl;
                audio.volume = 0.7;
                audio.currentTime = 0;

                audio.play().catch(err => {
                    console.error('Play error:', err);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'خطا در پخش صدا',
                            text: 'URL: ' + audioUrl,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });

                const btn = event.target.closest('button');
                if (btn) {
                    btn.classList.add('scale-125');
                    setTimeout(() => btn.classList.remove('scale-125'), 300);
                }
            }

            function testAllSSESounds() {
                const sounds = new Set();
                document.querySelectorAll('.sse-event-sound').forEach(select => sounds.add(select.value));

                const soundsArray = Array.from(sounds);
                let index = 0;

                function playNext() {
                    if (index >= soundsArray.length) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'تست همه صداها تمام شد',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                        return;
                    }

                    const soundName = soundsArray[index];
                    const soundData = availableSounds[soundName];
                    if (soundData) {
                        const audio = document.getElementById('sse-test-audio');
                        audio.src = soundData.path;
                        audio.volume = 0.7;
                        audio.play().catch(() => {});
                        setTimeout(() => {
                            index++;
                            playNext();
                        }, 2500);
                    } else {
                        index++;
                        playNext();
                    }
                }

                playNext();
            }
        </script>

        <!-- سایر تنظیمات اعلان‌ها (غیر از sse_sound_settings) -->
        <?php foreach ($currentCategory as $setting): ?>
            <?php if ($setting['key'] === 'sse_sound_settings') continue; ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start p-4 bg-gray-50/80 rounded-2xl border border-gray-200 hover:border-indigo-300 transition-all duration-200 hover:shadow-md">
                <div>
                    <label class="block text-sm font-black text-gray-800 mb-1"><?= htmlspecialchars(str_replace('_', ' ', $setting['key'])) ?></label>
                    <?php if (!empty($setting['description'])): ?>
                        <p class="text-xs text-gray-500 font-medium"><?= htmlspecialchars($setting['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <?php if ($setting['type'] === 'boolean'): ?>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="settings[<?= $setting['key'] ?>]" value="0">
                            <input type="checkbox" name="settings[<?= $setting['key'] ?>]" value="1" <?= $setting['value'] ? 'checked' : '' ?> class="w-5 h-5 text-<?= $catInfo['color'] ?>-600 rounded focus:ring-<?= $catInfo['color'] ?>-500">
                            <span class="text-sm font-bold text-gray-700">فعال</span>
                        </label>
                    <?php elseif ($setting['type'] === 'integer'): ?>
                        <input type="number" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php elseif ($setting['type'] === 'float'): ?>
                        <input type="number" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" step="0.01" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php else: ?>
                        <input type="text" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-6 flex items-center justify-between bg-gray-50/80 rounded-2xl p-4 border border-gray-200 shadow-sm">
        <div class="text-sm font-bold text-gray-600">💾 تغییرات ذخیره نشده دارید</div>
        <button type="submit" class="px-6 py-2.5 bg-<?= $catInfo['color'] ?>-600 hover:bg-<?= $catInfo['color'] ?>-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">💾 ذخیره تنظیمات</button>
    </div>
</form>