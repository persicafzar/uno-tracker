<?php

/**
 * صفحه نمایش بازی (Game Show)
 * 
 * 🆕 اصلاحات:
 * - استفاده از $_GET و $_SERVER به جای $request (رفع خطای Undefined variable)
 * - تشخیص partial request برای HTMX swap
 * - بازگشت فقط محتوای game-content.php در حالت partial
 */

// 🆕 تشخیص partial request با استفاده از superglobals
$isPartial = (isset($_GET['partial']) && $_GET['partial'] === '1') ||
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
    (isset($_SERVER['HTTP_HX_REQUEST']) && $_SERVER['HTTP_HX_REQUEST'] === 'true');

// 🆕 در حالت partial، فقط محتوای game-content را برگردان (بدون layout)
if ($isPartial) {
    include __DIR__ . '/partials/game-content.php';
    return;
}

// ═══════════════════════════════════════════════════════════
// 🎮 حالت کامل صفحه (Full Page with Layout)
// ═══════════════════════════════════════════════════════════

use Core\JalaliDate;

$currentUser = $currentUser ?? $user ?? null;
$isCurrentReferee = $currentUser && $game->referee_id === (int)($currentUser['id'] ?? 0);
$isAdminUser = $isAdmin ?? false;
?>

<div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
    <?php include __DIR__ . '/partials/game-content.php'; ?>
</div>