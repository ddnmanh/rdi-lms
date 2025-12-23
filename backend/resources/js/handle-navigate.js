function handleGotoBackPage_Global(targetPage = '/admin') {
    const currentRoute = window.location.pathname + (window.location.search || '');
    const prevPageIndex = currentRoute.indexOf('prev_page_url=');

    if (prevPageIndex !== -1) {
        const afterPrevPage = currentRoute.substring(prevPageIndex + 'prev_page_url='.length);
        targetPage = decodeURIComponent(afterPrevPage.split('&')[0]);
    }
    window.location.href = targetPage;
}

// expose to Blade inline scripts
window.handleGotoBackPage_Global = handleGotoBackPage_Global;
