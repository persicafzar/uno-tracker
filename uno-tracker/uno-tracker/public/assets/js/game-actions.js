/**
 * 🎮 Game Actions - توابع اصلی بازی (با HTMX)
 * 🆕 نسخه کامل با توابع ویرایش هدف برد و انتقال داور
 */

/**
 * ▶️ شروع بازی
 */
function startGame(gameId) {
  Swal.fire({
    title: "شروع بازی",
    text: "آیا مطمئن هستید که می‌خواهید بازی را شروع کنید؟",
    icon: "success",
    showCancelButton: true,
    confirmButtonColor: "#16a34a",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "بله، شروع کن",
    cancelButtonText: "انصراف",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (typeof markSelfAction === "function") {
        markSelfAction("game_started");
        markSelfAction("game_status_changed");
      }

      htmx.ajax("POST", "/game/" + gameId + "/start", {
        target: "#game-page-content",
        swap: "innerHTML",
      });
    }
  });
}

/**
 * ⚡ تأیید عملیات (با HTMX)
 */
function confirmAction(message, url, title, icon) {
  Swal.fire({
    title: title,
    text: message,
    icon: icon,
    showCancelButton: true,
    confirmButtonColor: "#4f46e5",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "بله، انجام بده",
    cancelButtonText: "انصراف",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (typeof markSelfAction === "function") {
        if (url.includes("/record-round") || url.includes("/round")) {
          markSelfAction("round_recorded");
        } else if (url.includes("/undo")) {
          markSelfAction("round_undone");
        } else if (url.includes("/finish")) {
          markSelfAction("game_finished");
        } else if (url.includes("/pause") || url.includes("/resume")) {
          markSelfAction("game_status_changed");
        } else if (url.includes("/cancel")) {
          markSelfAction("game_status_changed");
        }
      }

      htmx.ajax("POST", url, {
        target: "#game-page-content",
        swap: "innerHTML",
      });
    }
  });
}

/**
 * 🆕 ویرایش هدف برد
 */
function editTargetWins(gameId, currentTarget, maxWins) {
  const modal = document.getElementById("edit-target-wins-modal");
  if (modal) {
    const input = document.getElementById("target-wins-input");
    if (input) {
      input.value = currentTarget;
      // 🆕 min = max(1, maxWins) نه maxWins
      input.min = Math.max(1, maxWins);
    }

    const hiddenInput = document.getElementById("max-wins-hidden");
    if (hiddenInput) {
      hiddenInput.value = maxWins;
    }

    const hint = document.getElementById("target-wins-hint");
    if (hint) {
      if (maxWins > 0) {
        hint.textContent = `⚠️ هدف جدید نمی‌تواند کمتر از بالاترین تعداد برد فعلی بازیکنان (بالاترین تعداد برد فعلی: ${maxWins}) باشد`;
      } else {
        hint.textContent = `⚠️ هدف برد باید حداقل ۱ باشد (هیچ بازیکنی هنوز بردی ندارد)`;
      }
    }

    modal.classList.remove("hidden");
  }
}

/**
 * 🆕 بستن Modal ویرایش هدف برد
 */
function closeEditTargetWinsModal() {
  const modal = document.getElementById("edit-target-wins-modal");
  if (modal) {
    modal.classList.add("hidden");
  }
}

/**
 * 🆕 انتقال نقش داور با SweetAlert
 */
function transferReferee(gameId) {
  Swal.fire({
    title: "انتقال نقش داور",
    text: "آیا مطمئن هستید که می‌خواهید نقش داور را منتقل کنید؟",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#9333ea",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "بله، ادامه بده",
    cancelButtonText: "انصراف",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      const modal = document.getElementById("transfer-referee-modal");
      if (modal) {
        modal.classList.remove("hidden");
      }
    }
  });
}

/**
 * 🆕 بستن Modal انتقال نقش داور
 */
function closeTransferRefereeModal() {
  const modal = document.getElementById("transfer-referee-modal");
  if (modal) {
    modal.classList.add("hidden");
  }
}

/**
 * 🆕 لغو آخرین دور
 */
function confirmUndo(gameId) {
  Swal.fire({
    title: "لغو آخرین دور",
    text: "آیا مطمئن هستید که می‌خواهید آخرین دور را لغو کنید؟",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#f97316",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "بله، لغو کن",
    cancelButtonText: "انصراف",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (typeof markSelfAction === "function") {
        markSelfAction("round_undone");
      }

      htmx.ajax("POST", "/game/" + gameId + "/undo-round", {
        target: "#game-page-content",
        swap: "innerHTML",
      });
    }
  });
}

/**
 * 🆕 مدیریت خطای HTMX برای فرم ویرایش هدف برد
 */
function handleEditTargetError(event) {
  const xhr = event.detail.xhr;
  let errorMessage = "خطا در به‌روزرسانی هدف برد";

  try {
    const responseText = xhr.responseText;

    if (responseText.includes("bg-red-50")) {
      const match = responseText.match(/<span>(.*?)<\/span>/);
      if (match && match[1]) {
        errorMessage = match[1];
      }
    } else {
      errorMessage = responseText;
    }
  } catch (e) {
    console.error("Error parsing error message:", e);
  }

  if (typeof Swal !== "undefined") {
    Swal.fire({
      icon: "error",
      title: "خطا",
      text: errorMessage,
      confirmButtonColor: "#ef4444",
      confirmButtonText: "متوجه شدم",
    });
  } else {
    alert(errorMessage);
  }
}

// 🆕 Event listener برای خطاهای HTMX
document.body.addEventListener("htmx:responseError", function (event) {
  if (event.detail.elt && event.detail.elt.id === "edit-target-wins-form") {
    handleEditTargetError(event);
  }
});

// 🆕 بعد از swap موفق HTMX، Modal ها را ببند
document.body.addEventListener("htmx:afterSwap", function (event) {
  if (event.detail.target && event.detail.target.id === "game-page-content") {
    closeEditTargetWinsModal();
    closeTransferRefereeModal();
  }
});
