
function renderPaginationInfo_Global(htmlElementId = null, data = {}) {
    if (!htmlElementId) return '';
    const element = document.getElementById(htmlElementId);
    if (!element) return '';
    element.innerHTML = `
        Hiển thị <span class="font-semibold text-gray-900 dark:text-gray-100">${data?.from || 0}</span>
        – <span class="font-semibold text-gray-900 dark:text-gray-100">${data?.to || 0}</span>
        trong <span class="font-semibold text-gray-900 dark:text-gray-100">${data?.total || 0}</span>
    `;
}

function renderPaginationChangeItemPerPage_Global(htmlItemPerPageElementId = null, itemPerPage = 50, paginationData, functionCallback = () => {}) {
    if (!htmlItemPerPageElementId) return '';
    const element = document.getElementById(htmlItemPerPageElementId);
    if (!element) return '';

    let html = '<label for="itemPerPage" class="text-gray-600 dark:text-gray-400 whitespace-nowrap">Số mục mỗi trang</label>';
    html += `
        <select id="itemPerPage"
            class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="10" ${itemPerPage == 10 ? 'selected' : ''}>10</option>
            <option value="15" ${itemPerPage == 15 ? 'selected' : ''}>15</option>
            <option value="25" ${itemPerPage == 25 ? 'selected' : ''}>25</option>
            <option value="50" ${itemPerPage == 50 ? 'selected' : ''}>50</option>
            <option value="100" ${itemPerPage == 100 ? 'selected' : ''}>100</option>
            ${
                [10, 15, 25, 50, 100].includes(itemPerPage)
                ? ''
                : `<option value="${itemPerPage}" selected>${itemPerPage}</option>`
            }
        </select>
    `;

    element.innerHTML = html;

    const selectElement = element.querySelector('#itemPerPage');
    if (selectElement) {
        selectElement.addEventListener('change', function(e) {
            const numberItemPerPage = parseInt(e.target.value);
            functionCallback(1, numberItemPerPage);
        });
    }
}

function renderPaginationChangePage_Global(htmlChangePageElementId = null, htmlChangeItemPerPageElementId = null, paginationData, functionCallback = () => {}) {

    if (!htmlChangePageElementId) return '';
    const pagination = document.getElementById(htmlChangePageElementId);
    if (!pagination) return '';

    const selectElement = document.getElementById(htmlChangeItemPerPageElementId)?.getElementsByTagName('select')[0] || null;
    const itemPerPage = selectElement != null ? parseInt(selectElement.value || 50) : 50;

    const current = paginationData.current_page;
    const last = paginationData.last_page;

    if (last <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';

    // Previous Button
    if (current > 1) {
        html += `<button type="button" data-page="${current - 1}" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Trước</button>`;
    } else {
        html += `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">Trước</button>`;
    }

    // Page Numbers
    if (last <= 7) {
        // Show all pages if 7 or fewer
        for (let i = 1; i <= last; i++) {
            if (i === current) {
                html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
            } else {
                html += `<button type="button" data-page="${i}" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
            }
        }
    } else {
        // Complex pagination for many pages
        if (current <= 3) {
            // Show first 3, ellipsis, last
            for (let i = 1; i <= 3; i++) {
                if (i === current) {
                    html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                } else {
                    html += `<button type="button" data-page="${i}" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                }
            }
            html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
            html += `<button type="button" data-page="${last}" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
        } else if (current >= last - 2) {
            // Show first, ellipsis, last 3
            html += `<button type="button" data-page="1" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
            html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
            for (let i = last - 2; i <= last; i++) {
                if (i === current) {
                    html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                } else {
                    html += `<button type="button" data-page="${i}" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                }
            }
        } else {
            // Show first, ellipsis, current-1, current, current+1, ellipsis, last
            html += `<button type="button" data-page="1" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
            html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
            for (let i = current - 1; i <= current + 1; i++) {
                if (i === current) {
                    html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                } else {
                    html += `<button type="button" data-page="${i}" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                }
            }
            html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
            html += `<button type="button" data-page="${last}" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
        }
    }

    // Next Button
    if (current < last) {
        html += `<button type="button" data-page="${current + 1}" class="pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Sau</button>`;
    } else {
        html += `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">Sau</button>`;
    }

    pagination.innerHTML = html;

    // Attach event listeners to pagination buttons
    pagination.querySelectorAll('.pagination-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            functionCallback(page, itemPerPage);
        });
    });
}

// expose to Blade inline scripts
window.renderPaginationInfo_Global = renderPaginationInfo_Global;
window.renderPaginationChangeItemPerPage_Global = renderPaginationChangeItemPerPage_Global;
window.renderPaginationChangePage_Global = renderPaginationChangePage_Global;
