/**
 * 📡 Game SSE - مدیریت Real-time و Notifications
 * 🆕 نسخه نهایی با:
 *   - Auto-refresh غیرفعال برای داور
 *   - زمان قابل تنظیم از پنل مدیریت
 *   - رفع خطای HTMX insertBefore
 *   - بهبود پخش صدا
 *   - رفع خطای Swal.fire(...).catch
 */

// ═══════════════════════════════════════════════════════
// 🎯 CONFIG
// ═══════════════════════════════════════════════════════
if (typeof SSE_CONFIG === "undefined") {
  var SSE_CONFIG = {
    gameId: window.GAME_CONFIG?.gameId || 0,
    currentUserId: window.GAME_CONFIG?.currentUserId || 0,
    isReferee: window.GAME_CONFIG?.isReferee || false,
    reloadDebounceMs: 1500,
    lastReloadTime: 0,
    lastSseEventTime: Date.now(),
    pendingReload: false,
    reloadTimer: null,
    autoRefreshInterval: null,
    autoRefreshDelayMs: 10000, // مقدار پیش‌فرض (از تنظیمات به‌روز می‌شود)
    selfActions: new Set(),
  };
} else {
  if (window.GAME_CONFIG) {
    SSE_CONFIG.gameId = window.GAME_CONFIG.gameId || SSE_CONFIG.gameId;
    SSE_CONFIG.currentUserId =
      window.GAME_CONFIG.currentUserId || SSE_CONFIG.currentUserId;
    SSE_CONFIG.isReferee = window.GAME_CONFIG.isReferee ?? SSE_CONFIG.isReferee;
  }
}

// 🆕 دریافت وضعیت بازی از GAME_CONFIG
if (window.GAME_CONFIG && window.GAME_CONFIG.status) {
  SSE_CONFIG.gameStatus = window.GAME_CONFIG.status;
} else {
  SSE_CONFIG.gameStatus = "active"; // مقدار پیش‌فرض
}
console.log("🎮 Game status:", SSE_CONFIG.gameStatus);

// 🆕 بارگذاری تنظیمات Auto-Refresh از پنجره
if (window.SSE_FALLBACK_CONFIG) {
  const cfg = window.SSE_FALLBACK_CONFIG;
  if (cfg.enabled && cfg.refreshSeconds > 0) {
    SSE_CONFIG.autoRefreshDelayMs = cfg.refreshSeconds * 1000;
    console.log(`🔄 Auto-refresh enabled: ${cfg.refreshSeconds}s`);
  } else {
    SSE_CONFIG.autoRefreshDelayMs = 0; // غیرفعال
    console.log(`⏭️ Auto-refresh disabled by settings`);
  }
} else {
  // Fallback: اگر تنظیمات موجود نبود، از مقدار پیش‌فرض ۱۰ ثانیه استفاده کن
  console.warn("⚠️ SSE_FALLBACK_CONFIG not found, using default 10s");
  SSE_CONFIG.autoRefreshDelayMs = 10000;
}

if (typeof GAME_PARTICIPANTS === "undefined") {
  var GAME_PARTICIPANTS = window.GAME_CONFIG?.participants || [];
} else {
  if (window.GAME_CONFIG?.participants) {
    GAME_PARTICIPANTS = window.GAME_CONFIG.participants;
  }
}

console.log("📡 game-sse.js loaded (v2.3)");
console.log("🔧 SSE_CONFIG:", SSE_CONFIG);
console.log("👤 isReferee:", SSE_CONFIG.isReferee);

// ═══════════════════════════════════════════════════════
// 🔌 SSE Connection
// ═══════════════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", function () {
  console.log("🔄 DOMContentLoaded fired in game-sse.js");

  if (SSE_CONFIG.gameId > 0 && window.SSE) {
    console.log("🔌 Connecting to SSE for game #" + SSE_CONFIG.gameId);
    const sseUrl = (window.BASE_URL || "") + "/sse/game/" + SSE_CONFIG.gameId;
    console.log("🌐 SSE URL:", sseUrl);

    window.SSE.connect("game_" + SSE_CONFIG.gameId, sseUrl);

    const eventTypes = [
      "game_started",
      "round_recorded",
      "round_undone",
      "game_finished",
      "game_status_changed",
      "score_updated",
      "game_target_changed",
      "game_referee_changed",
    ];

    eventTypes.forEach((eventType) => {
      window.SSE.on("game_" + SSE_CONFIG.gameId, eventType, (data) => {
        console.log("📨 SSE event received:", eventType, data);
        handleSSEEvent(eventType, data);
      });
    });

    const heartbeatUrl = (window.BASE_URL || "") + "/game/" + SSE_CONFIG.gameId;
    window.SSE.startHeartbeat(heartbeatUrl);

    // 🆕 شروع Auto-Refresh Fallback (فقط برای غیر داور و در صورت فعال بودن تنظیمات)
    startAutoRefreshFallback();
  } else {
    console.warn("⚠️ SSE not available or gameId not set");
    startAutoRefreshFallback();
  }
});

