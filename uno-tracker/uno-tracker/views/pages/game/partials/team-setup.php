<!-- Step 4: Team Setup -->
<div x-show="gameMode === 'friendly' && totalPlayers >= 2" x-cloak class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-200/70 shadow-md">
    <h2 class="text-lg sm:text-xl font-black text-gray-800 mb-3 sm:mb-4 tracking-tight">مرحله ۴: تنظیمات تیم‌ها</h2>

    <?php include __DIR__ . '/team-names.php'; ?>
    <?php include __DIR__ . '/team-algorithm.php'; ?>
    <?php include __DIR__ . '/team-preview.php'; ?>
    <?php include __DIR__ . '/manual-assignment.php'; ?>

    <div x-show="hasTeamValidationError" x-cloak class="mt-4 bg-red-50 border-2 border-red-200 rounded-2xl p-3 sm:p-4 shadow-sm">
        <div class="flex items-start gap-2.5">
            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div class="text-xs sm:text-sm text-red-800 font-medium">
                <strong>خطا:</strong> <span x-text="teamValidationMessage"></span>
            </div>
        </div>
    </div>
</div>