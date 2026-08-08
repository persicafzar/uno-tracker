/**
 * 🎵 Sound Manager - نسخه نهایی با Lazy Initialization
 *
 * 🆕 اصلاحات ریشه‌ای:
 * - Lazy initialization (بعد از تعریف SSE_SOUND_CONFIG)
 * - پشتیبانی از retry بعد از تعامل کاربر
 * - رفع مشکل Autoplay
 */
class SoundManager {
  constructor() {
    this.soundUrls = {};
    this.enabled = true;
    this.volume = 0.7;
    this.eventConfig = {};
    this.soundFiles = {};
    this._initialized = false;
    this._pendingPlays = [];
    this._interactionListenerAdded = false;

    // بارگذاری تنظیمات کاربر
    const savedEnabled = localStorage.getItem("sound_enabled");
    const savedVolume = localStorage.getItem("sound_volume");
    if (savedEnabled !== null) this.enabled = savedEnabled === "true";
    if (savedVolume !== null) this.volume = parseFloat(savedVolume);

    console.log("🎵 SoundManager created (waiting for config)");

    // 🆕 بارگذاری خودکار بعد از 0ms
    setTimeout(() => this.loadConfig(), 0);
  }

  /**
   * 🆕 بارگذاری config (باید بعد از تعریف SSE_SOUND_CONFIG فراخوانی شود)
   */
  loadConfig() {
    // اگر قبلاً مقداردهی شده و config خالی نیست، برگرد
    if (this._initialized && Object.keys(this.eventConfig).length > 0) {
      console.log("⚠️ SoundManager already initialized with config");
      return;
    }

    // دریافت از window
    this.soundFiles = window.SOUND_FILES || {
      default: "/assets/sounds/default.mp3",
    };
    this.eventConfig = window.SSE_SOUND_CONFIG || {};

    // پر کردن soundUrls
    this.soundUrls = {};
    Object.entries(this.soundFiles).forEach(([name, url]) => {
      this.soundUrls[name] = url;
    });

    this._initialized = true;
    console.log(
      `🎵 Sound Manager ready: ${Object.keys(this.soundUrls).length} sounds`,
    );
    console.log(`🎵 Event config keys:`, Object.keys(this.eventConfig));

    // پخش صداهای در انتظار
    this._playPending();
  }

  updateEventConfig(newConfig) {
    this.eventConfig = newConfig || {};
    console.log("🎵 Event config updated:", this.eventConfig);
  }

  /**
   * 🎯 پخش صدا برای رویداد SSE
   */
  playForEvent(eventName, data = {}) {
    if (!this.enabled) {
      console.log(`🔕 Sound disabled globally: ${eventName}`);
      return;
    }

    // 🆕 اگر config خالی است، دوباره بارگذاری کن
    if (!this._initialized || Object.keys(this.eventConfig).length === 0) {
      console.log(`⏳ Config not loaded, reloading...`);
      this.loadConfig();
      if (Object.keys(this.eventConfig).length === 0) {
        console.warn(`⚠️ Config still empty after reload, using defaults`);
      }
    }

    let config = null;
    let soundName = "default";
    let isEnabled = true;

    // هندل ویژه game_status_changed
    if (eventName === "game_status_changed" && data.status) {
      const parentConfig = this.eventConfig[eventName] || {};
      if (data.status === "paused") {
        config = parentConfig.paused;
      } else if (data.status === "active") {
        config = parentConfig.resumed;
      }
    } else {
      config = this.eventConfig[eventName];
      // Fallback هوشمند
      if (!config) {
        if (eventName === "round_winner" || eventName === "round_loser") {
          config = this.eventConfig["round_recorded"];
          console.log(`🔄 Fallback: ${eventName} → round_recorded`);
        } else if (eventName === "game_winner" || eventName === "game_loser") {
          config = this.eventConfig["game_finished"];
          console.log(`🔄 Fallback: ${eventName} → game_finished`);
        }
      }
    }

    if (!config) {
      soundName = "default";
      isEnabled = true;
      console.warn(`⚠️ No config for "${eventName}", using default`);
    } else {
      isEnabled = config.enabled !== false;
      soundName = config.sound || "default";
      // اطمینان از اینکه soundName با پسوند باشد (اگر نبود اضافه کن)
      if (!soundName.includes(".")) {
        soundName = soundName + ".mp3";
      }
    }

    if (!isEnabled) {
      console.log(`🔕 Sound disabled for: ${eventName}`);
      return;
    }

    console.log(`🎵 Event: ${eventName} → Sound: ${soundName}`);
    this.play(soundName);
  }