// ═══════════════════════════════════════════════════════
// 🆕 AUTO-REFRESH FALLBACK
// ═══════════════════════════════════════════════════════

/**
 * 🔄 شروع auto-refresh - فقط برای کاربران عادی و در صورت فعال بودن تنظیمات
 */
function startAutoRefreshFallback() {
  // 🆕 اگر auto-refresh غیرفعال است یا داور است، شروع نکن
  if (SSE_CONFIG.autoRefreshDelayMs === 0) {
    console.log("⏭️ Auto-refresh disabled by settings");
    return;
  }

  if (SSE_CONFIG.isReferee) {
    console.log("⏭️ Auto-refresh disabled for referee");
    if (SSE_CONFIG.autoRefreshInterval) {
      clearInterval(SSE_CONFIG.autoRefreshInterval);
      SSE_CONFIG.autoRefreshInterval = null;
    }
    return;
  }

  // 🆕 بررسی وضعیت بازی
  if (
    SSE_CONFIG.gameStatus === "finished" ||
    SSE_CONFIG.gameStatus === "cancelled"
  ) {
    console.log(
      `⏭️ Auto-refresh disabled for game status: ${SSE_CONFIG.gameStatus}`,
    );
    if (SSE_CONFIG.autoRefreshInterval) {
      clearInterval(SSE_CONFIG.autoRefreshInterval);
      SSE_CONFIG.autoRefreshInterval = null;
    }
    return;
  }

  if (SSE_CONFIG.autoRefreshInterval) {
    clearInterval(SSE_CONFIG.autoRefreshInterval);
  }

  SSE_CONFIG.autoRefreshInterval = setInterval(() => {
    // 🆕 چک مجدد: اگر کاربر داور شده یا تنظیمات غیرفعال شده، auto-refresh را متوقف کن
    if (SSE_CONFIG.isReferee || SSE_CONFIG.autoRefreshDelayMs === 0) {
      console.log("⏭️ Auto-refresh stopped (referee or disabled)");
      clearInterval(SSE_CONFIG.autoRefreshInterval);
      SSE_CONFIG.autoRefreshInterval = null;
      return;
    }

    const now = Date.now();
    const timeSinceLastSse = now - SSE_CONFIG.lastSseEventTime;

    if (timeSinceLastSse > SSE_CONFIG.autoRefreshDelayMs) {
      console.log(
        `⏰ Auto-refresh triggered (no SSE for ${Math.round(timeSinceLastSse / 1000)}s)`,
      );
      performReload("auto_refresh");
      SSE_CONFIG.lastSseEventTime = Date.now();
    }
  }, 5000); // هر ۵ ثانیه چک کن

  console.log(
    `✅ Auto-refresh started for non-referee (delay: ${SSE_CONFIG.autoRefreshDelayMs / 1000}s)`,
  );
}

function stopAutoRefreshFallback() {
  if (SSE_CONFIG.autoRefreshInterval) {
    clearInterval(SSE_CONFIG.autoRefreshInterval);
    SSE_CONFIG.autoRefreshInterval = null;
    console.log("⏹️ Auto-refresh stopped");
  }
}

window.addEventListener("beforeunload", stopAutoRefreshFallback);

// ═══════════════════════════════════════════════════════
// 👤 User Role Detection
// ═══════════════════════════════════════════════════════

