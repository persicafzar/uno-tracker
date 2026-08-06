/**
 * 📡 TV SSE - نسخه مخصوص نمایش تلویزیون با پشتیبانی از صدا و نوتیفیکیشن عمومی
 * 🎯 تمام نوتیفیکیشن‌ها به‌صورت عمومی نمایش داده می‌شوند (بدون شخصی‌سازی)
 */

// متغیرهای سراسری
if (typeof TV_SSE_CONFIG === "undefined") {
  var TV_SSE_CONFIG = {
    gameId: window.GAME_CONFIG?.gameId || 0,
    currentUserId: window.GAME_CONFIG?.currentUserId || 0,
    isReferee: window.GAME_CONFIG?.isReferee || false,
    reloadDebounceMs: 0,
    lastReloadTime: 0,
    pendingReload: false,
    reloadTimer: null,
  };
} else {
  if (window.GAME_CONFIG) {
    TV_SSE_CONFIG.gameId = window.GAME_CONFIG.gameId || TV_SSE_CONFIG.gameId;
    TV_SSE_CONFIG.currentUserId =
      window.GAME_CONFIG.currentUserId || TV_SSE_CONFIG.currentUserId;
    TV_SSE_CONFIG.isReferee =
      window.GAME_CONFIG.isReferee ?? TV_SSE_CONFIG.isReferee;
  }
}

// لیست شرکت‌کنندگان
if (typeof TV_GAME_PARTICIPANTS === "undefined") {
  var TV_GAME_PARTICIPANTS = window.GAME_CONFIG?.participants || [];
} else {
  if (window.GAME_CONFIG?.participants) {
    TV_GAME_PARTICIPANTS = window.GAME_CONFIG.participants;
  }
}

console.log("📡 TV-SSE loaded (public notifications mode)");
console.log("🔧 TV_SSE_CONFIG:", TV_SSE_CONFIG);

document.addEventListener("DOMContentLoaded", function () {
  console.log("🔄 DOMContentLoaded fired in tv-sse.js");

  if (TV_SSE_CONFIG.gameId > 0 && window.SSE) {
    console.log("🔌 Connecting to SSE for TV game #" + TV_SSE_CONFIG.gameId);

    const sseUrl =
      (window.BASE_URL || "") + "/sse/game/" + TV_SSE_CONFIG.gameId;
    console.log("🌐 SSE URL:", sseUrl);

    window.SSE.connect("game_" + TV_SSE_CONFIG.gameId, sseUrl);

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
      console.log("📨 Registering TV listener for:", eventType);
      window.SSE.on("game_" + TV_SSE_CONFIG.gameId, eventType, (data) => {
        console.log("📨 TV SSE event received:", eventType, data);
        handleTVSSEEvent(eventType, data);
      });
    });

    const heartbeatUrl =
      (window.BASE_URL || "") + "/tv/" + TV_SSE_CONFIG.gameId;
    window.SSE.startHeartbeat(heartbeatUrl);
  } else {
    console.warn("⚠️ TV SSE not available or gameId not set");
  }
});

// ============================================
// ✅ توابع اصلی
// ============================================

function getTVUserRoleInEvent(eventType, data) {
  return "spectator";
}

function getTVNotificationClass(userRole) {
  return "notification-round-recorded";
}

function playTVSound(eventType, userRole, eventData) {
  if (!window.SoundManager) return;

  switch (eventType) {
    case "game_started":
      window.SoundManager.playGameStart();
      break;
    case "round_recorded":
      window.SoundManager.playRoundRecorded();
      break;
    case "round_undone":
      window.SoundManager.playDefault();
      break;
    case "game_finished":
      window.SoundManager.playGameWin();
      showTVConfetti();
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
    case "game_target_changed":
      window.SoundManager.play("default", { volume: 0.6 });
      break;
    case "game_referee_changed":
      window.SoundManager.play("default", { volume: 0.6 });
      break;
    default:
      window.SoundManager.playDefault();
  }
}

