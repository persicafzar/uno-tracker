/**
 * 📡 Game SSE - نسخه نهایی با رفع خطای Swal
 *
 * 🆕 اصلاحات:
 * - رفع TypeError: Swal.fire(...).catch is not a function
 * - افزودن round_undone به event types
 * - بهبود error handling
 */

if (typeof SSE_CONFIG === "undefined") {
  var SSE_CONFIG = {
    gameId: window.GAME_CONFIG?.gameId || 0,
    currentUserId: window.GAME_CONFIG?.currentUserId || 0,
    isReferee: window.GAME_CONFIG?.isReferee || false,
    reloadDebounceMs: 1500,
    lastReloadTime: 0,
    pendingReload: false,
    reloadTimer: null,
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

if (typeof GAME_PARTICIPANTS === "undefined") {
  var GAME_PARTICIPANTS = window.GAME_CONFIG?.participants || [];
} else {
  if (window.GAME_CONFIG?.participants) {
    GAME_PARTICIPANTS = window.GAME_CONFIG.participants;
  }
}

console.log("📡 game-sse.js loaded");
console.log("🔧 SSE_CONFIG:", SSE_CONFIG);
console.log("👥 GAME_PARTICIPANTS:", GAME_PARTICIPANTS);

document.addEventListener("DOMContentLoaded", function () {
  console.log("🔄 DOMContentLoaded fired in game-sse.js");

  if (SSE_CONFIG.gameId > 0 && window.SSE) {
    console.log("🔌 Connecting to SSE for game #" + SSE_CONFIG.gameId);
    const sseUrl = (window.BASE_URL || "") + "/sse/game/" + SSE_CONFIG.gameId;
    console.log("🌐 SSE URL:", sseUrl);

    window.SSE.connect("game_" + SSE_CONFIG.gameId, sseUrl);

    // 🆕 همه event types معتبر
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
      console.log("📨 Registering listener for:", eventType);
      window.SSE.on("game_" + SSE_CONFIG.gameId, eventType, (data) => {
        console.log("📨 SSE event received:", eventType, data);
        handleSSEEvent(eventType, data);
      });
    });

    const heartbeatUrl = (window.BASE_URL || "") + "/game/" + SSE_CONFIG.gameId;
    window.SSE.startHeartbeat(heartbeatUrl);
  } else {
    console.warn("⚠️ SSE not available or gameId not set");
  }
});

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
// 🎵 Sound Management
// ═══════════════════════════════════════════════════════

