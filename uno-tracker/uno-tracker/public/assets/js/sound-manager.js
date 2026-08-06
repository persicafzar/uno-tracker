/**
 * 🎵 Sound Manager - نسخه نهایی
 *
 * 🆕 اصلاحات:
 * - هر بار Audio object جدید می‌سازد (پخش همزمان)
 * - پشتیبانی از soundName با یا بدون فرمت
 * - ذخیره URL واقعی به جای Audio object
 */
class SoundManager {
  constructor() {
    this.soundUrls = {}; // 🆕 ذخیره URL به جای Audio object
    this.enabled = true;
    this.volume = 0.7;

    // لیست صداها از window.SOUND_FILES
    this.soundFiles = window.SOUND_FILES || {
      default: "/assets/sounds/default.mp3",
    };

    // تنظیمات رویدادها
    this.eventConfig = window.SSE_SOUND_CONFIG || {};

    this._init();
  }

  _init() {
    const savedEnabled = localStorage.getItem("sound_enabled");
    const savedVolume = localStorage.getItem("sound_volume");

    if (savedEnabled !== null) {
      this.enabled = savedEnabled === "true";
    }
    if (savedVolume !== null) {
      this.volume = parseFloat(savedVolume);
    }

    // 🆕 ذخیره URL ها
    Object.entries(this.soundFiles).forEach(([name, url]) => {
      this.soundUrls[name] = url;
    });

    console.log(
      `🎵 Sound Manager ready: ${Object.keys(this.soundUrls).length} sounds`,
    );
  }

  updateEventConfig(newConfig) {
    this.eventConfig = newConfig || {};
  }

  /**
   * 🎯 پخش صدا برای رویداد SSE
   */
  /**
   * 🎯 پخش صدا برای رویداد SSE - نسخه هوشمند با Fallback
   */
  playForEvent(eventName, data = {}) {
    if (!this.enabled) return;

    let config = null;
    let soundName = "default";
    let isEnabled = true;

    // 🎯 هندل ویژه game_status_changed (با زیرمجموعه)
    if (eventName === "game_status_changed" && data.status) {
      const parentConfig = this.eventConfig[eventName] || {};
      if (data.status === "paused") {
        config = parentConfig.paused;
      } else if (data.status === "active") {
        config = parentConfig.resumed;
      }
    } else {
      // 🆕 ابتدا دنبال کلید دقیق بگرد
      config = this.eventConfig[eventName];

      // 🆕 Fallback هوشمند: اگر کلید دقیق نبود، از کلید عمومی استفاده کن
      if (!config) {
        // برای round_winner/round_loser → round_recorded
        if (eventName === "round_winner" || eventName === "round_loser") {
          config = this.eventConfig["round_recorded"];
          console.log(`🔄 Fallback: ${eventName} → round_recorded`);
        }
        // برای game_winner/game_loser → game_finished
        else if (eventName === "game_winner" || eventName === "game_loser") {
          config = this.eventConfig["game_finished"];
          console.log(`🔄 Fallback: ${eventName} → game_finished`);
        }
      }
    }
    // 🎯 استخراج تنظیمات
    if (!config) {
      soundName = "default";
      isEnabled = true;
      console.log(`⚠️ No config for ${eventName}, using default`);
    } else {
      isEnabled = config.enabled !== false;
      soundName = config.sound || "default";
    }

    if (!isEnabled) {
      console.log(`🔕 Sound disabled for: ${eventName}`);
      return;
    }

    console.log(`🎵 Event: ${eventName} → Sound: ${soundName}`);
    this.play(soundName);
  }

  /**
   * 🎵 پخش یک صدا
   *
   * 🆕 هر بار Audio جدید می‌سازد + پشتیبانی از نام با/بدون فرمت
   */
  play(soundName, options = {}) {
    if (!this.enabled) return;

    // 🆕 تبدیل soundName به URL
    const audioUrl = this._resolveUrl(soundName);

    if (!audioUrl) {
      console.warn(`⚠️ Sound not found: "${soundName}"`);
      return;
    }

    try {
      // 🆕 هر بار Audio object جدید بساز (پخش همزمان)
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
              console.log(`🔕 Autoplay blocked: ${soundName}`);
              this._pendingPlay = soundName;
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
   * 🆕 تبدیل soundName به URL
   *
   * پشتیبانی از:
   *   "game-pause"       → "/assets/sounds/game-pause.mp3"
   *   "game-pause.mp3"   → "/assets/sounds/game-pause.mp3"
   *   "/assets/..."       → "/assets/..."
   */
  _resolveUrl(soundName) {
    if (!soundName) return null;

    // ۱. اگر URL کامل است، مستقیم برگردان
    if (soundName.startsWith("/") || soundName.startsWith("http")) {
      return soundName;
    }

    // ۲. اگر فرمت دارد (مثلاً "game-pause.mp3")
    if (soundName.includes(".")) {
      const nameWithoutExt = soundName.substring(0, soundName.lastIndexOf("."));
      // ابتدا با نام بدون فرمت جستجو کن
      if (this.soundUrls[nameWithoutExt]) {
        return this.soundUrls[nameWithoutExt];
      }
      // اگر نبود، با نام کامل جستجو کن
      if (this.soundUrls[soundName]) {
        return this.soundUrls[soundName];
      }
      // اگر هیچکدام نبود، URL را بساز
      return "/assets/sounds/" + soundName;
    }

    // ۳. اگر فرمت ندارد (مثلاً "game-pause")
    if (this.soundUrls[soundName]) {
      return this.soundUrls[soundName];
    }

    // ۴. Fallback: URL را بساز با .mp3
    return "/assets/sounds/" + soundName + ".mp3";
  }

  _addInteractionListener() {
    const enableSound = () => {
      if (this._pendingPlay) {
        this.play(this._pendingPlay);
        this._pendingPlay = null;
      }
      this._interactionListenerAdded = false;
    };
    this._interactionListenerAdded = true;
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

window.SoundManager = new SoundManager();

window.playSound = (soundName, options) =>
  window.SoundManager.play(soundName, options);
window.playSoundForEvent = (eventName, data) =>
  window.SoundManager.playForEvent(eventName, data);
window.toggleSound = () => window.SoundManager.toggle();
window.setSoundVolume = (volume) => window.SoundManager.setVolume(volume);
