/**
 * 📡 Game SSE - نسخه ریشه‌ای با Fallback Refresh
 *
 * 🆕 ویژگی‌های جدید:
 * - Fallback refresh timer (اگر SSE قطع شد)
 * - Debug logging قوی
 * - Connection health monitoring
 * - Automatic reconnect با exponential backoff
 */

// استفاده از var برای جلوگیری از خطای تکرار
if (typeof SSE_CONFIG === "undefined") {
  var SSE_CONFIG = {
    gameId: window.GAME_CONFIG?.gameId || 0,
    currentUserId: window.GAME_CONFIG?.currentUserId || 0,
    isReferee: window.GAME_CONFIG?.isReferee || false,
    sseFallbackSeconds: window.GAME_CONFIG?.sseFallbackSeconds || 10,
    reloadDebounceMs: 1500,
    lastReloadTime: 0,
    pendingReload: false,
    reloadTimer: null,
    selfActions: new Set(),
    lastEventTime: Date.now(), // 🆕 زمان آخرین رویداد
    fallbackTimer: null, // 🆕 timer برای fallback
    connectionHealthy: true, // 🆕 سلامت اتصال
  };
} else {
  if (window.GAME_CONFIG) {
    SSE_CONFIG.gameId = window.GAME_CONFIG.gameId || SSE_CONFIG.gameId;
    SSE_CONFIG.currentUserId =
      window.GAME_CONFIG.currentUserId || SSE_CONFIG.currentUserId;
    SSE_CONFIG.isReferee = window.GAME_CONFIG.isReferee ?? SSE_CONFIG.isReferee;
    SSE_CONFIG.sseFallbackSeconds =
      window.GAME_CONFIG.sseFallbackSeconds || SSE_CONFIG.sseFallbackSeconds;
  }
}

if (typeof GAME_PARTICIPANTS === "undefined") {
  var GAME_PARTICIPANTS = window.GAME_CONFIG?.participants || [];
} else {
  if (window.GAME_CONFIG?.participants) {
    GAME_PARTICIPANTS = window.GAME_CONFIG.participants;
  }
}

console.log("📡 game-sse.js loaded (enhanced version)");
console.log("🔧 SSE_CONFIG:", SSE_CONFIG);
console.log("👥 GAME_PARTICIPANTS:", GAME_PARTICIPANTS);

// ============================================
// 🚀 Initialization
// ============================================
document.addEventListener("DOMContentLoaded", function () {
  console.log("🔄 DOMContentLoaded fired in game-sse.js");

  if (SSE_CONFIG.gameId > 0 && window.SSE) {
    console.log("🔌 Connecting to SSE for game #" + SSE_CONFIG.gameId);

    const sseUrl = (window.BASE_URL || "") + "/sse/game/" + SSE_CONFIG.gameId;
    console.log("🌐 SSE URL:", sseUrl);

    window.SSE.connect("game_" + SSE_CONFIG.gameId, sseUrl);

    // ثبت listener برای همه event types
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

    // 🆕 شروع Fallback Timer
    startFallbackTimer();
  } else {
    console.warn("⚠️ SSE not available or gameId not set");
    console.warn("SSE_CONFIG.gameId:", SSE_CONFIG.gameId);
    console.warn("window.SSE:", window.SSE);

    // 🆕 حتی اگر SSE نبود، fallback timer را شروع کن
    startFallbackTimer();
  }
});

// ============================================
// 🆕 Fallback Refresh Timer
// ============================================
function startFallbackTimer() {
  // اگر غیرفعال است، چیزی نکن
  if (SSE_CONFIG.sseFallbackSeconds <= 0) {
    console.log("⏸️ Fallback refresh disabled (seconds = 0)");
    return;
  }

  console.log(`⏱️ Starting fallback timer: ${SSE_CONFIG.sseFallbackSeconds}s`);

  // پاک کردن timer قبلی
  if (SSE_CONFIG.fallbackTimer) {
    clearInterval(SSE_CONFIG.fallbackTimer);
  }

  // بررسی هر ۵ ثانیه
  SSE_CONFIG.fallbackTimer = setInterval(() => {
    const now = Date.now();
    const timeSinceLastEvent = (now - SSE_CONFIG.lastEventTime) / 1000;

    // اگر بیش از N ثانیه از آخرین رویداد گذشته
    if (timeSinceLastEvent >= SSE_CONFIG.sseFallbackSeconds) {
      console.warn(
        `⚠️ No SSE event for ${Math.floor(timeSinceLastEvent)}s - triggering fallback refresh`,
      );

      // بررسی سلامت SSE connection
      const connection = window.SSE?.connections?.get(
        "game_" + SSE_CONFIG.gameId,
      );
      if (connection && !connection.ready) {
        console.warn("🔌 SSE connection is not ready - attempting reconnect");
        window.SSE.disconnect("game_" + SSE_CONFIG.gameId);
        setTimeout(() => {
          const sseUrl =
            (window.BASE_URL || "") + "/sse/game/" + SSE_CONFIG.gameId;
          window.SSE.connect("game_" + SSE_CONFIG.gameId, sseUrl);
        }, 1000);
      }

      // Fallback refresh
      performFallbackReload();

      // ریست timer
      SSE_CONFIG.lastEventTime = Date.now();
    } else {
      console.log(
        `✅ SSE healthy - last event ${Math.floor(timeSinceLastEvent)}s ago`,
      );
    }
  }, 5000); // بررسی هر ۵ ثانیه
}