function playAppropriateSound(eventType, userRole, eventData) {
  if (!window.SoundManager) {
    console.warn("⚠️ SoundManager not loaded yet");
    return;
  }

  // 🎯 اولویت ۱: استفاده از playForEvent (تنظیمات دیتابیس)
  if (typeof window.SoundManager.playForEvent === "function") {
    let actualEvent = eventType;

    // 🆕 برای round_recorded، بر اساس userRole تصمیم بگیر
    if (eventType === "round_recorded") {
      if (userRole === "round_winner") {
        actualEvent = "round_winner";
      } else if (userRole === "round_loser") {
        actualEvent = "round_loser";
      }
    }

    // 🆕 برای game_finished، بر اساس userRole تصمیم بگیر
    if (eventType === "game_finished") {
      if (userRole === "game_winner") {
        actualEvent = "game_winner";
        showConfetti();
      } else if (userRole === "game_loser") {
        actualEvent = "game_loser";
      }
    }

    // ساخت data غنی
    const enrichedData = { ...(eventData || {}) };
    if (eventType === "game_status_changed" && eventData?.status) {
      enrichedData.status = eventData.status;
    }
    enrichedData.user_role = userRole;
    enrichedData.is_winner =
      userRole === "round_winner" || userRole === "game_winner";

    console.log(
      `🎵 Playing sound for event: ${actualEvent} (original: ${eventType}, role: ${userRole})`,
      enrichedData,
    );

    window.SoundManager.playForEvent(actualEvent, enrichedData);
    return;
  }

  // 🎯 اولویت ۲: Fallback به متدهای قدیمی
  console.log(`⚠️ playForEvent not available, using legacy methods`);
  switch (eventType) {
    case "game_started":
      window.SoundManager.playGameStart();
      break;
    case "round_recorded":
      if (userRole === "round_winner") {
        window.SoundManager.playRoundWin();
      } else if (userRole === "round_loser") {
        window.SoundManager.playRoundLose();
      } else {
        window.SoundManager.playRoundRecorded();
      }
      break;
    case "round_undone":
      window.SoundManager.playDefault();
      break;
    case "game_finished":
      if (userRole === "game_winner") {
        window.SoundManager.playGameWin();
        showConfetti();
      } else {
        window.SoundManager.playRoundLose();
      }
      break;
    case "game_status_changed":
      if (eventData?.status === "paused") {
        window.SoundManager.playGamePause();
      } else if (eventData?.status === "active") {
        window.SoundManager.playGameResume();
      } else {
        window.SoundManager.playDefault();
      }
      break;
    default:
      window.SoundManager.playDefault();
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

  if (data.source_user_id && data.source_user_id === SSE_CONFIG.currentUserId) {
    console.log("⏭️ Ignoring own event (source_user_id matches)");
    return;
  }

  if (eventType === "game_referee_changed") {
    if (data.new_referee_id === SSE_CONFIG.currentUserId) {
      console.log("🎯 We are the new referee");
      SSE_CONFIG.isReferee = true;
      setTimeout(() => performReload(eventType), 1000);
      showCustomNotification(eventType, data, "participant");
      return;
    } else if (data.old_referee_id === SSE_CONFIG.currentUserId) {
      console.log("⏭️ We transferred referee role");
      SSE_CONFIG.isReferee = false;
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
// 🎨 Notification UI - 🆕 رفع خطای .catch
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
        if (isTeammateWinner) {
          title = `🏆🎉 تبریک! تیم شما برنده بازی شد!`;
        } else {
          title = `🏆🎉 تبریک! شما برنده بازی شدید!`;
        }
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
      if (data.new_target > data.old_target) {
        title = `🎯 هدف بازی از ${data.old_target} به ${data.new_target} افزایش یافت`;
      } else {
        title = `🎯 هدف بازی از ${data.old_target} به ${data.new_target} کاهش یافت`;
      }
      break;

    case "game_referee_changed":
      title = `👤 داور بازی به ${data.new_referee_name || "کاربر جدید"} منتقل شد`;
      break;
  }

  if (!title || typeof Swal === "undefined") return;

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

  // 🆕 رفع خطای .catch با استفاده از try/catch و Promise صحیح
  try {
    const resultPromise = Swal.fire(swalConfig);

    // 🆕 چک اینکه آیا resultPromise یک Promise واقعی است
    if (resultPromise && typeof resultPromise.then === "function") {
      resultPromise
        .then((result) => {
          if (result.isConfirmed) {
            console.log('✅ User clicked "عالی!"');
          } else if (result.dismiss === Swal.DismissReason.timer) {
            console.log("⏰ Auto-closed by timer");
          } else if (result.dismiss === Swal.DismissReason.close) {
            console.log("❌ User clicked close");
          } else if (result.dismiss === Swal.DismissReason.esc) {
            console.log("⌨️ User pressed Escape");
          }
        })
        .catch((err) => {
          console.warn("⚠️ Swal error (caught):", err);
        });
    } else {
      console.warn("⚠️ Swal.fire did not return a Promise");
    }
  } catch (err) {
    console.warn("⚠️ Swal error (try/catch):", err);
  }
}

// ═══════════════════════════════════════════════════════
// 🔄 Reload Management
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

  if (typeof htmx !== "undefined") {
    const url =
      (window.BASE_URL || "") + `/game/${SSE_CONFIG.gameId}?partial=1`;
    console.log(`🔄 Reloading: ${url}`);

    htmx
      .ajax("GET", url, {
        target: "#game-page-content",
        swap: "innerHTML",
        headers: {
          "HX-Request": "true",
          "X-Requested-With": "XMLHttpRequest",
        },
      })
      .then(() => {
        const content = document.getElementById("game-page-content");
        if (content) {
          htmx.process(content);
          if (window.GAME_CONFIG) {
            SSE_CONFIG.isReferee =
              window.GAME_CONFIG.isReferee ?? SSE_CONFIG.isReferee;
          }
          console.log("✅ Reload completed successfully");
        }
      })
      .catch((error) => {
        console.error("❌ HTMX reload failed:", error);
        location.reload();
      });
  } else {
    location.reload();
  }
}

function markSelfAction(eventType) {
  SSE_CONFIG.selfActions.add(eventType);
  setTimeout(() => {
    SSE_CONFIG.selfActions.delete(eventType);
  }, 3000);
}
