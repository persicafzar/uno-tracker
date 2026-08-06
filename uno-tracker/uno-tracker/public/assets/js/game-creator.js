// Game Creator - JavaScript Module
(function () {
  let playersData = [];
  let todayDate = "";

  const log = (emoji, message, data) => {
    console.log(
      `${emoji} [GameCreator] ${message}`,
      data !== undefined ? data : "",
    );
  };

  window.gameCreator = function () {
    return {
      // ============================================
      // State Variables
      // ============================================
      gameName: "",
      gameMode: "solo",
      targetWins: 10,
      selectedPlayers: [],
      teamAlgorithm: "manual",
      guestPlayers: [],
      teamNames: [],
      playerTeams: {},
      newGuestName: "",
      // 🆕 Team Preview State
      previewTeams: [],
      isLoadingTeams: false,
      teamAssignments: null,

      // Touch & Long Press State
      touchTimer: null,
      touchGhost: null,
      isLongPress: false,
      LONG_PRESS_DURATION: 400,

      // Drag & Drop State
      draggedPlayer: null,
      dragOverTeam: null,
      touchStartPos: null,
      isDragging: false,

      // ============================================
      // Avatar Methods
      // ============================================
      getPlayerAvatarUrl(playerId) {
        const player = playersData.find((p) => p.id == playerId);
        return player && player.avatar_path
          ? "/storage/uploads/avatars/" + player.avatar_path
          : null;
      },

      getPlayerAvatarUrlByKey(playerKey) {
        if (playerKey && playerKey.startsWith("user-")) {
          return this.getPlayerAvatarUrl(parseInt(playerKey.split("-")[1]));
        }
        return null;
      },

      // ============================================
      // Default Names
      // ============================================
      generateDefaultGameName() {
        const modeText =
          this.gameMode === "friendly" ? "لیگ دوستانه" : "لیگ انفرادی";
        return `${modeText} - ${todayDate}`;
      },

      generateDefaultTeamName(n) {
        return `تیم ${n}`;
      },

      updateDefaultGameName() {
        const s = `لیگ انفرادی - ${todayDate}`;
        const f = `لیگ دوستانه - ${todayDate}`;
        if (!this.gameName || this.gameName === s || this.gameName === f) {
          this.gameName = this.generateDefaultGameName();
        }
      },

      updateDefaultTeamNames() {
        const count = this.calculatedTeams;
        if (this.teamNames.length !== count) {
          const old = [...this.teamNames];
          this.teamNames = [];
          for (let i = 0; i < count; i++) {
            this.teamNames[i] =
              old[i] && old[i].trim()
                ? old[i]
                : this.generateDefaultTeamName(i + 1);
          }
        }
      },

      // ============================================
      // Load Old Data
      // ============================================
      loadOldData(element) {
        log("🔍", "loadOldData");
        if (!element) return;

        todayDate =
          element.getAttribute("data-today") ||
          new Date().toLocaleDateString("fa-IR");

        const pa = element.getAttribute("data-players");
        if (pa) {
          try {
            playersData = JSON.parse(pa);
          } catch (e) {
            log("❌", "parse players", e);
          }
        }

        const od = element.getAttribute("data-old");
        if (!od) {
          this.gameName = this.generateDefaultGameName();
          return;
        }

        try {
          const d = JSON.parse(od);
          this.gameName = d.game_name || this.generateDefaultGameName();
          this.gameMode = d.game_mode || "solo";
          this.targetWins = parseInt(d.target_wins) || 10;
          this.selectedPlayers = d.player_ids || [];
          this.teamAlgorithm = d.team_algorithm || "manual";
          this.guestPlayers = d.guest_players || [];
          this.teamNames = [];
          (d.team_names || []).forEach((n, i) => {
            this.teamNames[i] = n || this.generateDefaultTeamName(i + 1);
          });
          this.updateDefaultTeamNames();
          this.playerTeams = {};
          Object.keys(d.player_teams || {}).forEach((k) => {
            this.playerTeams[k] = parseInt(d.player_teams[k]);
          });
        } catch (e) {
          log("❌", "parse oldData", e);
          this.gameName = this.generateDefaultGameName();
        }
      },

      // ============================================
      // Validation
      // ============================================
      canCreateGame() {
        if (!this.gameName || !this.gameName.trim()) return false;

        const total = this.totalPlayers;

        if (this.gameMode === "solo") {
          if (total < 2) return false;
        } else if (this.gameMode === "friendly") {
          if (total < 4) return false;
          if (
            this.teamNames.filter((n) => n && n.trim()).length <
            this.calculatedTeams
          )
            return false;

          if (this.teamAlgorithm === "manual") {
            if (Object.keys(this.playerTeams).length !== total) return false;
            for (let i = 1; i <= this.calculatedTeams; i++) {
              const m = this.getTeamMembers(i);
              if (m.length !== 2) return false;
            }
          }
        }

        return true;
      },

      // ============================================
      // Players Management
      // ============================================
      getUnassignedPlayers() {
        const u = [];
        this.selectedPlayers.forEach((id) => {
          if (!this.playerTeams["user-" + id]) u.push("user-" + id);
        });
        this.guestPlayers.forEach((_, i) => {
          if (!this.playerTeams["guest-" + i]) u.push("guest-" + i);
        });
        return u;
      },

      get availablePlayers() {
        if (!this.selectedPlayers || !Array.isArray(this.selectedPlayers))
          return [];
        return this.selectedPlayers.filter((id) => {
          const key = "user-" + id;
          return !this.playerTeams || !this.playerTeams.hasOwnProperty(key);
        });
      },

      get availableGuests() {
        if (!this.guestPlayers || !Array.isArray(this.guestPlayers)) return [];
        return this.guestPlayers
          .map((name, index) => ({
            name,
            index,
            key: "guest-" + index,
          }))
          .filter((guest) => {
            return (
              !this.playerTeams || !this.playerTeams.hasOwnProperty(guest.key)
            );
          });
      },

      addGuest() {
        if (this.newGuestName.trim()) {
          this.guestPlayers.push(this.newGuestName.trim());
          this.newGuestName = "";
          log("➕", "Guest added");
        }
      },

      removeGuest(index) {
        log("🗑️", "removeGuest", index);
        this.guestPlayers.splice(index, 1);
        delete this.playerTeams["guest-" + index];
        const np = {};
        Object.keys(this.playerTeams).forEach((k) => {
          if (k.startsWith("guest-")) {
            const oi = parseInt(k.split("-")[1]);
            np[oi > index ? "guest-" + (oi - 1) : k] = this.playerTeams[k];
          } else {
            np[k] = this.playerTeams[k];
          }
        });
        this.playerTeams = np;
      },

      removePlayerFromTeam(playerKey) {
        log("🗑️", "removePlayerFromTeam", playerKey);
        if (!playerKey) return;

        const newTeams = {};
        Object.keys(this.playerTeams).forEach((k) => {
          if (k !== playerKey) {
            newTeams[k] = this.playerTeams[k];
          }
        });
        this.playerTeams = newTeams;

        log("✅", "After removal", Object.keys(this.playerTeams));
      },

      getPlayerName(playerId) {
        const p = playersData.find((p) => p.id == playerId);
        return p ? p.nickname : "ناشناس";
      },

      getPlayerNameByKey(playerKey) {
        if (!playerKey) return "ناشناس";
        if (playerKey.startsWith("guest-"))
          return (
            this.guestPlayers[parseInt(playerKey.split("-")[1])] || "ناشناس"
          );
        return this.getPlayerName(parseInt(playerKey.split("-")[1]));
      },

      // ============================================
      // Teams Management
      // ============================================
      getTeamColor(index) {
        return [
          "#3B82F6",
          "#EF4444",
          "#10B981",
          "#F59E0B",
          "#8B5CF6",
          "#EC4899",
        ][index % 6];
      },

      getTeamDisplayName(n) {
        const name = this.teamNames[n - 1];
        return name && name.trim() ? name : "تیم " + n;
      },

      getTeamMembers(n) {
        if (n === undefined || n === null || n === "" || isNaN(parseInt(n))) {
          return [];
        }

        if (!this.playerTeams || typeof this.playerTeams !== "object") {
          return [];
        }

        const ni = parseInt(n);

        const result = Object.keys(this.playerTeams).filter((k) => {
          const val = this.playerTeams[k];
          if (val === undefined || val === null) return false;
          return parseInt(val) === ni;
        });

        return Array.isArray(result) ? result : [];
      },

      get teamsData() {
        const teams = [];
        for (let i = 1; i <= this.calculatedTeams; i++) {
          teams.push({
            number: i,
            members: this.getTeamMembers(i),
            color: this.getTeamColor(i - 1),
            name: this.getTeamDisplayName(i),
          });
        }
        return teams;
      },

      get totalPlayers() {
        return this.selectedPlayers.length + this.guestPlayers.length;
      },

      get calculatedTeams() {
        if (this.gameMode !== "friendly" || this.totalPlayers === 0) return 0;
        return Math.floor(this.totalPlayers / 2);
      },

      get hasTeamValidationError() {
        if (
          this.gameMode !== "friendly" ||
          this.teamAlgorithm !== "manual" ||
          this.totalPlayers === 0
        )
          return false;
        if (Object.keys(this.playerTeams).length === 0) return false;
        if (Object.keys(this.playerTeams).length !== this.totalPlayers)
          return true;

        for (let i = 1; i <= this.calculatedTeams; i++) {
          const m = this.getTeamMembers(i);
          if (m.length !== 2) return true;
        }

        return false;
      },

      get teamValidationMessage() {
        if (!this.hasTeamValidationError) return "";

        const total = this.totalPlayers;
        const assigned = Object.keys(this.playerTeams).length;

        if (total < 4) return "بازی تیمی حداقل ۴ بازیکن نیاز دارد";
        if (assigned !== total)
          return `همه بازیکنان باید به تیم اختصاص داده شوند (${assigned}/${total})`;

        for (let i = 1; i <= this.calculatedTeams; i++) {
          const m = this.getTeamMembers(i);
          if (m.length !== 2) {
            return `تیم ${i} باید دقیقاً ۲ بازیکن داشته باشد (الان ${m.length} نفر)`;
          }
        }

        return "";
      },

      // ============================================
      // Desktop: HTML5 Drag & Drop
      // ============================================
      onDragStart(event, playerKey) {
        log("🎯", "Drag started", playerKey);
        this.draggedPlayer = playerKey;
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.setData("text/plain", playerKey);
        event.target.classList.add("dragging");
      },

      onDragEnd(event) {
        log("✅", "Drag ended");
        this.draggedPlayer = null;
        this.dragOverTeam = null;
        event.target.classList.remove("dragging");

        document.querySelectorAll(".team-drop-zone").forEach((el) => {
          el.classList.remove("drag-over");
        });
      },

      onDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = "move";
      },

      onDragEnter(event, teamNumber) {
        event.preventDefault();
        this.dragOverTeam = teamNumber;

        const teamEl = document.getElementById(`team-drop-list-${teamNumber}`);
        if (teamEl) {
          teamEl.closest(".team-drop-zone").classList.add("drag-over");
        }
      },

      onDragLeave(event, teamNumber) {
        const relatedTarget = event.relatedTarget;
        const teamEl = document.getElementById(`team-drop-list-${teamNumber}`);

        if (!teamEl || !teamEl.contains(relatedTarget)) {
          if (this.dragOverTeam === teamNumber) {
            this.dragOverTeam = null;
          }
          teamEl?.closest(".team-drop-zone")?.classList.remove("drag-over");
        }
      },

      onDrop(event, teamNumber) {
        event.preventDefault();

        const playerKey = event.dataTransfer.getData("text/plain");
        if (!playerKey) {
          log("⚠️", "No playerKey");
          return;
        }

        log("📍", "Drop", { playerKey, teamNumber });

        const newTeams = { ...this.playerTeams };
        newTeams[playerKey] = teamNumber;
        this.playerTeams = newTeams;

        this.draggedPlayer = null;
        this.dragOverTeam = null;

        document.querySelectorAll(".team-drop-zone").forEach((el) => {
          el.classList.remove("drag-over");
        });

        log("✅", "Player assigned", this.playerTeams);
      },

      onPoolDrop(event) {
        event.preventDefault();

        const playerKey = event.dataTransfer.getData("text/plain");
        if (!playerKey) return;

        log("↩️", "Return to pool", playerKey);

        const newTeams = { ...this.playerTeams };
        delete newTeams[playerKey];
        this.playerTeams = newTeams;

        this.draggedPlayer = null;
      },

      // ============================================
      // Mobile: Touch Events with Long Press + Ghost
      // ============================================
      // ============================================
      // 🆕 Touch Events - سازگار با همه مرورگرها
      // ============================================

      onTouchStart(event, playerKey) {
        this.draggedPlayer = playerKey;
        this.isLongPress = false;

        const touch = event.touches[0];
        this.touchStartPos = {
          x: touch.clientX,
          y: touch.clientY,
          time: Date.now(),
        };

        // 🆕 ذخیره عنصر اصلی برای مخفی کردن بعداً
        this.touchTarget = event.target.closest(".sortable-item");

        this.touchTimer = setTimeout(() => {
          this.isLongPress = true;

          // 🆕 جلوگیری از native ghost در Chrome Android
          if (event.cancelable) {
            event.preventDefault();
          }

          // 🆕 تغییر touch-action برای جلوگیری از scroll و native drag
          if (this.touchTarget) {
            this.touchTarget.style.touchAction = "none";
            this.touchTarget.style.visibility = "hidden";
            this.touchTarget.dataset.originalVisibility = "visible";
          }

          // ساخت Ghost
          this.createGhost(playerKey, touch.clientX, touch.clientY);

          // فیدبک لرزشی
          if (navigator.vibrate) {
            navigator.vibrate(50);
          }

          log("👆", "Long press activated", playerKey);
        }, this.LONG_PRESS_DURATION);
      },

      onTouchMove(event) {
        if (!this.draggedPlayer) return;

        const touch = event.touches[0];

        // 🆕 اگر Long Press هنوز شروع نشده
        if (!this.isLongPress) {
          const dx = touch.clientX - this.touchStartPos.x;
          const dy = touch.clientY - this.touchStartPos.y;
          const distance = Math.sqrt(dx * dx + dy * dy);

          // اگر انگشت زیاد حرکت کرده → لغو drag
          if (distance > 10) {
            clearTimeout(this.touchTimer);
            this.draggedPlayer = null;
            this.cleanupGhost();
            log("🚫", "Drag cancelled - scroll allowed");
            return;
          }

          return;
        }

        // 🆕 Long Press فعال - جلوگیری از رفتار پیش‌فرض مرورگر
        if (event.cancelable) {
          event.preventDefault();
        }

        // 🆕 به‌روزرسانی موقعیت Ghost با transform (سریع‌تر از left/top)
        if (this.touchGhost) {
          const x = touch.clientX - 60;
          const y = touch.clientY - 25;

          // 🆕 استفاده از transform به جای left/top (عملکرد بهتر)
          requestAnimationFrame(() => {
            if (this.touchGhost) {
              this.touchGhost.style.transform = `translate(${x}px, ${y}px)`;
            }
          });
        } else {
          this.createGhost(this.draggedPlayer, touch.clientX, touch.clientY);
        }

        // 🆕 پیدا کردن عنصر زیر انگشت
        if (this.touchGhost) {
          this.touchGhost.style.visibility = "hidden";
        }

        const element = document.elementFromPoint(touch.clientX, touch.clientY);

        if (this.touchGhost) {
          this.touchGhost.style.visibility = "visible";
        }

        // حذف کلاس‌های قبلی
        document
          .querySelectorAll(".team-drop-zone, #players-pool")
          .forEach((el) => {
            el.classList.remove("drag-over");
          });

        // پیدا کردن تیم یا pool
        const teamEl = element?.closest('[id^="team-drop-list-"]');
        const poolEl = element?.closest("#players-pool");

        if (teamEl) {
          const teamNumber = parseInt(teamEl.id.replace("team-drop-list-", ""));
          this.dragOverTeam = teamNumber;
          teamEl.closest(".team-drop-zone").classList.add("drag-over");
        } else if (poolEl) {
          this.dragOverTeam = "pool";
          poolEl.classList.add("drag-over");
        } else {
          this.dragOverTeam = null;
        }
      },

      onTouchEnd(event) {
        clearTimeout(this.touchTimer);

        // 🆕 بازیگرداندن visibility و touch-action عنصر اصلی
        if (this.touchTarget) {
          if (this.touchTarget.dataset.originalVisibility) {
            this.touchTarget.style.visibility =
              this.touchTarget.dataset.originalVisibility;
            delete this.touchTarget.dataset.originalVisibility;
          }
          this.touchTarget.style.touchAction = "manipulation";
          this.touchTarget = null;
        }

        if (!this.draggedPlayer) {
          this.cleanupGhost();
          return;
        }

        if (!this.isLongPress) {
          this.draggedPlayer = null;
          this.cleanupGhost();
          return;
        }

        log("👆", "Touch ended", {
          playerKey: this.draggedPlayer,
          target: this.dragOverTeam,
        });

        // عملیات Drop
        if (this.dragOverTeam === "pool") {
          const newTeams = { ...this.playerTeams };
          delete newTeams[this.draggedPlayer];
          this.playerTeams = newTeams;
        } else if (typeof this.dragOverTeam === "number") {
          const newTeams = { ...this.playerTeams };
          newTeams[this.draggedPlayer] = this.dragOverTeam;
          this.playerTeams = newTeams;
        }

        // پاکسازی
        this.cleanupGhost();
        this.draggedPlayer = null;
        this.dragOverTeam = null;
        this.isLongPress = false;
        this.touchStartPos = null;

        document
          .querySelectorAll(".team-drop-zone, #players-pool")
          .forEach((el) => {
            el.classList.remove("drag-over");
          });

        log("✅", "Touch completed", this.playerTeams);
      },

      // 🆕 اصلاح createGhost - استفاده از transform
      createGhost(playerKey, x, y) {
        this.cleanupGhost();

        const ghost = document.createElement("div");
        ghost.className = "touch-ghost";

        const isGuest = playerKey.startsWith("guest-");
        const name = this.getPlayerNameByKey(playerKey);
        const avatarUrl = this.getPlayerAvatarUrlByKey(playerKey);

        ghost.innerHTML = `
        <div class="flex items-center px-3 py-2 bg-white rounded-lg border-2 border-indigo-500 shadow-2xl">
            ${
              avatarUrl
                ? `<img src="${avatarUrl}" class="!w-8 !h-8 aspect-square rounded-full object-cover ml-2 border-2 border-indigo-300" draggable="false">`
                : `<div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-300 to-purple-300 flex items-center justify-center ml-2 border-2 border-indigo-300 text-sm">${isGuest ? "👥" : "👤"}</div>`
            }
            <span class="text-gray-800 font-semibold text-sm mr-2">${name}</span>
            ${isGuest ? '<span class="text-xs text-gray-500">(مهمان)</span>' : ""}
        </div>
    `;

        // 🆕 استفاده از transform به جای left/top
        const initialX = x - 60;
        const initialY = y - 25;

        ghost.style.cssText = `
        position: fixed;
        left: 0;
        top: 0;
        transform: translate(${initialX}px, ${initialY}px);
        z-index: 9999;
        pointer-events: none;
        opacity: 0.95;
        will-change: transform;
        /* 🆕 بدون transition برای حرکت فوری */
    `;

        document.body.appendChild(ghost);
        this.touchGhost = ghost;

        log("👻", "Ghost created at", { x: initialX, y: initialY });
      },

      cleanupGhost() {
        if (this.touchGhost && this.touchGhost.parentNode) {
          this.touchGhost.parentNode.removeChild(this.touchGhost);
        }
        this.touchGhost = null;
      },

      // ============================================
      // Watchers
      // ============================================
      init() {
        log("🚀", "init");

        this.$watch("teamAlgorithm", (nv, ov) => {
          log("🔄", "teamAlgorithm changed", { new: nv, old: ov });

          if (nv === "manual") {
            this.playerTeams = {};
            this.previewTeams = [];
          } else {
            // 🆕 بارگذاری پیش‌نمایش تیم‌ها
            if (this.selectedPlayers.length >= 4) {
              this.loadTeamPreview();
            }
          }
        });

        this.$watch("selectedPlayers", (nv, ov) => {
          if (JSON.stringify(nv) === JSON.stringify(ov)) return;

          log("👥", "selectedPlayers changed", { new: nv, old: ov });

          // فقط بازیکن‌هایی که از انتخاب خارج شده‌اند را حذف کن
          if (ov && Array.isArray(ov)) {
            ov.forEach((id) => {
              if (!nv.includes(id)) {
                const key = "user-" + id;
                if (this.playerTeams.hasOwnProperty(key)) {
                  delete this.playerTeams[key];
                  log("🗑️", "Removed unselected player", key);
                }
              }
            });
          }

          this.playerTeams = { ...this.playerTeams };

          // فقط بازیکن‌های جدید را به تیم اضافه کن
          if (this.teamAlgorithm === "manual") {
            nv.forEach((id) => {
              const k = "user-" + id;
              if (
                !this.playerTeams.hasOwnProperty(k) &&
                (!ov || !ov.includes(id))
              ) {
                let mt = 1,
                  mc = Infinity;
                for (let i = 1; i <= this.calculatedTeams; i++) {
                  const c = this.getTeamMembers(i).length;
                  if (c < mc) {
                    mc = c;
                    mt = i;
                  }
                }
                this.playerTeams[k] = mt;
                log("➕", "Added new player to team", { player: k, team: mt });
              }
            });

            this.playerTeams = { ...this.playerTeams };
          }

          log("✅", "After selectedPlayers change", this.playerTeams);
        });

        this.$watch("guestPlayers", (nv, ov) => {
          if (JSON.stringify(nv) === JSON.stringify(ov)) return;

          log("👥", "guestPlayers changed", nv);

          Object.keys(this.playerTeams).forEach((k) => {
            if (k.startsWith("guest-")) {
              const index = parseInt(k.split("-")[1]);
              if (index >= nv.length) {
                delete this.playerTeams[k];
              }
            }
          });

          if (this.teamAlgorithm === "manual") {
            nv.forEach((_, i) => {
              const k = "guest-" + i;
              if (
                !this.playerTeams.hasOwnProperty(k) &&
                (!ov || i >= ov.length)
              ) {
                let mt = 1,
                  mc = Infinity;
                for (let j = 1; j <= this.calculatedTeams; j++) {
                  const c = this.getTeamMembers(j).length;
                  if (c < mc) {
                    mc = c;
                    mt = j;
                  }
                }
                this.playerTeams[k] = mt;
              }
            });
          }
        });

        this.$watch("calculatedTeams", (nv, ov) => {
          log("🔢", "calculatedTeams changed", { new: nv, old: ov });

          if (nv > 0 && ov !== undefined && nv !== ov) {
            const newTeams = {};
            let playerIndex = 0;

            Object.keys(this.playerTeams).forEach((k) => {
              const currentTeam = parseInt(this.playerTeams[k]);

              if (currentTeam <= nv) {
                newTeams[k] = currentTeam;
              } else {
                newTeams[k] = (playerIndex % nv) + 1;
                playerIndex++;
              }
            });

            this.playerTeams = newTeams;
            log("🔄", "Reassigned players to new teams", this.playerTeams);
          }

          this.updateDefaultTeamNames();
        });

        this.$watch("gameMode", (nv) => {
          if (nv === "solo") this.playerTeams = {};
          this.updateDefaultGameName();
        });

        this.$watch("teamAlgorithm", (nv) => {
          if (nv !== "manual") this.playerTeams = {};
        });

        this.$watch(
          "playerTeams",
          () => {
            this.cleanupGhost();
          },
          { deep: true },
        );
      },
      // 🆕 Load Team Preview (AJAX) - اصلاح شده
      async loadTeamPreview() {
        if (
          this.teamAlgorithm === "manual" ||
          this.selectedPlayers.length < 4
        ) {
          this.previewTeams = [];
          this.teamAssignments = null;
          return;
        }

        this.isLoadingTeams = true;

        // 🆕 تبدیل Proxy به آرایه معمولی
        const playerIdsArray = [...this.selectedPlayers];

        log("🔄", "Loading team preview", {
          algorithm: this.teamAlgorithm,
          players: playerIdsArray,
        });

        try {
          const formData = new FormData();
          formData.append("player_ids", JSON.stringify(playerIdsArray));
          formData.append("algorithm", this.teamAlgorithm);
          formData.append("team_size", 2);

          const response = await fetch("/game/preview-teams", {
            method: "POST",
            body: formData,
            headers: {
              "X-Requested-With": "XMLHttpRequest",
            },
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const data = await response.json();

          if (data.success) {
            this.previewTeams = data.teams;

            // 🆕 ذخیره ترکیب تیم‌ها برای ارسال به سرور
            this.teamAssignments = data.teams.map((team) => ({
              team_number: team.team_number,
              player_ids: team.player_ids,
            }));

            log("✅", "Team preview loaded", data.teams);
            log("📋", "Team assignments saved", this.teamAssignments);
          } else {
            log("❌", "Team preview failed", data.error);
            this.previewTeams = [];
            this.teamAssignments = null;
          }
        } catch (error) {
          log("❌", "Team preview error", error);
          this.previewTeams = [];
          this.teamAssignments = null;
        } finally {
          this.isLoadingTeams = false;
        }
      },

      // 🆕 متد جدید برای دریافت hidden input value
      getTeamAssignmentsJson() {
        if (!this.teamAssignments) return "";
        return JSON.stringify(this.teamAssignments);
      },

      previewDebounceTimer: null,
    };
  };
})();
