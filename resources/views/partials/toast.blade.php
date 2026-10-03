{{-- Système de notifications toast, réutilisable dans tous les layouts.
     Lit les messages flash de session (success/error/info/warning) et
     les affiche sous forme de cartes élégantes, avec fermeture auto. --}}
<style>
    #toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 380px;
    }

    .toast-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: white;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 10px 30px -6px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(15,23,42,0.04);
        border-left: 4px solid;
        opacity: 0;
        transform: translateX(30px);
        animation: toast-in 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .toast-item.toast-hide {
        animation: toast-out 0.3s ease forwards;
    }

    @keyframes toast-in {
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes toast-out {
        to { opacity: 0; transform: translateX(30px); }
    }

    .toast-item.success { border-left-color: #22C55E; }
    .toast-item.error { border-left-color: #EF4444; }
    .toast-item.info { border-left-color: #6366F1; }
    .toast-item.warning { border-left-color: #F59E0B; }

    .toast-icon {
        width: 32px; height: 32px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; flex-shrink: 0;
    }
    .toast-item.success .toast-icon { background: #DCFCE7; color: #16A34A; }
    .toast-item.error .toast-icon { background: #FEE2E2; color: #DC2626; }
    .toast-item.info .toast-icon { background: #E0E7FF; color: #4F46E5; }
    .toast-item.warning .toast-icon { background: #FEF3C7; color: #D97706; }

    .toast-message {
        font-size: 13.5px;
        font-weight: 500;
        color: #1E293B;
        line-height: 1.4;
        flex: 1;
        padding-top: 2px;
    }

    .toast-close {
        background: none; border: none; cursor: pointer;
        color: #94A3B8; font-size: 12px; padding: 4px; flex-shrink: 0;
        transition: color 0.15s ease;
    }
    .toast-close:hover { color: #475569; }
</style>

<div id="toast-container"></div>

<script>
    function showToast(message, type = 'info', duration = 4500) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const icons = {
            success: 'fa-circle-check',
            error: 'fa-circle-exclamation',
            info: 'fa-circle-info',
            warning: 'fa-triangle-exclamation'
        };

        const toast = document.createElement('div');
        toast.className = `toast-item ${type}`;
        toast.innerHTML = `
            <div class="toast-icon"><i class="fas ${icons[type] || icons.info}"></i></div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="dismissToast(this.parentElement)"><i class="fas fa-xmark"></i></button>
        `;
        container.appendChild(toast);

        setTimeout(() => dismissToast(toast), duration);
    }

    function dismissToast(toast) {
        if (!toast || toast.classList.contains('toast-hide')) return;
        toast.classList.add('toast-hide');
        setTimeout(() => toast.remove(), 300);
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            showToast(@json(session('success')), 'success');
            @if(str_contains(session('success'), 'obtenu'))
                if (typeof triggerConfetti === 'function') {
                    setTimeout(triggerConfetti, 200);
                }
            @endif
        @endif
        @if(session('error'))
            showToast(@json(session('error')), 'error');
        @endif
        @if(session('info'))
            showToast(@json(session('info')), 'info');
        @endif
        @if(session('warning'))
            showToast(@json(session('warning')), 'warning');
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast(@json($error), 'error');
            @endforeach
        @endif
    });
</script>