function getUserRoleInEvent(eventType, data) {
  const userId = SSE_CONFIG.currentUserId;
  const currentParticipant = GAME_PARTICIPANTS.find(
    (p) => p.user_id === userId,
  );

  if (!currentParticipant) return "spectator";

  switch (eventType) {
    case "round_recorded": {
      const winnerId = data.winner?.participant_id || data.winner?.id;
      const winnerUserId = data.winner?.id;
      const winnerParticipant = GAME_PARTICIPANTS.find(
        (p) => p.id === winnerId || p.user_id === winnerUserId,
      );

      if (
        currentParticipant.id === winnerId ||
        currentParticipant.user_id === winnerUserId
      ) {
        return "round_winner";
      }

      if (
        winnerParticipant?.team_id &&
        currentParticipant.team_id &&
        currentParticipant.team_id === winnerParticipant.team_id
      ) {
        return "round_winner";
      }

      return "round_loser";
    }

    case "round_undone":
      return "neutral";

    case "game_finished": {
      const winnerId = data.winner?.participant_id || data.winner?.id;
      const winnerUserId = data.winner?.id;
      const winnerParticipant = GAME_PARTICIPANTS.find(
        (p) => p.id === winnerId || p.user_id === winnerUserId,
      );

      if (
        currentParticipant.id === winnerId ||
        currentParticipant.user_id === winnerUserId
      ) {
        return "game_winner";
      }

      if (
        winnerParticipant?.team_id &&
        currentParticipant.team_id &&
        currentParticipant.team_id === winnerParticipant.team_id
      ) {
        return "game_winner";
      }

      return "game_loser";
    }

    case "game_started":
    case "game_status_changed":
    case "game_target_changed":
    case "game_referee_changed":
      return "participant";

    default:
      return "spectator";
  }
}

// ═══════════════════════════════════════════════════════
// 🎨 UI Helpers
// ═══════════════════════════════════════════════════════

function getNotificationClass(userRole) {
  const classMap = {
    game_winner: "notification-game-win",
    game_loser: "notification-round-lose",
    round_winner: "notification-round-win",
    round_loser: "notification-round-lose",
    participant: "notification-game-start",
    spectator: "notification-round-recorded",
    neutral: "notification-default",
  };
  return classMap[userRole] || "notification-default";
}

function getTimerDuration(userRole) {
  const durationMap = {
    game_winner: 5000,
    game_loser: 4000,
    round_winner: 3500,
    round_loser: 3500,
    participant: 3000,
    spectator: 2500,
    neutral: 2500,
  };
  return durationMap[userRole] || 2500;
}

// ═══════════════════════════════════════════════════════
// 🎵 Sound Management (بهبود یافته)
// ═══════════════════════════════════════════════════════

function playAppropriateSound(eventType, userRole, eventData) {
  if (!window.SoundManager) {
    console.warn("⚠️ SoundManager not loaded yet");
    return;
  }

  let actualEvent = eventType;

  if (eventType === "round_recorded") {
    if (userRole === "round_winner") actualEvent = "round_winner";
    else if (userRole === "round_loser") actualEvent = "round_loser";
  }

  if (eventType === "game_finished") {
    if (userRole === "game_winner") {
      actualEvent = "game_winner";
      showConfetti();
    } else if (userRole === "game_loser") {
      actualEvent = "game_loser";
    }
  }

  const enrichedData = { ...(eventData || {}) };
  if (eventType === "game_status_changed" && eventData?.status) {
    enrichedData.status = eventData.status;
  }
  enrichedData.user_role = userRole;
  enrichedData.is_winner =
    userRole === "round_winner" || userRole === "game_winner";

  console.log(
    `🎵 Playing sound: ${actualEvent} (original: ${eventType}, role: ${userRole})`,
    enrichedData,
  );

  try {
    if (typeof window.SoundManager.playForEvent === "function") {
      window.SoundManager.playForEvent(actualEvent, enrichedData);
    } else {
      console.warn("⚠️ playForEvent not available, using fallback");
      window.SoundManager.play("default", { volume: 0.6 });
    }
  } catch (error) {
    console.error("❌ Error playing sound:", error);
    try {
      if (typeof window.SoundManager.play === "function") {
        window.SoundManager.play("default", { volume: 0.6 });
      }
    } catch (e) {
      console.error("❌ Final fallback failed:", e);
    }
  }
}

function showConfetti() {
  const colors = [
    "#f59e0b",
    "#ef4444",
    "#10b981",
    "#3b82f6",
    "#8b5cf6",
    "#ec4899",
  ];
  const container = document.createElement("div");
  container.className = "notification-confetti";
  document.body.appendChild(container);

  for (let i = 0; i < 100; i++) {
    const piece = document.createElement("div");
    piece.className = "confetti-piece";
    piece.style.left = Math.random() * 100 + "%";
    piece.style.background = colors[Math.floor(Math.random() * colors.length)];
    piece.style.animationDelay = Math.random() * 2 + "s";
    piece.style.animationDuration = Math.random() * 2 + 2 + "s";
    container.appendChild(piece);
  }

  setTimeout(() => container.remove(), 5000);
}

