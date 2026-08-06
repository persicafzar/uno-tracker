<?php

/**
 * 🎨 توابع کمکی عمومی پروژه (UNO Tracker)
 * 
 * ============================================
 * 🐛 توابع Debug (مشابه Laravel dd/dump)
 * ============================================
 * - dd(...$vars)   : Dump and Die (نمایش و توقف)
 * - dump(...$vars) : فقط نمایش (ادامه اجرای کد)
 * - d(...$vars)    : Alias کوتاه dump
 * - sql($query, $params)  : نمایش کوئری SQL با syntax highlighting
 * - trace()        : نمایش Call Stack
 * 
 * ============================================
 * 🔗 توابع URL و Asset
 * ============================================
 * - url($path, $params)   : تولید URL کامل
 * - base_url()            : URL پایه پروژه
 * - asset($path)          : مسیر فایل‌های استاتیک
 * - current_url($withQuery): URL فعلی صفحه
 * - previous_url($fallback): URL صفحه قبلی (Referer)
 * 
 * ============================================
 * 🌍 توابع Environment و Utility
 * ============================================
 * - env($key, $default)   : خواندن متغیرهای محیطی
 * - config($key, $default): خواندن تنظیمات از config/
 * - e($value)             : Escape HTML (htmlspecialchars)
 * - csrf_token()          : گرفتن CSRF Token
 * - csrf_field()          : تولید HTML input برای CSRF
 * - redirect($url, $code) : ریدایرکت به URL دیگر
 * - abort($code, $msg)    : توقف با HTTP Error
 * - class_basename($class): گرفتن نام کلاس بدون namespace
 * 
 * ============================================
 * 🎮 توابع اختصاصی UNO Tracker
 * ============================================
 * - is_online()           : آیا سیستم آنلاین است؟
 */

// ============================================
// 🐛 توابع Debug
// ============================================

