<?php

/**
 * ثبت تمام Listenerهای سیستم
 * 
 * هر رویداد می‌تواند چندین Listener داشته باشد
 * اولویت بالاتر = اجرای زودتر
 */

use Core\EventDispatcher;

return function (EventDispatcher $events) {

    // ============================================
    // رویداد: پایان دور بازی (round_recorded)
    // ============================================
    // اولویت‌ها:
    // 100 = ScoringListener (محاسبه امتیاز)
    // 90  = TitleListener (بررسی القاب)
    // 80  = AchievementListener (بررسی نشان‌ها)
    // 60  = LeaderboardListener (به‌روزرسانی جدول امتیازات)
    // 50  = GamificationListener (سیستم گیمیفیکیشن)

    $events->listen('round_recorded', [\Presentation\Listeners\ScoringListener::class, 'handle'], 100);
    $events->listen('round_recorded', [\Presentation\Listeners\TitleListener::class, 'checkTitles'], 90);
    $events->listen('round_recorded', [\Presentation\Listeners\AchievementListener::class, 'checkAchievements'], 80);
    $events->listen('round_recorded', [\Presentation\Listeners\LeaderboardListener::class, 'updateStats'], 60);
    $events->listen('round_recorded', [\Presentation\Listeners\GamificationListener::class, 'handleRoundRecorded'], 50);

    // ============================================
    // رویداد: پایان بازی (game_finished)
    // ============================================
    $events->listen('game_finished', [\Presentation\Listeners\LeaderboardListener::class, 'finalizeGame'], 100);
    $events->listen('game_finished', [\Presentation\Listeners\AchievementListener::class, 'checkGameAchievements'], 90);
    $events->listen('game_finished', [\Presentation\Listeners\TitleListener::class, 'checkGameTitles'], 80);
    $events->listen('game_finished', [\Presentation\Listeners\GamificationListener::class, 'handleGameFinished'], 70);

    // ============================================
    // رویداد: شروع بازی (game_started)
    // ============================================
    $events->listen('game_started', [\Presentation\Listeners\LeaderboardListener::class, 'markGameStarted'], 100);
    $events->listen('game_started', [\Presentation\Listeners\GamificationListener::class, 'handleGameStarted'], 50);

    // ============================================
    // رویداد: لغو بازی (game_cancelled)
    // ============================================
    $events->listen('game_cancelled', [\Presentation\Listeners\LeaderboardListener::class, 'handleCancellation'], 100);
    $events->listen('game_cancelled', [\Presentation\Listeners\GamificationListener::class, 'handleGameCancelled'], 50);

    // ============================================
    // رویداد: انتقال لقب (title_transferred)
    // ============================================
    $events->listen('title_transferred', [\Presentation\Listeners\GamificationListener::class, 'handleTitleTransferred'], 50);
    // ============================================
    // 🛡️ ضدتقلب
    // ============================================
    $events->listen('game_created', [\Presentation\Listeners\AntiCheatListener::class, 'onGameCreated'], 10);
    $events->listen('game_finished', [\Presentation\Listeners\AntiCheatListener::class, 'onGameFinished'], 10);
};