function showTVConfetti() {
  const colors = [
    "#f59e0b",
    "#ef4444",
    "#10b981",
    "#3b82f6",
    "#8b5cf6",
    "#ec4899",
    "#f97316",
    "#06b6d4",
  ];
  const container = document.createElement("div");
  container.className = "notification-confetti";
  document.body.appendChild(container);

  for (let i = 0; i < 150; i++) {
    const piece = document.createElement("div");
    piece.className = "confetti-piece";
    piece.style.left = Math.random() * 100 + "%";
    piece.style.background = colors[Math.floor(Math.random() * colors.length)];
    piece.style.width = Math.random() * 10 + 5 + "px";
    piece.style.height = Math.random() * 10 + 5 + "px";
    piece.style.borderRadius = Math.random() > 0.5 ? "50%" : "2px";
    piece.style.animationDelay = Math.random() * 2 + "s";
    piece.style.animationDuration = Math.random() * 2 + 2 + "s";
    container.appendChild(piece);
  }

  setTimeout(() => container.remove(), 5000);
}

function handleTVSSEEvent(eventType, data) {
  console.log(`📨 TV SSE Event: ${eventType}`, data);

  if (eventType === "game_referee_changed") {
    if (data.new_referee_id === TV_SSE_CONFIG.currentUserId) {
      TV_SSE_CONFIG.isReferee = true;
    } else if (data.old_referee_id === TV_SSE_CONFIG.currentUserId) {
      TV_SSE_CONFIG.isReferee = false;
    }
  }

  const userRole = "spectator";

  // ✅ صدا و نوتیفیکیشن و ریلود همزمان
  playTVSound(eventType, userRole, data);
  showTVNotification(eventType, data);
  performTVReload(eventType);

  // 🆕 ارسال رویداد برای ریست تایمر رفرش خودکار
  document.dispatchEvent(
    new CustomEvent("game_updated", {
      detail: { eventType, data },
    }),
  );
}

function showTVNotification(eventType, data) {
  let title = "";
  const winnerName = data.winner?.name || "نامشخص";

  switch (eventType) {
    case "game_started":
      title = `🎮 بازی شروع شد! اولین بازیکن: ${data.first_player?.name || "نامشخص"}`;
      break;
    case "round_recorded":
      title = `📊 دور ${data.round_number} ثبت شد - برنده: ${winnerName}`;
      break;
    case "round_undone":
      title = `↩️ دور ${data.undone_round} لغو شد`;
      break;
    case "game_finished":
      title = `🏁 بازی پایان یافت - برنده: ${winnerName}`;
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
    case "score_updated":
      title = "⭐ امتیازات به‌روز شد";
      break;
    default:
      title = `📢 رویداد: ${eventType}`;
  }

  if (!title || typeof Swal === "undefined") return;

  if (Swal.isVisible()) Swal.close();

  Swal.fire({
    toast: true,
    position: "top-end",
    title: title,
    showConfirmButton: false,
    timer: 4500,
    timerProgressBar: true,
    customClass: {
      popup: "notification-custom notification-round-recorded",
    },
  });
}

function performTVReload(eventType) {
  TV_SSE_CONFIG.lastReloadTime = Date.now();
  TV_SSE_CONFIG.pendingReload = false;

  if (TV_SSE_CONFIG.reloadTimer) {
    clearTimeout(TV_SSE_CONFIG.reloadTimer);
    TV_SSE_CONFIG.reloadTimer = null;
  }

  const targetId = "#tv-game-content";
  if (!document.querySelector(targetId)) {
    console.warn("⚠️ Target not found, reloading page");
    location.reload();
    return;
  }

  if (typeof htmx !== "undefined") {
    const url = (window.BASE_URL || "") + `/tv/${TV_SSE_CONFIG.gameId}/partial`;
    console.log(`🔄 TV Reloading: ${url}`);

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
        if (content) {
          htmx.process(content);
          if (window.GAME_CONFIG) {
            TV_SSE_CONFIG.isReferee =
              window.GAME_CONFIG.isReferee ?? TV_SSE_CONFIG.isReferee;
          }
          console.log("✅ TV Reload completed successfully");
        } else {
          console.warn("⚠️ Content not found after reload, reloading page");
          location.reload();
        }
      })
      .catch((error) => {
        console.error("❌ TV HTMX reload failed:", error);
        location.reload();
      });
  } else {
    location.reload();
  }
}

function markSelfAction(eventType) {
  console.log(`🏷️ TV markSelfAction: ${eventType} (ignored)`);
}