if (!function_exists('_debug_render')) {

    /**
     * 🎨 رندر HTML زیبا برای یک متغیر
     */
    function _debug_render($var, string $label = '', int $depth = 0): string
    {
        $maxDepth = 8;

        // رنگ‌بندی بر اساس نوع
        $colors = [
            'string'   => '#e879a0',
            'integer'  => '#6cb6ff',
            'double'   => '#6cb6ff',
            'boolean'  => '#ff7b72',
            'NULL'     => '#8b949e',
            'array'    => '#ffa657',
            'object'   => '#d2a8ff',
            'resource' => '#7ee787',
        ];

        $type = gettype($var);
        $color = $colors[$type] ?? '#c9d1d9';

        // 🆕 اصلاح PHP 8.1+ compatibility
        $safeEscape = function ($str) {
            return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
        };

        // String
        if (is_string($var)) {
            $len = mb_strlen($var);
            $escaped = $safeEscape($var);
            return "<span style='color:{$color}'>string</span>" .
                "<span style='color:#8b949e'>({$len})</span> " .
                "<span style='color:#a5d6ff'>\"{$escaped}\"</span>";
        }

        // Integer / Float
        if (is_int($var) || is_float($var)) {
            return "<span style='color:{$color}'>{$type}</span> " .
                "<span style='color:#79c0ff;font-weight:bold'>{$var}</span>";
        }

        // Boolean
        if (is_bool($var)) {
            $val = $var ? 'true' : 'false';
            return "<span style='color:{$color};font-weight:bold'>{$val}</span>";
        }

        // NULL
        if (is_null($var)) {
            return "<span style='color:{$color};font-weight:bold'>null</span>";
        }

        // Resource
        if (is_resource($var)) {
            return "<span style='color:{$color}'>resource</span>(" . get_resource_type($var) . ")";
        }

        // Array
        if (is_array($var)) {
            $count = count($var);
            if ($count === 0) {
                return "<span style='color:{$color}'>array</span>:[]";
            }

            $id = 'arr_' . md5(microtime() . rand());
            $html = "<span style='color:{$color}'>array</span>" .
                "<span style='color:#8b949e'>({$count})</span> " .
                "<a href='#' onclick='var el=document.getElementById(\"{$id}\"); el.style.display=el.style.display===\"none\"?\"block\":\"none\"; return false;' " .
                "style='color:#58a6ff;text-decoration:none;font-size:11px'>[▼ toggle]</a>" .
                "<div id='{$id}' style='margin-left:20px;border-left:2px solid #30363d;padding-left:12px;margin-top:4px'>";

            if ($depth >= $maxDepth) {
                $html .= "<span style='color:#8b949e'>... (max depth)</span>";
            } else {
                $items = array_slice($var, 0, 100, true);
                foreach ($items as $key => $value) {
                    $keyStr = is_int($key)
                        ? "<span style='color:#79c0ff'>{$key}</span>"
                        : "<span style='color:#ffa657'>\"" . $safeEscape((string)$key) . "\"</span>";
                    $html .= "<div style='padding:2px 0'>{$keyStr} => " .
                        _debug_render($value, '', $depth + 1) . "</div>";
                }
                if ($count > 100) {
                    $html .= "<div style='padding:2px 0;color:#8b949e'>... and " . ($count - 100) . " more items</div>";
                }
            }
            $html .= "</div>";
            return $html;
        }

        // Object
        if (is_object($var)) {
            $class = get_class($var);

            $id = 'obj_' . md5(microtime() . rand());
            $html = "<span style='color:{$color}'>object</span>" .
                "(<span style='color:#d2a8ff;font-weight:bold'>{$class}</span>) " .
                "<a href='#' onclick='var el=document.getElementById(\"{$id}\"); el.style.display=el.style.display===\"none\"?\"block\":\"none\"; return false;' " .
                "style='color:#58a6ff;text-decoration:none;font-size:11px'>[▼ toggle]</a>" .
                "<div id='{$id}' style='margin-left:20px;border-left:2px solid #30363d;padding-left:12px;margin-top:4px;display:none'>";

            if ($depth >= $maxDepth) {
                $html .= "<span style='color:#8b949e'>... (max depth)</span>";
            } else {
                try {
                    $reflection = new ReflectionObject($var);
                    $properties = $reflection->getProperties();

                    foreach ($properties as $prop) {
                        $prop->setAccessible(true);
                        try {
                            $value = $prop->getValue($var);
                        } catch (\Throwable $e) {
                            $value = "[UNREADABLE: {$e->getMessage()}]";
                        }
                        $visibility = $prop->isPublic() ? '+' : ($prop->isProtected() ? '#' : '-');
                        $visColor = $prop->isPublic() ? '#7ee787' : ($prop->isProtected() ? '#ffa657' : '#ff7b72');

                        $html .= "<div style='padding:2px 0'>" .
                            "<span style='color:{$visColor}'>{$visibility}</span> " .
                            "<span style='color:#ffa657'>\${$prop->getName()}</span>: " .
                            (is_string($value) && strpos($value, '[UNREADABLE') === 0
                                ? "<span style='color:#ff7b72'>{$value}</span>"
                                : _debug_render($value, '', $depth + 1)) .
                            "</div>";
                    }
                } catch (Throwable $e) {
                    $html .= "<span style='color:#ff7b72'>Reflection error: {$safeEscape($e->getMessage())}</span>";
                }
            }
            $html .= "</div>";
            return $html;
        }

        return "<span style='color:#8b949e'>unknown</span>";
    }

    /**
     * 🎨 رندر Container اصلی
     */
    function _debug_container(array $vars, array $locations, bool $die = false): void
    {
        // جلوگیری از خروجی HTML در AJAX خالص
        if (!headers_sent()) {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
            $isHtmx = !empty($_SERVER['HTTP_HX_REQUEST']);

            if ($isAjax && !$isHtmx) {
                echo "\n\n========== DEBUG ==========\n";
                foreach ($vars as $i => $var) {
                    echo "[$i] " . print_r($var, true) . "\n";
                }
                echo "===========================\n\n";
                if ($die) exit;
                return;
            }
        }

        $uid = 'dd_' . md5(microtime() . rand());

        echo "<style>
        .debug-container-{$uid} {
            font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.6;
            background: #0d1117;
            color: #c9d1d9;
            border: 2px solid #30363d;
            border-radius: 12px;
            margin: 16px 8px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            overflow: hidden;
            direction: ltr;
            text-align: left;
            max-width: calc(100% - 16px);
        }
        .debug-header-{$uid} {
            background: linear-gradient(90deg, #161b22, #1f262e);
            padding: 10px 16px;
            border-bottom: 1px solid #30363d;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .debug-title-{$uid} {
            font-weight: 700;
            font-size: 13px;
            color: #f0f6fc;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .debug-badge-{$uid} {
            background: " . ($die ? '#da3633' : '#238636') . ";
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .debug-info-{$uid} {
            font-size: 11px;
            color: #8b949e;
            font-family: monospace;
        }
        .debug-body-{$uid} {
            padding: 16px;
            max-height: 600px;
            overflow: auto;
        }
        .debug-var-{$uid} {
            margin-bottom: 12px;
            padding: 12px;
            background: #161b22;
            border-radius: 8px;
            border-left: 3px solid #58a6ff;
        }
        .debug-var-label-{$uid} {
            font-size: 11px;
            color: #8b949e;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .debug-actions-{$uid} {
            padding: 8px 16px;
            background: #161b22;
            border-top: 1px solid #30363d;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .debug-btn-{$uid} {
            background: #21262d;
            color: #c9d1d9;
            border: 1px solid #30363d;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }
        .debug-btn-{$uid}:hover {
            background: #30363d;
            border-color: #58a6ff;
        }
        .debug-container-{$uid} ::-webkit-scrollbar { width: 10px; height: 10px; }
        .debug-container-{$uid} ::-webkit-scrollbar-track { background: #0d1117; }
        .debug-container-{$uid} ::-webkit-scrollbar-thumb { background: #30363d; border-radius: 5px; }
        .debug-container-{$uid} ::-webkit-scrollbar-thumb:hover { background: #484f58; }
        </style>";

        // 🎯 تعریف توابع JavaScript (فقط یکبار در صفحه)
        echo "<script>
(function() {
    if (typeof window.__debugHelpersInitialized !== 'undefined') return;
    window.__debugHelpersInitialized = true;
    
    // 🎯 Toggle visibility کل container
    window.toggleDebugVisibility = function(id) {
        var el = document.getElementById(id);
        if (!el) return;
        var current = el.style.display;
        el.style.display = (current === 'none') ? '' : 'none';
        window.updateDebugFloatingButton();
    };
    
    // 🎯 به‌روزرسانی دکمه شناور
    window.updateDebugFloatingButton = function() {
        var btn = document.getElementById('debug-floating-btn');
        if (!btn) return;
        // 🆕 فقط container های اصلی (debug-box) را بشمار
        var boxes = document.querySelectorAll('.debug-box');
        var total = boxes.length;
        var visible = 0;
        boxes.forEach(function(box) {
            if (box.style.display !== 'none') visible++;
        });
        btn.innerHTML = '🐛 ' + visible + '/' + total;
        btn.style.background = visible > 0 ? '#1f6feb' : '#6e7681';
    };
    
    // 🎯 Toggle همه box ها (فقط container اصلی)
    window.toggleAllDebugBoxes = function() {
        var boxes = document.querySelectorAll('.debug-box');
        if (boxes.length === 0) return;
        
        var allHidden = true;
        boxes.forEach(function(box) {
            if (box.style.display !== 'none') allHidden = false;
        });
        
        boxes.forEach(function(box) {
            box.style.display = allHidden ? '' : 'none';
        });
        window.updateDebugFloatingButton();
    };
    
    // 🎯 ایجاد دکمه شناور
    if (!document.getElementById('debug-floating-btn')) {
        var btn = document.createElement('div');
        btn.id = 'debug-floating-btn';
        btn.innerHTML = '🐛 0/0';
        btn.style.cssText = 'position:fixed;bottom:20px;left:20px;z-index:999999;background:#1f6feb;color:white;padding:8px 16px;border-radius:20px;cursor:pointer;font-family:monospace;font-size:13px;font-weight:bold;box-shadow:0 4px 12px rgba(0,0,0,0.4);transition:all 0.2s;user-select:none;';
        btn.title = 'کلیک: Toggle همه Debug Box ها\\nShift+کلیک: حذف همه';
        btn.onmouseenter = function() { this.style.transform = 'scale(1.1)'; };
        btn.onmouseleave = function() { this.style.transform = 'scale(1)'; };
        btn.onclick = function(e) {
            if (e.shiftKey) {
                document.querySelectorAll('.debug-box').forEach(function(box) { box.remove(); });
                window.updateDebugFloatingButton();
            } else {
                window.toggleAllDebugBoxes();
            }
        };
        document.body.appendChild(btn);
        
        var style = document.createElement('style');
        style.textContent = '@media print { #debug-floating-btn, .debug-box { display: none !important; } }';
        document.head.appendChild(style);
    }
    
    setTimeout(window.updateDebugFloatingButton, 100);
})();
</script>";

        // 🆕 اضافه کردن کلاس debug-box برای شمارش صحیح
        echo "<div class='debug-container-{$uid} debug-box' id='{$uid}'>";

        // Header
        echo "<div class='debug-header-{$uid}' id='{$uid}_header'>";
        echo "<div class='debug-title-{$uid}'>";
        echo "🔍 <span>Debug Output</span>";
        echo "<span class='debug-badge-{$uid}'>" . ($die ? 'DIE' : 'DUMP') . "</span>";
        echo "<span style='color:#8b949e;font-weight:400'>(" . count($vars) . " variable" . (count($vars) > 1 ? 's' : '') . ")</span>";
        echo "</div>";

        if (!empty($locations)) {
            $loc = $locations[0];
            $file = htmlspecialchars((string)($loc['file'] ?? 'unknown'), ENT_QUOTES, 'UTF-8');
            $line = $loc['line'] ?? 0;
            $shortFile = basename($file);
            echo "<div class='debug-info-{$uid}' title='{$file}'>";
            echo "📁 <strong>{$shortFile}</strong>:{$line}";
            echo "</div>";
        }
        echo "</div>";

        // Body
        echo "<div class='debug-body-{$uid}' id='{$uid}_body'>";
        foreach ($vars as $i => $var) {
            $label = isset($locations[$i])
                ? "#{$i} @ " . basename((string)($locations[$i]['file'] ?? '')) . ":" . ($locations[$i]['line'] ?? 0)
                : "#{$i}";
            echo "<div class='debug-var-{$uid}'>";
            echo "<div class='debug-var-label-{$uid}'>" . htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8') . "</div>";
            echo _debug_render($var);
            echo "</div>";
        }
        echo "</div>";

        // 🆕 Actions (بدون دکمه Minimize)
        echo "<div class='debug-actions-{$uid}' id='{$uid}_actions' style='display:flex'>";
        echo "<button class='debug-btn-{$uid}' onclick='(function(){ var el=document.getElementById(\"{$uid}_body\"); if(el.style.maxHeight===\"none\"){el.style.maxHeight=\"600px\"}else{el.style.maxHeight=\"none\"} })()'>↕ Toggle Height</button>";
        echo "<button class='debug-btn-{$uid}' onclick='toggleDebugVisibility(\"{$uid}\")' title='مخفی کردن (از دکمه شناور برگردانید)'>👁 Hide</button>";
        if (!$die) {
            echo "<button class='debug-btn-{$uid}' onclick='document.getElementById(\"{$uid}\").remove(); updateDebugFloatingButton();' style='color:#ff7b72' title='حذف کامل'>✕ Remove</button>";
        }
        echo "</div>";

        echo "<script>setTimeout(updateDebugFloatingButton, 50);</script>";
        echo "</div>";

        if ($die) {
            exit(1);
        }
    }

    /**
     * 🔴 dd() - Dump and Die
     */
    function dd(...$vars): void
    {
        $locations = [];
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($vars as $i => $var) {
            if (isset($backtrace[$i + 1])) {
                $locations[] = [
                    'file' => $backtrace[$i + 1]['file'] ?? 'unknown',
                    'line' => $backtrace[$i + 1]['line'] ?? 0,
                ];
            }
        }

        if (empty($locations) && isset($backtrace[0])) {
            $locations[] = [
                'file' => $backtrace[0]['file'] ?? 'unknown',
                'line' => $backtrace[0]['line'] ?? 0,
            ];
        }

        _debug_container($vars, $locations, true);
    }

    /**
     * 🟢 dump() - فقط نمایش
     */
    function dump(...$vars): void
    {
        $locations = [];
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($vars as $i => $var) {
            if (isset($backtrace[$i + 1])) {
                $locations[] = [
                    'file' => $backtrace[$i + 1]['file'] ?? 'unknown',
                    'line' => $backtrace[$i + 1]['line'] ?? 0,
                ];
            }
        }

        if (empty($locations) && isset($backtrace[0])) {
            $locations[] = [
                'file' => $backtrace[0]['file'] ?? 'unknown',
                'line' => $backtrace[0]['line'] ?? 0,
            ];
        }

        _debug_container($vars, $locations, false);
    }

    /**
     * 🔵 d() - Alias کوتاه dump
     */
    function d(...$vars): void
    {
        dump(...$vars);
    }

    /**
     * 🟣 sql() - نمایش کوئری SQL با formatting
     */
    function sql(string $query, array $params = []): void
    {
        $uid = 'sql_' . md5(microtime() . rand());
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $loc = $backtrace[0] ?? [];

        $formatted = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');

        $keywords = [
            'SELECT',
            'FROM',
            'WHERE',
            'AND',
            'OR',
            'JOIN',
            'LEFT',
            'RIGHT',
            'INNER',
            'ON',
            'GROUP BY',
            'ORDER BY',
            'LIMIT',
            'INSERT',
            'UPDATE',
            'DELETE',
            'INTO',
            'VALUES',
            'SET',
            'HAVING',
            'UNION',
            'AS',
            'IN',
            'NOT',
            'NULL',
            'LIKE',
            'BETWEEN',
            'IS',
            'EXISTS',
            'DISTINCT',
            'COUNT',
            'SUM',
            'AVG'
        ];
        foreach ($keywords as $kw) {
            $formatted = preg_replace("/\\b{$kw}\\b/i", "<span style='color:#ff7b72;font-weight:bold'>{$kw}</span>", $formatted);
        }
        $formatted = preg_replace("/'([^']*)'/", "<span style='color:#a5d6ff'>'$1'</span>", $formatted);
        $formatted = preg_replace("/\\b(\\d+)\\b/", "<span style='color:#79c0ff'>$1</span>", $formatted);
        $formatted = preg_replace("/\\?/", "<span style='color:#ffa657;font-weight:bold'>?</span>", $formatted);

        echo "<style>
        .sql-{$uid} {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.7;
            background: #0d1117;
            color: #c9d1d9;
            border: 2px solid #1f6feb;
            border-radius: 12px;
            margin: 16px 8px;
            padding: 16px;
            box-shadow: 0 8px 32px rgba(31,111,235,0.3);
            direction: ltr;
            text-align: left;
            white-space: pre-wrap;
            word-break: break-word;
        }
        </style>";

        echo "<div class='sql-{$uid}'>";
        echo "<div style='color:#58a6ff;font-size:11px;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px'>";
        echo "🗄️ SQL Query";
        if (!empty($loc)) {
            $file = htmlspecialchars((string)($loc['file'] ?? ''), ENT_QUOTES, 'UTF-8');
            echo " <span style='color:#8b949e'>@ " . basename($file) . ":" . ($loc['line'] ?? '') . "</span>";
        }
        echo "</div>";
        echo $formatted;

        if (!empty($params)) {
            echo "<div style='margin-top:12px;padding-top:12px;border-top:1px solid #30363d'>";
            echo "<div style='color:#ffa657;font-size:11px;margin-bottom:4px'>PARAMETERS (" . count($params) . "):</div>";
            foreach ($params as $i => $p) {
                $type = gettype($p);
                if (is_string($p)) {
                    $val = '"' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '"';
                } elseif (is_null($p)) {
                    $val = 'NULL';
                } elseif (is_bool($p)) {
                    $val = $p ? 'true' : 'false';
                } else {
                    $val = (string)$p;
                }
                echo "<div><span style='color:#79c0ff'>[$i]</span> <span style='color:#8b949e'>({$type})</span> = <span style='color:#a5d6ff'>{$val}</span></div>";
            }
            echo "</div>";
        }

        echo "</div>";
    }

    /**
     * 🟠 trace() - نمایش Call Stack
     */
    function trace(): void
    {
        $uid = 'tr_' . md5(microtime() . rand());
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        echo "<style>
        .trace-{$uid} {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            background: #0d1117;
            color: #c9d1d9;
            border: 2px solid #f0883e;
            border-radius: 12px;
            margin: 16px 8px;
            padding: 16px;
            direction: ltr;
            text-align: left;
        }
        .trace-row-{$uid} {
            padding: 4px 8px;
            border-left: 2px solid #30363d;
            margin: 2px 0;
        }
        .trace-row-{$uid}:hover { border-left-color: #f0883e; background: #161b22; }
        </style>";

        echo "<div class='trace-{$uid}'>";
        echo "<div style='color:#f0883e;font-weight:bold;margin-bottom:8px'>📍 Call Stack (" . count($backtrace) . " frames)</div>";

        foreach ($backtrace as $i => $frame) {
            $file = isset($frame['file'])
                ? htmlspecialchars(basename((string)$frame['file']), ENT_QUOTES, 'UTF-8') . ':' . ($frame['line'] ?? 0)
                : '[internal]';
            $class = $frame['class'] ?? '';
            $type = $frame['type'] ?? '';
            $func = $frame['function'] ?? '';
            $call = $class ? "{$class}{$type}{$func}()" : "{$func}()";
            $call = htmlspecialchars($call, ENT_QUOTES, 'UTF-8');

            echo "<div class='trace-row-{$uid}'>";
            echo "<span style='color:#8b949e'>#{$i}</span> ";
            echo "<span style='color:#d2a8ff'>{$call}</span> ";
            echo "<span style='color:#8b949e'>at</span> ";
            echo "<span style='color:#79c0ff'>{$file}</span>";
            echo "</div>";
        }

        echo "</div>";
    }
}

// ============================================
// 🔗 توابع URL و Asset
// ============================================

if (!function_exists('base_url')) {
    /**
     * 🔗 گرفتن URL پایه پروژه
     * 
     * @return string URL پایه (مثلاً: https://example.com/uno)
     */
    function base_url(): string
    {
        // اولویت با BASE_URL تعریف شده در index.php
        if (defined('BASE_URL') && BASE_URL !== '') {
            return rtrim(BASE_URL, '/');
        }

        // تشخیص خودکار
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');

        if ($scriptDir === '/' || $scriptDir === '\\') {
            $scriptDir = '';
        }

        return $protocol . '://' . $host . $scriptDir;
    }
}

if (!function_exists('url')) {
    /**
     * 🔗 تولید URL کامل
     * 
     * @param string $path مسیر (مثلاً: /dashboard یا /users/123)
     * @param array $params پارامترهای GET (مثلاً: ['page' => 2])
     * @return string URL کامل
     * 
     * مثال‌ها:
     * - url('/dashboard') => https://example.com/uno/dashboard
     * - url('/search', ['q' => 'test']) => https://example.com/uno/search?q=test
     */
    function url(string $path = '', array $params = []): string
    {
        $base = base_url();
        $path = '/' . ltrim($path, '/');

        $url = $base . $path;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
}

if (!function_exists('asset')) {
    /**
     * 🎨 تولید URL فایل‌های استاتیک
     * 
     * @param string $path مسیر فایل (مثلاً: css/app.css یا js/app.js)
     * @return string URL کامل
     * 
     * مثال‌ها:
     * - asset('css/app.css') => https://example.com/uno/assets/css/app.css
     * - asset('images/logo.svg') => https://example.com/uno/assets/images/logo.svg
     */
    function asset(string $path): string
    {
        $base = base_url();
        $path = ltrim($path, '/');

        if (strpos($path, 'assets/') === 0) {
            return $base . '/' . $path;
        }

        return $base . '/assets/' . $path;
    }
}

if (!function_exists('current_url')) {
    /**
     * 🔗 گرفتن URL فعلی صفحه
     * 
     * @param bool $withQuery آیا پارامترهای GET هم لحاظ شوند؟
     * @return string URL فعلی
     */
    function current_url(bool $withQuery = true): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $url = $protocol . '://' . $host . $uri;

        if (!$withQuery) {
            $url = strtok($url, '?');
        }

        return $url;
    }
}

if (!function_exists('previous_url')) {
    /**
     * 🔗 گرفتن URL صفحه قبلی (Referer)
     * 
     * @param string $fallback اگر referer نبود، به این URL برگرد
     * @return string
     */
    function previous_url(string $fallback = '/'): string
    {
        return $_SERVER['HTTP_REFERER'] ?? url($fallback);
    }
}

// ============================================
// 🌍 توابع Environment و Utility
// ============================================

if (!function_exists('env')) {
    /**
     * 🌍 خواندن متغیرهای محیطی
     * 
     * @param string $key نام متغیر
     * @param mixed $default مقدار پیش‌فرض
     * @return mixed
     * 
     * مثال:
     * - env('APP_DEBUG', false)
     * - env('DB_HOST', 'localhost')
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }

        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * ⚙️ خواندن تنظیمات از فایل‌های config/
     * 
     * @param string $key فرمت: filename.key (مثلاً: app.name یا database.host)
     * @param mixed $default مقدار پیش‌فرض
     * @return mixed
     * 
     * مثال:
     * - config('app.name') => 'UNO Tracker'
     * - config('database.host', 'localhost')
     */
    function config(string $key, $default = null)
    {
        static $cache = [];

        $parts = explode('.', $key, 2);
        if (count($parts) !== 2) {
            return $default;
        }

        [$file, $configKey] = $parts;

        if (!isset($cache[$file])) {
            $configPath = (defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2)) . '/config/' . $file . '.php';

            if (!file_exists($configPath)) {
                return $default;
            }

            $cache[$file] = require $configPath;
        }

        return $cache[$file][$configKey] ?? $default;
    }
}

if (!function_exists('e')) {
    /**
     * 🛡️ Escape HTML (Alias برای htmlspecialchars)
     * 
     * @param mixed $value
     * @return string
     */
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * 🔒 گرفتن CSRF Token از Session
     * 
     * @return string
     */
    function csrf_token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            return '';
        }

        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * 🔒 تولید HTML input برای CSRF Token
     * 
     * @return string
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('redirect')) {
    /**
     * 🔀 ریدایرکت به URL دیگر
     * 
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    function redirect(string $url, int $statusCode = 302): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Location: ' . $url);
        }
        exit;
    }
}

if (!function_exists('abort')) {
    /**
     * 🛑 توقف با HTTP Error
     * 
     * @param int $code کد خطا (مثلاً: 404, 500)
     * @param string $message پیام
     * @return void
     */
    function abort(int $code, string $message = ''): void
    {
        if (!headers_sent()) {
            http_response_code($code);
        }

        $defaultMessages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];

        $msg = $message ?: ($defaultMessages[$code] ?? 'Error');

        echo "<h1>{$code} - {$msg}</h1>";
        exit;
    }
}

if (!function_exists('class_basename')) {
    /**
     * 📦 گرفتن نام کلاس بدون namespace
     * 
     * @param object|string $class
     * @return string
     */
    function class_basename($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}

// ============================================
// 📝 تابع لاگ با قابلیت فعال/غیرفعال
// ============================================

if (!function_exists('log_message')) {
    /**
     * لاگ کردن پیام با کنترل از طریق تنظیمات
     *
     * این تابع جایگزین error_log شده و ابتدا وضعیت لاگ را از
     * config('app.log_enabled') بررسی می‌کند. در صورت غیرفعال بودن،
     * هیچ کاری انجام نمی‌دهد.
     *
     * @param string $message پیام لاگ
     * @param int $message_type نوع پیام (0 = سیستم, 1 = ایمیل, 3 = فایل)
     * @param string|null $destination مقصد (برای نوع 3 مسیر فایل)
     * @param string|null $extra_headers هدرهای اضافی (برای نوع 1)
     * @return bool true در صورت موفقیت یا غیرفعال بودن لاگ
     */
    function log_message($message, $message_type = 0, $destination = null, $extra_headers = null)
    {
        // خواندن وضعیت لاگ از کانفیگ (پیش‌فرض true)
        $logEnabled = config('app.log_enabled', true);

        // اگر لاگ غیرفعال است، بدون انجام کاری برگرد
        if (!$logEnabled) {
            return true;
        }

        // در غیر این صورت به error_log اصلی ارسال کن
        return error_log($message, $message_type, $destination, $extra_headers);
    }
}
