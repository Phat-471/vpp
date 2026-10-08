window.addEventListener('load', async () => {
    // The browser owns the print dialog; closing it does not prove the bill was printed.
    if (document.fonts?.ready) await document.fonts.ready;
    window.focus();
    window.print();
}, { once: true });