// ═══════════════════════════════════════════════════════
// 📨 SSE Event Handler
// ═══════════════════════════════════════════════════════

function handleSSEEvent(eventType, data) {
  console.log(`📨 SSE Event: ${eventType}`, data);

  // 🆕 به‌روزرسانی زمان آخرین SSE
  SSE_CONFIG.lastSseEventTime = Date.now();

  if (eventType === "game_status_changed") {
    const newStatus = data.status;
    console.log(`🔄 Game status changed to: ${newStatus}`);
    SSE_CONFIG.gameStatus = newStatus;

    // اگر وضعیت finished یا cancelled شد، auto-refresh را متوقف کن
    if (newStatus === "finished" || newStatus === "cancelled") {
      if (SSE_CONFIG.autoRefreshInterval) {
        clearInterval(SSE_CONFIG.autoRefreshInterval);
        SSE_CONFIG.autoRefreshInterval = null;
        console.log("⏹️ Auto-refresh stopped (game finished/cancelled)");
      }
      return; // نیازی به ادامه نیست
    }

    // اگر وضعیت به active/pending/paused تغییر کرد، auto-refresh را شروع کن (اگر قبلاً شروع نشده)
    if (!SSE_CONFIG.autoRefreshInterval) {
      startAutoRefreshFallback();
    }
  }

  if (data.source_user_id && data.source_user_id === SSE_CONFIG.currentUserId) {
    console.log("⏭️ Ignoring own event (source_user_id matches)");
    return;
  }

  if (eventType === "game_referee_changed") {
    if (data.new_referee_id === SSE_CONFIG.currentUserId) {
      console.log("🎯 We are the new referee");
      SSE_CONFIG.isReferee = true;
      stopAutoRefreshFallback();
      setTimeout(() => performReload(eventType), 1000);
      showCustomNotification(eventType, data, "participant");
      return;
    } else if (data.old_referee_id === SSE_CONFIG.currentUserId) {
      console.log("⏭️ We transferred referee role");
      SSE_CONFIG.isReferee = false;
      setTimeout(() => startAutoRefreshFallback(), 2000);
      setTimeout(() => performReload(eventType), 1000);
      showCustomNotification(eventType, data, "participant");
      return;
    }
  }

  if (SSE_CONFIG.selfActions.has(eventType)) {
    console.log("⏭️ Ignoring self action (selfActions):", eventType);
    SSE_CONFIG.selfActions.delete(eventType);
    return;
  }

  const userRole = getUserRoleInEvent(eventType, data);
  console.log(`👤 User role: ${userRole}`);

  playAppropriateSound(eventType, userRole, data);
  showCustomNotification(eventType, data, userRole);
  scheduleReload(eventType, data);
}

// ═══════════════════════════════════════════════════════
// 🎨 Notification UI - 🆕 نسخه اصلاح‌شده با مدیریت خطا
// ═══════════════════════════════════════════════════════

