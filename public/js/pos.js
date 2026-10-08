document.addEventListener('click', async (event) => {
    if (!event.target.closest('[data-pos-fullscreen]')) return;
    try {
        if (document.fullscreenElement) await document.exitFullscreen();
        else await document.documentElement.requestFullscreen();
    } catch {
        // The POS remains usable when the browser does not support fullscreen.
    }
});

// A same-origin frame avoids popup blockers. Only the cashier can confirm paper output.
window.addEventListener('pos-print-receipt', (event) => {
    const url = new URL(event.detail.url, window.location.origin);
    if (url.origin !== window.location.origin || !url.pathname.startsWith('/pos/hoa-don/')) return;
    document.getElementById('pos-receipt-frame')?.remove();
    const frame = document.createElement('iframe');
    frame.id = 'pos-receipt-frame';
    frame.className = 'pos-receipt-frame';
    frame.title = 'In hóa đơn bán hàng';
    frame.src = url.href;
    document.body.appendChild(frame);
});
