/**
 * 📡 SSE Client - نسخه‌ی ساده‌شده (اتصال مستقیم برای همه‌ی تب‌ها)
 * 🆕 برای رفع مشکل عدم برقراری اتصال در حالت Slave
 */
(function () {
    if (typeof window.SSE !== "undefined") {
        console.log("⚠️ SSE Client already loaded");
        return;
    }

    class SSEClient {
        constructor() {
            this.connections = new Map();
            this.listeners = new Map();
            this.pendingListeners = new Map();

            // BroadcastChannel برای ارتباط بین تب‌ها (اختیاری)
            this.broadcastChannel = null;
            try {
                this.broadcastChannel = new BroadcastChannel("uno_sse_channel");
                this.broadcastChannel.onmessage = (event) =>
                    this._handleBroadcast(event);
            } catch (e) {
                console.warn("⚠️ BroadcastChannel not supported");
            }

            // ثبت تب (برای استفاده‌های آتی)
            this.myId = "tab_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
            this._registerTab();
        }

        _registerTab() {
            try {
                const tabs = JSON.parse(localStorage.getItem("uno_sse_tabs") || "{}");
                tabs[this.myId] = { timestamp: Date.now() };
                localStorage.setItem("uno_sse_tabs", JSON.stringify(tabs));
            } catch (e) {}
        }

        _unregisterTab() {
            try {
                const tabs = JSON.parse(localStorage.getItem("uno_sse_tabs") || "{}");
                delete tabs[this.myId];
                localStorage.setItem("uno_sse_tabs", JSON.stringify(tabs));
            } catch (e) {}
        }

        _handleBroadcast(event) {
            // برای هماهنگی بین تب‌ها (اختیاری)
            const { type, channel, eventType, data, senderId } = event.data;
            if (senderId === this.myId) return;
            if (type === "sse_event") {
                const key = `${channel}:${eventType}`;
                const callbacks = this.listeners.get(key) || [];
                callbacks.forEach((cb) => {
                    try { cb(data); } catch (error) { console.error(error); }
                });
            }
        }

        _broadcastEvent(channel, eventType, data) {
            if (!this.broadcastChannel) return;
            try {
                this.broadcastChannel.postMessage({
                    type: "sse_event",
                    channel,
                    eventType,
                    data,
                    senderId: this.myId,
                });
            } catch (e) {}
        }

        _getLastEventId(channel) {
            try {
                return parseInt(localStorage.getItem(`sse_last_event_id_${channel}`) || "0", 10);
            } catch (e) { return 0; }
        }

        _setLastEventId(channel, eventId) {
            try {
                localStorage.setItem(`sse_last_event_id_${channel}`, String(eventId));
            } catch (e) {}
        }

        /**
         * 🔗 برقراری اتصال (همیشه مستقیم، بدون Master/Slave)
         */
        connect(channel, url, options = {}) {
            if (this.connections.has(channel)) {
                console.log(`🔌 Disconnecting existing connection for ${channel}`);
                this.disconnect(channel);
            }

            // تکمیل URL نسبی با BASE_URL
            if (url.startsWith("/") && window.BASE_URL) {
                url = window.BASE_URL + url;
            }

            console.log(`🔗 connect: channel=${channel}, url=${url}`);

            const connection = {
                url,
                channel,
                options,
                ready: false,
                eventSource: null,
                realConnection: false,
                reconnectAttempts: 0,
                maxReconnectAttempts: 100,
                baseReconnectDelay: 2000,
                maxReconnectDelay: 60000,
                attachedListeners: new Set(),
            };

            this.connections.set(channel, connection);

            // 🆕 همیشه اتصال واقعی را برقرار کن (بدون شرط Master)
            console.log(`🔌 Connecting real SSE for ${channel} (direct mode)`);
            this._connectReal(channel, connection);

            return connection;
        }

        /**
         * اتصال واقعی SSE
         */
        _connectReal(channel, connection) {
            if (connection.realConnection) return;

            const lastEventId = this._getLastEventId(channel);
            const separator = connection.url.includes("?") ? "&" : "?";
            const urlWithLastEventId =
                lastEventId > 0
                    ? `${connection.url}${separator}last_event_id=${lastEventId}`
                    : connection.url;

            console.log(`🔌 Creating EventSource for ${channel} (lastEventId: ${lastEventId})`);
            console.log(`🔌 URL: ${urlWithLastEventId}`);

            try {
                const eventSource = new EventSource(urlWithLastEventId);
                connection.eventSource = eventSource;
                connection.realConnection = true;

                eventSource.onopen = () => {
                    console.log(`✅ SSE connected: ${channel}`);
                    connection.ready = true;
                    connection.reconnectAttempts = 0;
                    this._processPendingListeners(channel);
                    this._reattachExistingListeners(channel);
                };

                eventSource.onerror = (error) => {
                    console.log(`❌ SSE error on ${channel}:`, error);
                    if (eventSource.readyState === 2) {
                        console.log(`🔌 SSE closed: ${channel}`);
                    }
                    connection.ready = false;
                    connection.realConnection = false;
                    eventSource.close();

                    if (connection.reconnectAttempts < connection.maxReconnectAttempts) {
                        connection.reconnectAttempts++;
                        const delay = Math.min(
                            connection.baseReconnectDelay * Math.pow(1.5, connection.reconnectAttempts - 1),
                            connection.maxReconnectDelay
                        );
                        console.log(`🔄 Reconnecting in ${delay}ms (attempt ${connection.reconnectAttempts})`);
                        setTimeout(() => {
                            if (this.connections.has(channel)) {
                                this._connectReal(channel, connection);
                            }
                        }, delay);
                    }
                };

                this._reattachExistingListeners(channel);
            } catch (error) {
                console.error(`❌ Failed to create EventSource for ${channel}:`, error);
                connection.realConnection = false;
            }
        }

        _reattachExistingListeners(channel) {
            const connection = this.connections.get(channel);
            if (!connection || !connection.eventSource) return;
            for (const [key, callbacks] of this.listeners.entries()) {
                if (key.startsWith(`${channel}:`)) {
                    const eventType = key.substring(channel.length + 1);
                    this._attachListener(connection, eventType, key);
                }
            }
        }

        _attachListener(connection, eventType, key) {
            if (connection.attachedListeners.has(key)) return;
            connection.attachedListeners.add(key);

            console.log(`📎 Attaching listener for ${key} on ${connection.channel}`);

            connection.eventSource.addEventListener(eventType, (event) => {
                console.log(`📨 Event received on ${connection.channel}: ${eventType}`, event);
                let data;
                try {
                    data = JSON.parse(event.data);
                } catch (e) {
                    data = event.data;
                }
                if (event.lastEventId) {
                    this._setLastEventId(connection.channel, parseInt(event.lastEventId, 10));
                }
                // Broadcast به سایر تب‌ها
                this._broadcastEvent(connection.channel, eventType, data);
                // اجرای callback‌ها
                const callbacks = this.listeners.get(key) || [];
                callbacks.forEach((cb) => {
                    try { cb(data, event); } catch (error) { console.error(error); }
                });
            });
        }

        on(channel, eventType, callback) {
            const key = `${channel}:${eventType}`;
            if (!this.listeners.has(key)) {
                this.listeners.set(key, []);
            }
            this.listeners.get(key).push(callback);

            const connection = this.connections.get(channel);
            if (connection && connection.ready && connection.eventSource) {
                this._attachListener(connection, eventType, key);
            } else {
                if (!this.pendingListeners.has(channel)) {
                    this.pendingListeners.set(channel, []);
                }
                this.pendingListeners.get(channel).push({ eventType, key });
            }
            return this;
        }

        _processPendingListeners(channel) {
            const connection = this.connections.get(channel);
            if (!connection || !connection.eventSource) return;
            const pending = this.pendingListeners.get(channel) || [];
            pending.forEach(({ eventType, key }) => {
                this._attachListener(connection, eventType, key);
            });
            this.pendingListeners.delete(channel);
        }

        disconnect(channel) {
            const connection = this.connections.get(channel);
            if (connection) {
                if (connection.eventSource) {
                    connection.eventSource.close();
                }
                this.connections.delete(channel);
                this.pendingListeners.delete(channel);
                console.log(`🔌 Disconnected: ${channel}`);
            }
        }

        disconnectAll() {
            for (const channel of this.connections.keys()) {
                this.disconnect(channel);
            }
        }

        startHeartbeat(page = "") {
            if (this.heartbeatInterval) {
                clearInterval(this.heartbeatInterval);
            }
            let heartbeatUrl = "/sse/heartbeat";
            if (window.BASE_URL) {
                heartbeatUrl = window.BASE_URL + heartbeatUrl;
            }
            this.heartbeatInterval = setInterval(async () => {
                try {
                    await fetch(heartbeatUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: `page=${encodeURIComponent(page)}`,
                    });
                } catch (error) {
                    console.error("❌ Heartbeat error:", error);
                }
            }, 60000);
        }

        stopHeartbeat() {
            if (this.heartbeatInterval) {
                clearInterval(this.heartbeatInterval);
                this.heartbeatInterval = null;
            }
        }
    }

    window.SSEClient = SSEClient;
    window.SSE = new SSEClient();

    window.addEventListener("beforeunload", () => {
        if (window.SSE) {
            window.SSE.disconnectAll();
            window.SSE.stopHeartbeat();
            window.SSE._unregisterTab();
        }
    });

    console.log("🚀 SSE Client initialized (direct mode)");
})();