function showCustomNotification(eventType, data, userRole) {
  let title = "";

  const winnerId = data.winner?.participant_id || data.winner?.id;
  const winnerUserId = data.winner?.id;
  const winnerParticipant = GAME_PARTICIPANTS.find(
    (p) => p.id === winnerId || p.user_id === winnerUserId,
  );
  const currentParticipant = GAME_PARTICIPANTS.find(
    (p) => p.user_id === SSE_CONFIG.currentUserId,
  );
  const isTeammateWinner =
    winnerParticipant?.team_id &&
    currentParticipant?.team_id &&
    currentParticipant.team_id === winnerParticipant.team_id &&
    currentParticipant.id !== winnerId;

  switch (eventType) {
    case "game_started":
      title = `🎮 بازی شروع شد! اولین بازیکن: ${data.first_player?.name || "نامشخص"}`;
      break;

    case "round_recorded":
      if (userRole === "round_winner") {
        if (isTeammateWinner) {
          title = `🎉 هم‌تیمی شما ${data.winner?.name || ""} دور ${data.round_number} را برد!`;
        } else {
          title = `🎉 شما دور ${data.round_number} را بردید!`;
        }
      } else if (userRole === "round_loser") {
        title = `💔 دور ${data.round_number} را باختید - برنده: ${data.winner?.name || "نامشخص"}`;
      } else {
        title = `📊 دور ${data.round_number} ثبت شد - برنده: ${data.winner?.name || "نامشخص"}`;
      }
      break;

    case "round_undone":
      title = `↩️ دور ${data.undone_round} لغو شد`;
      break;

    case "game_finished":
      if (userRole === "game_winner") {
        title = isTeammateWinner
          ? `🏆🎉 تبریک! تیم شما برنده بازی شد!`
          : `🏆🎉 تبریک! شما برنده بازی شدید!`;
      } else if (userRole === "game_loser") {
        title = `💔 بازی پایان یافت - برنده: ${data.winner?.name || "نامشخص"}`;
      } else {
        title = `🏁 بازی پایان یافت - برنده: ${data.winner?.name || "نامشخص"}`;
      }
      break;

    case "game_status_changed":
      const statusLabels = {
        active: "در حال بازی",
        paused: "متوقف",
        finished: "پایان یافته",
        cancelled: "لغو شده",
      };
      title = `🔄 وضعیت بازی: ${statusLabels[data.status] || data.status}`;
      break;

    case "score_updated":
      title = "⭐ امتیازات به‌روز شد";
      break;

    case "game_target_changed":
      title =
        data.new_target > data.old_target
          ? `🎯 هدف بازی از ${data.old_target} به ${data.new_target} افزایش یافت`
          : `🎯 هدف بازی از ${data.old_target} به ${data.new_target} کاهش یافت`;
      break;

    case "game_referee_changed":
      title = `👤 داور بازی به ${data.new_referee_name || "کاربر جدید"} منتقل شد`;
      break;

    case "auto_refresh":
      return;
  }

  if (!title || typeof Swal === "undefined") {
    console.warn("⚠️ No title or Swal not available");
    return;
  }

  if (Swal.isVisible()) {
    Swal.close();
  }

  const customClass = getNotificationClass(userRole);
  const isGameWinner = userRole === "game_winner";
  const timerDuration = getTimerDuration(userRole);

  const swalConfig = {
    toast: !isGameWinner,
    position: isGameWinner ? "center" : "top-end",
    title: title,
    showConfirmButton: isGameWinner,
    confirmButtonText: isGameWinner ? "🎉 عالی!" : undefined,
    confirmButtonColor: "#f59e0b",
    showCloseButton: !isGameWinner,
    timer: isGameWinner ? 5000 : timerDuration,
    timerProgressBar: !isGameWinner,
    customClass: {
      popup: `notification-custom ${customClass}`,
    },
  };

  // ✅ نسخه اصلاح‌شده با مدیریت خطای Swal.fire(...).catch
  try {
    const result = Swal.fire(swalConfig);

    // بررسی اینکه result یک Promise معتبر است
    if (result && typeof result.then === "function") {
      result
        .then((res) => {
          if (res.isConfirmed) {
            console.log('✅ User clicked "عالی!"');
          } else if (res.dismiss === Swal.DismissReason.timer) {
            console.log("⏰ Auto-closed by timer");
          } else if (res.dismiss === Swal.DismissReason.close) {
            console.log("❌ User clicked close");
          } else if (res.dismiss === Swal.DismissReason.esc) {
            console.log("⌨️ User pressed Escape");
          }
        })
        .catch((err) => {
          console.warn("⚠️ Swal promise error:", err);
        });
    } else {
      console.warn("⚠️ Swal.fire did not return a Promise. Result:", result);
      // Fallback: نمایش پیام با alert اگر Swal کار نکرد
      if (isGameWinner) {
        alert(title);
      }
    }
  } catch (error) {
    console.warn("⚠️ Swal.fire threw an error:", error);
    // Fallback: نمایش پیام با alert در صورت بروز خطا
    if (isGameWinner) {
      alert(title);
    }
  }
}

// ═══════════════════════════════════════════════════════
// 🔄 Reload Management (بهبود یافته برای رفع خطای insertBefore)
// ═══════════════════════════════════════════════════════

function scheduleReload(eventType, data) {
  const now = Date.now();
  const timeSinceLastReload = now - SSE_CONFIG.lastReloadTime;

  if (SSE_CONFIG.pendingReload) return;

  if (timeSinceLastReload < SSE_CONFIG.reloadDebounceMs) {
    const waitTime = SSE_CONFIG.reloadDebounceMs - timeSinceLastReload;
    SSE_CONFIG.pendingReload = true;
    SSE_CONFIG.reloadTimer = setTimeout(() => {
      performReload(eventType);
    }, waitTime);
  } else {
    performReload(eventType);
  }
}