  /**
   * 🎵 پخش یک صدا با پشتیبانی از retry بعد از تعامل
   */
  play(soundName, options = {}) {
    if (!this.enabled) return;

    const audioUrl = this._resolveUrl(soundName);
    if (!audioUrl) {
      console.warn(`⚠️ Sound not found: "${soundName}"`);
      return;
    }

    try {
      const audio = new Audio(audioUrl);
      audio.volume =
        options.volume !== undefined ? options.volume : this.volume;
      if (options.loop) audio.loop = true;

      const playPromise = audio.play();
      if (playPromise !== undefined) {
        playPromise
          .then(() => {
            console.log(`🔊 Playing: ${soundName} → ${audioUrl}`);
          })
          .catch((error) => {
            if (error.name === "NotAllowedError") {
              console.log(
                `🔕 Autoplay blocked: ${soundName} - will retry on interaction`,
              );
              // 🆕 اضافه به صف برای پخش بعد از تعامل
              this._pendingPlays.push({
                type: "sound",
                soundName,
                options,
                timestamp: Date.now(),
              });

              if (!this._interactionListenerAdded) {
                this._addInteractionListener();
              }
            } else {
              console.error("❌ Play error:", error);
            }
          });
      }
    } catch (error) {
      console.error("❌ Sound error:", error);
    }
  }

  /**
   * 🆕 پخش صداهای در انتظار بعد از تعامل کاربر
   */
  _playPending() {
    if (this._pendingPlays.length === 0) return;

    const now = Date.now();
    const validPlays = this._pendingPlays.filter((p) => {
      // فقط صداها/رویدادهای کمتر از 5 ثانیه پیش را پخش کن
      if (!p.timestamp) return true;
      return now - p.timestamp < 5000;
    });

    this._pendingPlays = [];

    validPlays.forEach((item) => {
      if (item.type === "event") {
        this.playForEvent(item.eventName, item.data);
      } else if (item.type === "sound") {
        this.play(item.soundName, item.options);
      }
    });
  }

  /**
   * تبدیل soundName به URL
   */
  _resolveUrl(soundName) {
    if (!soundName) return null;

    // URL کامل
    if (soundName.startsWith("/") || soundName.startsWith("http")) {
      return soundName;
    }

    // با فرمت (مثلاً round-win.mp3)
    if (soundName.includes(".")) {
      const nameWithoutExt = soundName.substring(0, soundName.lastIndexOf("."));
      // ابتدا در soundUrls جستجو کن
      if (this.soundUrls[nameWithoutExt]) return this.soundUrls[nameWithoutExt];
      if (this.soundUrls[soundName]) return this.soundUrls[soundName];
      // در نهایت به پوشه sounds اضافه کن
      return "/assets/sounds/" + soundName;
    }

    // بدون فرمت
    if (this.soundUrls[soundName]) return this.soundUrls[soundName];
    return "/assets/sounds/" + soundName + ".mp3";
  }

  /**
   * 🆕 فعال‌سازی با تعامل کاربر (فقط یک بار)
   */
  _addInteractionListener() {
    if (this._interactionListenerAdded) return;

    this._interactionListenerAdded = true;

    const enableSound = () => {
      console.log("👆 User interaction detected, playing pending sounds");
      this._playPending();
    };

    // 🆕 استفاده از { once: true } برای حذف خودکار listener
    document.addEventListener("click", enableSound, { once: true });
    document.addEventListener("touchstart", enableSound, { once: true });
    document.addEventListener("keydown", enableSound, { once: true });
  }

  toggle() {
    this.enabled = !this.enabled;
    localStorage.setItem("sound_enabled", this.enabled);
    console.log(`🔊 Sound ${this.enabled ? "enabled" : "disabled"}`);
    return this.enabled;
  }

  setVolume(volume) {
    this.volume = Math.max(0, Math.min(1, volume));
    localStorage.setItem("sound_volume", this.volume);
  }

  // متدهای راحت
  playRoundWin() {
    this.playForEvent("round_winner");
  }
  playRoundLose() {
    this.playForEvent("round_loser");
  }
  playGameWin() {
    this.playForEvent("game_winner");
  }
  playGameStart() {
    this.playForEvent("game_started");
  }
  playGamePause() {
    this.playForEvent("game_status_changed", { status: "paused" });
  }
  playGameResume() {
    this.playForEvent("game_status_changed", { status: "active" });
  }
  playRoundRecorded() {
    this.playForEvent("round_recorded");
  }
  playDefault() {
    this.play("default", { volume: 0.5 });
  }
  playNotification() {
    this.playForEvent("notification");
  }
  playSystemMessage() {
    this.playForEvent("system_message");
  }
}

// 🆕 ایجاد instance بدون بارگذاری config
window.SoundManager = new SoundManager();
// اطمینان از اینکه متد loadConfig وجود دارد
if (typeof window.SoundManager.loadConfig !== "function") {
  console.error("❌ loadConfig method missing!");
}

// API ساده
window.playSound = (soundName, options) =>
  window.SoundManager.play(soundName, options);
window.playSoundForEvent = (eventName, data) =>
  window.SoundManager.playForEvent(eventName, data);
window.toggleSound = () => window.SoundManager.toggle();
window.setSoundVolume = (volume) => window.SoundManager.setVolume(volume);