function performFallbackReload() {
  console.log("🔄 Performing fallback reload");

  if (typeof htmx !== "undefined") {
    const url =
      (window.BASE_URL || "") + `/game/${SSE_CONFIG.gameId}?partial=1`;
    console.log(`🔄 Fallback HTMX reload: ${url}`);

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
          console.log("✅ Fallback reload completed");
        }
      })
      .catch((error) => {
        console.error("❌ Fallback reload failed:", error);
        // اگر HTMX کار نکرد، full page reload
        console.log("🔄 Falling back to full page reload");
        location.reload();
      });
  } else {
    location.reload();
  }
}

// ============================================
// 📨 Event Handling
// ============================================
function handleSSEEvent(eventType, data) {
  console.log(`📨 SSE Event: ${eventType}`, data);

  // 🆕 Update last event time
  SSE_CONFIG.lastEventTime = Date.now();

  // Ignore own events
  if (data.source_user_id && data.source_user_id === SSE_CONFIG.currentUserId) {
    console.log("⏭️ Ignoring own event (source_user_id matches)");
    return;
  }

  // Handle referee change
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

  // Ignore self actions
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

// ============================================
// 🔍 User Role Detection
// ============================================
function getUserRoleInEvent(eventType, data) {
  const userId = SSE_CONFIG.currentUserId;
  const currentParticipant = GAME_PARTICIPANTS.find(
    (p) => p.user_id === userId,
  );

  if (!currentParticipant) {
    return "spectator";
  }

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

// ============================================
// 🎨 Notification UI
// ============================================
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

// ============================================
// 🎵 Sound Handling
// ============================================
function playAppropriateSound(eventType, userRole, eventData) {
  if (!window.SoundManager) return;

  if (typeof window.SoundManager.playForEvent === "function") {
    let actualEvent = eventType;

    if (eventType === "round_recorded") {
      if (userRole === "round_winner") {
        actualEvent = "round_winner";
      } else if (userRole === "round_loser") {
        actualEvent = "round_loser";
      }
    }

    if (eventType === "game_finished") {
      if (userRole === "game_winner") {
        actualEvent = "game_winner";
        showConfetti();
      } else if (userRole === "game_loser") {
        actualEvent = "game_loser";
      }
    }

    const enrichedData = { ...eventData };
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

// ============================================
// 🎨 Custom Notifications
// ============================================
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

  Swal.fire(swalConfig).then((result) => {
    if (result.isConfirmed) {
      console.log('✅ User clicked "عالی!"');
    } else if (result.dismiss === Swal.DismissReason.timer) {
      console.log("⏰ Auto-closed by timer");
    } else if (result.dismiss === Swal.DismissReason.close) {
      console.log("❌ User clicked close");
    } else if (result.dismiss === Swal.DismissReason.esc) {
      console.log("⌨️ User pressed Escape");
    }
  });
}

// ============================================
// 🔄 Reload Logic
// ============================================
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

          // 🆕 ریست lastEventTime بعد از reload
          SSE_CONFIG.lastEventTime = Date.now();
        }
      })
      .catch((error) => {
        console.error("❌ HTMX reload failed:", error);
        // Fallback to full page reload
        console.log("🔄 Falling back to full page reload");
        location.reload();
      });
  } else {
    location.reload();
  }
}

// ============================================
// 🏷️ Self Action Tracking
// ============================================
function markSelfAction(eventType) {
  SSE_CONFIG.selfActions.add(eventType);
  setTimeout(() => {
    SSE_CONFIG.selfActions.delete(eventType);
  }, 3000);
}

// ============================================
// 🧹 Cleanup
// ============================================
window.addEventListener("beforeunload", function () {
  if (SSE_CONFIG.fallbackTimer) {
    clearInterval(SSE_CONFIG.fallbackTimer);
    SSE_CONFIG.fallbackTimer = null;
  }

  if (SSE_CONFIG.reloadTimer) {
    clearTimeout(SSE_CONFIG.reloadTimer);
    SSE_CONFIG.reloadTimer = null;
  }
});

console.log("✅ game-sse.js fully initialized with fallback timer");