function performReload(eventType) {
  SSE_CONFIG.lastReloadTime = Date.now();
  SSE_CONFIG.pendingReload = false;
  if (SSE_CONFIG.reloadTimer) {
    clearTimeout(SSE_CONFIG.reloadTimer);
    SSE_CONFIG.reloadTimer = null;
  }

  const targetId = "#game-page-content";
  const targetElement = document.querySelector(targetId);
  if (!targetElement || !targetElement.isConnected) {
    console.warn(`⚠️ Target ${targetId} not available, using full reload`);
    location.reload();
    return;
  }

  if (typeof htmx === "undefined") {
    console.warn("⚠️ htmx not available, using full reload");
    location.reload();
    return;
  }

  const url = (window.BASE_URL || "") + `/game/${SSE_CONFIG.gameId}?partial=1`;
  console.log(`🔄 Reloading: ${url} (trigger: ${eventType})`);

  try {
    htmx
      .ajax("GET", url, {
        target: targetId,
        swap: "innerHTML",
        headers: {
          "HX-Request": "true",
          "X-Requested-With": "XMLHttpRequest",
        },
      })
      .then(() => {
        const content = document.querySelector(targetId);
        if (content && content.isConnected) {
          htmx.process(content);
          if (window.GAME_CONFIG) {
            const wasReferee = SSE_CONFIG.isReferee;
            SSE_CONFIG.isReferee =
              window.GAME_CONFIG.isReferee ?? SSE_CONFIG.isReferee;
            GAME_PARTICIPANTS = window.GAME_CONFIG.participants || [];

            // 🆕 به‌روزرسانی وضعیت بازی
            const newStatus = window.GAME_CONFIG.status;
            if (newStatus) {
              SSE_CONFIG.gameStatus = newStatus;
              console.log(`🔄 Game status after reload: ${newStatus}`);
            }

            // مدیریت تغییر نقش داور
            if (!wasReferee && SSE_CONFIG.isReferee) {
              console.log("🎯 User became referee after reload");
              stopAutoRefreshFallback();
            } else if (wasReferee && !SSE_CONFIG.isReferee) {
              console.log("🔄 User no longer referee after reload");
              startAutoRefreshFallback();
            } else {
              // 🆕 اگر نقش داور تغییر نکرده، بر اساس وضعیت بازی Auto-Refresh را تنظیم کن
              if (
                SSE_CONFIG.gameStatus === "finished" ||
                SSE_CONFIG.gameStatus === "cancelled"
              ) {
                stopAutoRefreshFallback();
                console.log(
                  `⏹️ Auto-refresh stopped (game ${SSE_CONFIG.gameStatus})`,
                );
              } else {
                // اگر Auto-Refresh در حال اجرا نیست، آن را شروع کن
                if (!SSE_CONFIG.autoRefreshInterval) {
                  startAutoRefreshFallback();
                }
              }
            }
          }
          console.log("✅ Reload completed successfully");
        } else {
          console.warn("⚠️ Content not found after reload");
          location.reload();
        }
      })
      .catch((error) => {
        console.error("❌ HTMX reload failed:", error);
        if (error.message && error.message.includes("insertBefore")) {
          console.error("💥 insertBefore error detected, full reload");
          location.reload();
        }
      });
  } catch (error) {
    console.error("❌ HTMX ajax error:", error);
    location.reload();
  }
}

function markSelfAction(eventType) {
  SSE_CONFIG.selfActions.add(eventType);
  setTimeout(() => {
    SSE_CONFIG.selfActions.delete(eventType);
  }, 3000);
}

// 🆕 هندلر خطای insertBefore سراسری
window.addEventListener("error", function (event) {
  if (
    event.message &&
    event.message.includes("insertBefore") &&
    event.filename &&
    event.filename.includes("htmx")
  ) {
    console.error("💥 Caught HTMX insertBefore error:", event.message);
    event.preventDefault();
    setTimeout(() => {
      if (SSE_CONFIG.gameId > 0) {
        performReload("error_recovery");
      }
    }, 1000);
    return true;
  }
});

console.log("✅ game-sse.js v2.3 initialized (with all fixes)");
