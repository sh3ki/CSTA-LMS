{{-- Toast Notification Container --}}
{{-- Supports: session('success'), session('error'), session('warning'), session('info'), $errors --}}

<div id="toast-container" aria-live="polite" aria-atomic="false"
     style="position:fixed;top:76px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;max-width:420px;pointer-events:none;">

    @if(session('success'))
    <div class="csta-toast csta-toast-success" role="alert">
        <div class="csta-toast-icon"><span class="material-icons">check_circle</span></div>
        <div class="csta-toast-body">{{ session('success') }}</div>
        <button class="csta-toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
        <div class="csta-toast-progress"></div>
    </div>
    @endif

    @if(session('error'))
    <div class="csta-toast csta-toast-error" role="alert">
        <div class="csta-toast-icon"><span class="material-icons">error</span></div>
        <div class="csta-toast-body">{{ session('error') }}</div>
        <button class="csta-toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
        <div class="csta-toast-progress"></div>
    </div>
    @endif

    @if(session('warning'))
    <div class="csta-toast csta-toast-warning" role="alert">
        <div class="csta-toast-icon"><span class="material-icons">warning</span></div>
        <div class="csta-toast-body">{{ session('warning') }}</div>
        <button class="csta-toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
        <div class="csta-toast-progress"></div>
    </div>
    @endif

    @if(session('info'))
    <div class="csta-toast csta-toast-info" role="alert">
        <div class="csta-toast-icon"><span class="material-icons">info</span></div>
        <div class="csta-toast-body">{{ session('info') }}</div>
        <button class="csta-toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
        <div class="csta-toast-progress"></div>
    </div>
    @endif

    @if($errors->any())
    <div class="csta-toast csta-toast-error" role="alert">
        <div class="csta-toast-icon"><span class="material-icons">error_outline</span></div>
        <div class="csta-toast-body">
            @if($errors->count() === 1)
                {{ $errors->first() }}
            @else
                <strong>Please fix the following:</strong>
                <ul style="margin:6px 0 0;padding-left:16px;">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <button class="csta-toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
        <div class="csta-toast-progress"></div>
    </div>
    @endif
</div>

<style>
.csta-toast {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,.14), 0 1px 5px rgba(0,0,0,.1);
    padding: 14px 16px 18px;
    position: relative;
    overflow: hidden;
    pointer-events: all;
    min-width: 280px;
    max-width: 420px;
    animation: toastIn .3s cubic-bezier(.21,1.02,.73,1) forwards;
    border-left: 4px solid transparent;
}
.csta-toast.hiding {
    animation: toastOut .3s ease forwards;
}
@keyframes toastIn {
    from { opacity:0; transform:translateX(60px) scale(.95); }
    to   { opacity:1; transform:translateX(0)    scale(1); }
}
@keyframes toastOut {
    from { opacity:1; transform:translateX(0)    scale(1);   max-height:200px; margin-bottom:0; }
    to   { opacity:0; transform:translateX(60px) scale(.95); max-height:0;     margin-bottom:-10px; padding-top:0; padding-bottom:0; }
}
.csta-toast-success { border-left-color:#34a853; }
.csta-toast-error   { border-left-color:#ea4335; }
.csta-toast-warning { border-left-color:#f9ab00; }
.csta-toast-info    { border-left-color:#1a73e8; }

.csta-toast-icon { flex-shrink:0; display:flex; align-items:center; }
.csta-toast-icon .material-icons { font-size:20px; }
.csta-toast-success .csta-toast-icon .material-icons { color:#34a853; }
.csta-toast-error   .csta-toast-icon .material-icons { color:#ea4335; }
.csta-toast-warning .csta-toast-icon .material-icons { color:#f9ab00; }
.csta-toast-info    .csta-toast-icon .material-icons { color:#1a73e8; }

.csta-toast-body {
    flex:1;
    font-size:13.5px;
    color:#202124;
    line-height:1.5;
    word-break:break-word;
}
.csta-toast-close {
    flex-shrink:0;
    background:none;
    border:none;
    font-size:18px;
    color:#80868b;
    cursor:pointer;
    padding:0 0 0 8px;
    line-height:1;
    margin-top:-2px;
    transition:color .15s;
}
.csta-toast-close:hover { color:#202124; }

.csta-toast-progress {
    position:absolute;
    bottom:0; left:0;
    height:3px;
    border-radius:0 0 0 12px;
    animation: toastProgress var(--toast-duration, 4000ms) linear forwards;
}
.csta-toast-success .csta-toast-progress { background:#34a853; }
.csta-toast-error   .csta-toast-progress { background:#ea4335; }
.csta-toast-warning .csta-toast-progress { background:#f9ab00; }
.csta-toast-info    .csta-toast-progress { background:#1a73e8; }

@keyframes toastProgress {
    from { width:100%; }
    to   { width:0%; }
}
</style>

<script>
(function () {
    const DURATION = 4000;

    function dismissToast(el) {
        if (!el || el.classList.contains('hiding')) return;
        el.classList.add('hiding');
        el.addEventListener('animationend', () => el.remove(), { once: true });
    }

    function startToastTimer(el) {
        el.style.setProperty('--toast-duration', DURATION + 'ms');
        setTimeout(() => dismissToast(el), DURATION);
    }

    document.querySelectorAll('.csta-toast').forEach(startToastTimer);
})();

function dismissToast(el) {
    if (!el || el.classList.contains('hiding')) return;
    el.classList.add('hiding');
    el.addEventListener('animationend', () => el.remove(), { once: true });
}

// Global helper — call from anywhere to show a toast programmatically
function showToast(message, type = 'info') {
    const icons = { success:'check_circle', error:'error', warning:'warning', info:'info' };
    const toast = document.createElement('div');
    toast.className = `csta-toast csta-toast-${type}`;
    toast.setAttribute('role','alert');
    toast.innerHTML = `
        <div class="csta-toast-icon"><span class="material-icons">${icons[type] || 'info'}</span></div>
        <div class="csta-toast-body">${message}</div>
        <button class="csta-toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
        <div class="csta-toast-progress"></div>
    `;
    document.getElementById('toast-container').appendChild(toast);
    toast.style.setProperty('--toast-duration', '4000ms');
    setTimeout(() => dismissToast(toast), 4000);
}
</script>
