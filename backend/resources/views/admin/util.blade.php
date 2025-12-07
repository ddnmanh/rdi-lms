
{{-- Alert Notification Modal --}}
<div id="NOTIFICATION_MODAL" class="fixed inset-0 z-[200] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeNotificationModal_Global()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-xl p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="alertIcon" class="flex h-12 w-12 items-center justify-center rounded-xl">
                        <i id="alertIconClass" class="text-xl"></i>
                    </div>
                    <div>
                        <h3 id="alertTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Thông báo
                        </h3>
                    </div>
                </div>
                {{-- <button type="button" onclick="closeNotificationModal_Global()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button> --}}
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div class="">
                <p id="alertMessage" class="text-gray-700 dark:text-gray-300">
                </p>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeNotificationModal_Global()"
                    id="NOTIFICATION_MODAL_CLOSE_BTN"
                    class="px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2">
                    <span>Đóng</span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    /**
     * functionCallback: Hàm sẽ gọi lại khi nhấn vào nút đóng
     * **/
    function showNotificationModel_Global(message, type = 'success', functionCallback = () => {}) {

        const alertModal = document.getElementById('NOTIFICATION_MODAL');
        const alertIcon = document.getElementById('alertIcon');
        const alertIconClass = document.getElementById('alertIconClass');
        const alertTitle = document.getElementById('alertTitle');
        const alertMessage = document.getElementById('alertMessage');
        const alertButton = document.getElementById('NOTIFICATION_MODAL_CLOSE_BTN');

        // Set message
        alertMessage.textContent = message;

        // Set type-specific styles
        switch (type) {
            case 'success':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20';
                alertIconClass.className = 'fas fa-check-circle text-xl text-green-600 dark:text-green-400';
                alertTitle.textContent = 'Thành công';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'error':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/20';
                alertIconClass.className = 'fas fa-exclamation-circle text-xl text-red-600 dark:text-red-400';
                alertTitle.textContent = 'Lỗi';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'warning':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/20';
                alertIconClass.className = 'fas fa-exclamation-triangle text-xl text-amber-600 dark:text-amber-400';
                alertTitle.textContent = 'Cảnh báo';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'info':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/20';
                alertIconClass.className = 'fas fa-info-circle text-xl text-blue-600 dark:text-blue-400';
                alertTitle.textContent = 'Thông tin';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2';
                break;
            default:
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700';
                alertIconClass.className = 'fas fa-bell text-xl text-gray-600 dark:text-gray-400';
                alertTitle.textContent = 'Thông báo';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-gray-600 rounded-xl hover:bg-gray-700 transition-all duration-300 flex items-center gap-2';
        }

        // Show modal
        alertModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        alertButton.addEventListener('click', async () => {
            closeNotificationModal_Global();
            functionCallback();
        })
    }

    function closeNotificationModal_Global() {
        const alertModal = document.getElementById('NOTIFICATION_MODAL');
        alertModal.style.display = 'none';
        document.body.style.overflow = '';
    }
</script>

{{-- Delete Confirmation Modal (Single & Bulk) --}}
<div id="DELETE_MODAL" class="fixed inset-0 z-[200] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModalGeneric_Global()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-xl p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/20">
                        <i class="fas fa-exclamation-triangle text-xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 id="deleteModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">-</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Hành động này không thể hoàn tác
                        </p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div class="">
                <p id="deleteModalMessage" class="text-gray-700 dark:text-gray-300 mb-4">-</p>
                {{-- Single delete info --}}
                <div id="singleDeleteInfo" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-20" id="deleteNameLabel"></span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="deleteNameValue"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-20" id="deleteDescLabel"></span>
                        <span class="text-sm text-gray-900 dark:text-gray-100 break-all" id="deleteDescValue"></span>
                    </div>
                </div>
                {{-- Bulk delete info --}}
                <div id="bulkDeleteInfo" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4" style="display: none;">

                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" id="DELETE_MODAL_CANCEL_BTN" onclick="closeDeleteModalGeneric_Global()"
                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    Hủy
                </button>
                <button type="button" id="DELETE_MODAL_CONFIRM_BTN"
                    class="px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-trash" id="DELETE_MODAL_CONFIRM_BTN_TRASH_ICON"></i>
                    <span class="LOADING_IN_BTN hidden" id="DELETE_MODAL_CONFIRM_BTN_LOADING_ICON"></span>
                    <span>Xác nhận xóa</span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>

    const OBJECTNAMEMODAL = {
        USER: 'USER',
        ROLE: 'ROLE',
        COURSE: 'COURSE',
        LESSON: 'LESSON',
        VIDEO: 'VIDEO',
        OTHER: 'OTHER',
    }

    /**
     * Hiển thị modal xác nhận xóa cho một đối tượng (người dùng, vai trò, khóa học, bài học hoặc đối tượng khác).
     *
     * Các tham số truyền vào cho phép tuỳ chỉnh đối tượng, id, tên, mô tả, callback xóa và callback sau khi thành công/thất bại.
     *
     * Khi người dùng xác nhận xóa, sẽ thực thi `actionFuncCallback`, nếu thành công sẽ đóng modal và hiển thị thông báo thành công,
     * ngược lại sẽ hiển thị thông báo lỗi.
     *
     * @param {Object} options
     * @param {string} options.objectName - Loại đối tượng (USER, ROLE, COURSE, LESSON, OTHER)
     * @param {number|null} options.idDelete - ID đối tượng sẽ xóa
     * @param {string} options.nameValue - Tên đối tượng
     * @param {string} options.descValue - Mô tả bổ sung cho đối tượng (email, level, v.v...)
     * @param {Function} options.actionFuncCallback - Hàm callback xử lý xóa, trả về true/false
     * @param {Function} options.successFuncCallback - Callback khi xóa thành công
     * @param {Function} options.failFuncCallback - Callback khi xóa thất bại
     * @param {string|null} options.title - Tiêu đề modal tuỳ chỉnh
     * @param {string|null} options.message - Nội dung cảnh báo tuỳ chỉnh
     */
    function openLogoutModalGeneric_Global({
        title = null,
        message = null,
        actionFuncCallback = async () => { return false; },
        successFuncCallback = async () => { return false; },
        failFuncCallback = async () => { return false; },
    } = {}) {

        document.getElementById('deleteModalTitle').textContent = title == null ? `Đăng xuất` : title;
        document.getElementById('deleteModalMessage').textContent = message == null ? `Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?` : message;

        document.getElementById('bulkDeleteInfo').style.display = 'none';
        document.getElementById('singleDeleteInfo').style.display = 'none';

        document.getElementById('DELETE_MODAL').style.display = 'block';

        document.getElementById('DELETE_MODAL_CONFIRM_BTN').getElementsByTagName('span')[0].textContent = 'Xác nhận đăng xuất';

        document.getElementById('DELETE_MODAL_CONFIRM_BTN').addEventListener('click', async () => {
            try {
                const result = await actionFuncCallback();
                console.log(result);

                if (result) {
                    closeDeleteModalGeneric_Global();
                    showNotificationModel_Global(`Đăng xuất người dùng thành công`, 'success', successFuncCallback);
                } else {
                    closeDeleteModalGeneric_Global();
                    showNotificationModel_Global(`Có lỗi đăng xuất người dùng`, 'error', failFuncCallback);
                }
            } catch (error) {
                console.log(error);

                closeDeleteModalGeneric_Global();
                showNotificationModel_Global(error.message, 'error', failFuncCallback);
            }
        });
        document.body.style.overflow = 'hidden';
    }

    /**
     * Hiển thị modal xác nhận xóa cho một đối tượng (người dùng, vai trò, khóa học, bài học hoặc đối tượng khác).
     *
     * Các tham số truyền vào cho phép tuỳ chỉnh đối tượng, id, tên, mô tả, callback xóa và callback sau khi thành công/thất bại.
     *
     * Khi người dùng xác nhận xóa, sẽ thực thi `actionFuncCallback`, nếu thành công sẽ đóng modal và hiển thị thông báo thành công,
     * ngược lại sẽ hiển thị thông báo lỗi.
     *
     * @param {Object} options
     * @param {string} options.objectName - Loại đối tượng (USER, ROLE, COURSE, LESSON, VIDEO, OTHER)
     * @param {number|null} options.idDelete - ID đối tượng sẽ xóa
     * @param {string} options.nameValue - Tên đối tượng
     * @param {string} options.descValue - Mô tả bổ sung cho đối tượng (email, level, v.v...)
     * @param {Function} options.actionFuncCallback - Hàm callback xử lý xóa, trả về true/false
     * @param {Function} options.successFuncCallback - Callback khi xóa thành công
     * @param {Function} options.failFuncCallback - Callback khi xóa thất bại
     * @param {string|null} options.title - Tiêu đề modal tuỳ chỉnh
     * @param {string|null} options.message - Nội dung cảnh báo tuỳ chỉnh
     * @param {string|null} options.confirmText - Text button xác nhận tuỳ chỉnh
     * @param {string|null} options.cancelText - Text button hủy tuỳ chỉnh
     */
    function openSingleDeleteModalGeneric_Global({
        objectName = OBJECTNAMEMODAL.OTHER,
        idDelete = null,
        nameValue = '-',
        descValue = '-',
        actionFuncCallback = async () => { return false; },
        successFuncCallback = async () => { return false; },
        failFuncCallback = async () => { return false; },
        title = null,
        message = null,
        confirmText = null,
        cancelText = null
    } = {}) {

        // Mapping object name to label
        const objectNameLabels = {
            [OBJECTNAMEMODAL.USER]: 'người dùng',
            [OBJECTNAMEMODAL.ROLE]: 'vai trò',
            [OBJECTNAMEMODAL.COURSE]: 'khóa học',
            [OBJECTNAMEMODAL.LESSON]: 'bài học',
            [OBJECTNAMEMODAL.VIDEO]: 'video',
            [OBJECTNAMEMODAL.OTHER]: 'đối tượng'
        };
        const objectNameLabel = objectNameLabels[objectName] || 'đối tượng';

        // Mapping object name to field labels
        const fieldLabels = {
            [OBJECTNAMEMODAL.USER]: { name: 'Tên:', desc: 'Email:' },
            [OBJECTNAMEMODAL.ROLE]: { name: 'Tên:', desc: 'Level:' },
            [OBJECTNAMEMODAL.COURSE]: { name: 'Id:', desc: 'Tên:' },
            [OBJECTNAMEMODAL.LESSON]: { name: 'Tên:', desc: 'Khóa học:' },
            [OBJECTNAMEMODAL.VIDEO]: { name: 'Tên:', desc: 'Thông tin:' },
            [OBJECTNAMEMODAL.OTHER]: { name: 'Tên:', desc: 'Mô tả:' }
        };
        const labels = fieldLabels[objectName] || fieldLabels[OBJECTNAMEMODAL.OTHER];

        // Set modal content
        document.getElementById('deleteModalTitle').textContent = title ?? `Xác nhận xóa ${objectNameLabel}`;
        document.getElementById('deleteModalMessage').textContent = message ?? `Bạn có chắc chắn muốn xóa ${objectNameLabel} này không?`;

        // Toggle info sections
        document.getElementById('bulkDeleteInfo').style.display = 'none';
        document.getElementById('singleDeleteInfo').style.display = 'block';

        // Set field labels and values
        document.getElementById('deleteNameLabel').textContent = labels.name;
        document.getElementById('deleteDescLabel').textContent = labels.desc;
        document.getElementById('deleteNameValue').textContent = nameValue || '-';
        document.getElementById('deleteDescValue').textContent = descValue || '-';

        // Get buttons
        const confirmBtn = document.getElementById('DELETE_MODAL_CONFIRM_BTN');
        const cancelBtn = document.getElementById('DELETE_MODAL_CANCEL_BTN');
        const confirmIcon = document.getElementById('DELETE_MODAL_CONFIRM_BTN_TRASH_ICON');
        const confirmLoading = document.getElementById('DELETE_MODAL_CONFIRM_BTN_LOADING_ICON');

        // Set button text
        const confirmBtnSpan = confirmBtn.querySelector('span:last-child');
        if (confirmBtnSpan) {
            confirmBtnSpan.textContent = confirmText ?? 'Xác nhận xóa';
        }
        if (cancelText) {
            cancelBtn.textContent = cancelText;
        }

        // Reset button states
        confirmBtn.disabled = false;
        cancelBtn.disabled = false;
        cancelBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
        confirmIcon.style.display = '';
        confirmLoading.classList.add('hidden');
        confirmLoading.classList.remove('inline-block');

        // Clone button to remove all old event listeners (prevent memory leak)
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        // Add new event listener
        newConfirmBtn.addEventListener('click', async () => {
            // Disable buttons
            newConfirmBtn.disabled = true;
            cancelBtn.disabled = true;
            cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');

            // Show loading
            const newConfirmIcon = document.getElementById('DELETE_MODAL_CONFIRM_BTN_TRASH_ICON');
            const newConfirmLoading = document.getElementById('DELETE_MODAL_CONFIRM_BTN_LOADING_ICON');
            newConfirmIcon.style.display = 'none';
            newConfirmLoading.classList.remove('hidden');
            newConfirmLoading.classList.add('inline-block');

            try {
                const result = await actionFuncCallback();
                closeDeleteModalGeneric_Global();
                if (result) {
                    successFuncCallback();
                } else {
                    failFuncCallback();
                }
            } catch (error) {
                closeDeleteModalGeneric_Global();
                failFuncCallback();
            }
        }, { once: true }); // Use 'once' option to auto-remove listener after first click

        // Show modal and focus confirm button
        document.getElementById('DELETE_MODAL').style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Auto focus on confirm button for better UX
        setTimeout(() => newConfirmBtn.focus(), 100);
    }


    /**
     * Hiển thị modal xác nhận xóa (hàng loạt) cho các đối tượng (người dùng, vai trò, khóa học, bài học...)
     *
     * @param {Object} params - Các tham số cho xóa bulk.
     * @param {Array} params.arrayIds - Mảng chứa ID các đối tượng sẽ xóa.
     * @param {string} params.objectName - Loại đối tượng xóa (OBJECTNAMEMODAL.USER, .ROLE, .COURSE, .LESSON, .OTHER,...).
     * @param {Function} params.actionFuncCallback - Hàm bất đồng bộ sẽ thực thi để thực hiện xóa, trả về true nếu thành công.
     * @param {Function} params.successFuncCallback - Hàm callback gọi sau khi xóa thành công.
     * @param {Function} params.failFuncCallback - Hàm callback gọi sau khi xóa thất bại hoặc lỗi.
     * @param {string|null} params.title - Tiêu đề modal, nếu null sẽ tự động sinh phù hợp.
     * @param {string|null} params.message - Nội dung thông điệp modal, nếu null sẽ tự động sinh phù hợp.
     */
    function openBulkDeleteModalGeneric_Global({
        arrayIds = [],
        objectName = OBJECTNAMEMODAL.OTHER,
        actionFuncCallback = async () => { return false; },
        successFuncCallback = async () => { return false; },
        failFuncCallback = async () => { return false; },
        title = null,
        message = null
    } = {}) {

        let objectNameLabel = '';
        if (objectName === OBJECTNAMEMODAL.USER) {
            objectNameLabel = 'người dùng';
        } else if (objectName === OBJECTNAMEMODAL.ROLE) {
            objectNameLabel = 'vai trò';
        } else if (objectName === OBJECTNAMEMODAL.COURSE) {
            objectNameLabel = 'khóa học';
        } else if (objectName === OBJECTNAMEMODAL.LESSON) {
            objectNameLabel = 'bài học';
        } else {
            objectNameLabel = 'đối tượng';
        }

        document.getElementById('deleteModalTitle').textContent = title == null ? `Xác nhận xóa nhiều ${objectNameLabel}` : title;
        document.getElementById('deleteModalMessage').textContent = message == null ? `Bạn có chắc chắn muốn xóa ${objectNameLabel} này không?` : message;

        document.getElementById('singleDeleteInfo').style.display = 'none';
        document.getElementById('bulkDeleteInfo').style.display = 'block';
        document.getElementById('bulkDeleteInfo').innerHTML = `
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Tất cả dữ liệu liên quan đến <span id="bulkDeleteCount" class="font-semibold text-red-600 dark:text-red-400">${arrayIds.length}</span> ${objectNameLabel} đã chọn sẽ bị xóa vĩnh viễn và không thể khôi phục.
            </p>
        `;

        document.getElementById('DELETE_MODAL').style.display = 'block';
        document.getElementById('DELETE_MODAL_CONFIRM_BTN').addEventListener('click', async () => {

            // Disable nút xác nhận
            const confirmBtn = document.getElementById('DELETE_MODAL_CONFIRM_BTN');
            confirmBtn.disabled = true;

            document.getElementById('DELETE_MODAL_CONFIRM_BTN_TRASH_ICON').style.display = 'none';
            document.getElementById('DELETE_MODAL_CONFIRM_BTN_LOADING_ICON').classList.remove('hidden');
            document.getElementById('DELETE_MODAL_CONFIRM_BTN_LOADING_ICON').classList.add('inline-block');

            // Disable nút hủy
            const cancelBtn = document.getElementById('DELETE_MODAL_CANCEL_BTN');
            cancelBtn.disabled = true;
            cancelBtn.classList.add('hidden', 'cursor-not-allowed');

            try {
                const result = await actionFuncCallback();
                closeDeleteModalGeneric_Global();
                if (result) {
                    successFuncCallback();
                } else {
                    failFuncCallback();
                }
            } catch (error) {
                closeDeleteModalGeneric_Global();
                failFuncCallback();
            }
        });
        document.body.style.overflow = 'hidden';
    }

    // Close modal and reset states
    function closeDeleteModalGeneric_Global() {
        const modal = document.getElementById('DELETE_MODAL');
        const confirmBtn = document.getElementById('DELETE_MODAL_CONFIRM_BTN');
        const cancelBtn = document.getElementById('DELETE_MODAL_CANCEL_BTN');
        const confirmIcon = document.getElementById('DELETE_MODAL_CONFIRM_BTN_TRASH_ICON');
        const confirmLoading = document.getElementById('DELETE_MODAL_CONFIRM_BTN_LOADING_ICON');

        // Hide modal
        modal.style.display = 'none';
        document.body.style.overflow = '';

        // Reset button states
        if (confirmBtn) {
            confirmBtn.disabled = false;
        }
        if (cancelBtn) {
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
        }
        if (confirmIcon) {
            confirmIcon.style.display = '';
        }
        if (confirmLoading) {
            confirmLoading.classList.add('hidden');
            confirmLoading.classList.remove('inline-block');
        }
    }


    // Close modals on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const deleteModal = document.getElementById('DELETE_MODAL');
            const alertModal = document.getElementById('NOTIFICATION_MODAL');
            const blockModal = document.getElementById('BLOCK_MODAL');

            if (deleteModal && deleteModal.style.display !== 'none') {
                closeDeleteModalGeneric_Global();
            }
            if (alertModal && alertModal.style.display !== 'none') {
                closeNotificationModal_Global();
            }
            if (blockModal && blockModal.style.display !== 'none') {
                closeBlockModalGeneric_Global();
            }
        }
    });


</script>


{{-- ==================== BLOCK/UNBLOCK MODAL ==================== --}}
<div id="BLOCK_MODAL" class="fixed inset-0 z-[200] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeBlockModalGeneric_Global()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-xl p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="blockModalIconContainer" class="flex h-12 w-12 items-center justify-center rounded-xl">
                        <i id="blockModalIcon" class="text-xl"></i>
                    </div>
                    <div>
                        <h3 id="blockModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">-</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Hành động này sẽ thay đổi trạng thái của vai trò
                        </p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div class="">
                <p id="blockModalMessage" class="text-gray-700 dark:text-gray-300 mb-4">-</p>
                {{-- Single block info --}}
                <div id="singleBlockInfo" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-24" id="blockNameLabel">Tên:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100 break-words flex-1" id="blockNameValue"></span>
                    </div>
                    <div class="flex items-center gap-2" id="blockDescRow">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-24" id="blockDescLabel">Mô tả:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100 break-words flex-1" id="blockDescValue"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-24">Trạng thái:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="blockStatusValue"></span>
                    </div>
                </div>
                {{-- Bulk block info --}}
                <div id="bulkBlockInfo" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4" style="display: none;">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Tất cả <span id="bulkBlockCount" class="font-semibold"></span> <span id="bulkBlockObjectName"></span> đã chọn sẽ bị <span id="bulkBlockAction" class="font-semibold"></span>.
                    </p>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" id="BLOCK_MODAL_CANCEL_BTN" onclick="closeBlockModalGeneric_Global()"
                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    Hủy
                </button>
                <button type="button" id="BLOCK_MODAL_CONFIRM_BTN"
                    class="px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2">
                    <i id="BLOCK_MODAL_CONFIRM_BTN_ICON"></i>
                    <span class="LOADING_IN_BTN hidden" id="BLOCK_MODAL_CONFIRM_BTN_LOADING_ICON"></span>
                    <span id="BLOCK_MODAL_CONFIRM_BTN_TEXT">Xác nhận</span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    /**
     * Hiển thị modal xác nhận khóa/mở khóa cho một đối tượng
     *
     * @param {Object} options
     * @param {string} options.objectName - Loại đối tượng (ROLE, USER, PERMISSION, COURSE, LESSON, OTHER)
     * @param {number|null} options.idBlock - ID đối tượng
     * @param {string} options.nameValue - Tên đối tượng
     * @param {string} options.descValue - Mô tả bổ sung cho đối tượng
     * @param {boolean} options.isCurrentlyBlocked - Trạng thái hiện tại (true = đang bị khóa)
     * @param {Function} options.actionFuncCallback - Hàm callback xử lý khóa/mở khóa
     * @param {Function} options.successFuncCallback - Callback khi thành công
     * @param {Function} options.failFuncCallback - Callback khi thất bại
     * @param {string|null} options.title - Tiêu đề modal tùy chỉnh
     * @param {string|null} options.message - Nội dung cảnh báo tùy chỉnh
     */
    function openSingleBlockModalGeneric_Global({
        objectName = OBJECTNAMEMODAL.OTHER,
        idBlock = null,
        nameValue = '-',
        descValue = null,
        isCurrentlyBlocked = false,
        actionFuncCallback = async () => { return false; },
        successFuncCallback = async () => { return false; },
        failFuncCallback = async () => { return false; },
        title = null,
        message = null
    } = {}) {

        // Mapping object name to label
        const objectNameLabels = {
            [OBJECTNAMEMODAL.USER]: 'người dùng',
            [OBJECTNAMEMODAL.ROLE]: 'vai trò',
            [OBJECTNAMEMODAL.COURSE]: 'khóa học',
            [OBJECTNAMEMODAL.LESSON]: 'bài học',
            [OBJECTNAMEMODAL.VIDEO]: 'video',
            [OBJECTNAMEMODAL.OTHER]: 'đối tượng'
        };
        const objectNameLabel = objectNameLabels[objectName] || 'đối tượng';

        // Mapping object name to field labels
        const fieldLabels = {
            [OBJECTNAMEMODAL.USER]: { name: 'Tên:', desc: 'Email:' },
            [OBJECTNAMEMODAL.ROLE]: { name: 'Tên:', desc: 'Mô tả:' },
            [OBJECTNAMEMODAL.COURSE]: { name: 'Tiêu đề:', desc: 'Mô tả:' },
            [OBJECTNAMEMODAL.LESSON]: { name: 'Tiêu đề:', desc: 'Khóa học:' },
            [OBJECTNAMEMODAL.VIDEO]: { name: 'Tên:', desc: 'Thông tin:' },
            [OBJECTNAMEMODAL.OTHER]: { name: 'Tên:', desc: 'Mô tả:' }
        };
        const labels = fieldLabels[objectName] || fieldLabels[OBJECTNAMEMODAL.OTHER];

        // Xác định action (khóa hoặc mở khóa)
        const action = isCurrentlyBlocked ? 'mở khóa' : 'khóa';
        const actionTitle = isCurrentlyBlocked ? 'Mở khóa' : 'Khóa';
        const actionColor = isCurrentlyBlocked ? 'green' : 'orange';
        const actionIcon = isCurrentlyBlocked ? 'fa-lock-open' : 'fa-lock';
        const currentStatus = isCurrentlyBlocked ? 'Đã khóa' : 'Hoạt động';
        const newStatus = isCurrentlyBlocked ? 'Hoạt động' : 'Đã khóa';

        // Set modal icon and colors
        const iconContainer = document.getElementById('blockModalIconContainer');
        const icon = document.getElementById('blockModalIcon');
        if (isCurrentlyBlocked) {
            iconContainer.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20';
            icon.className = 'text-xl fas fa-lock-open text-green-600 dark:text-green-400';
        } else {
            iconContainer.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/20';
            icon.className = 'text-xl fas fa-lock text-orange-600 dark:text-orange-400';
        }

        // Set modal content
        document.getElementById('blockModalTitle').textContent = title ?? `${actionTitle} ${objectNameLabel}`;
        document.getElementById('blockModalMessage').textContent = message ?? `Bạn có chắc chắn muốn ${action} ${objectNameLabel} này không?`;

        // Toggle info sections
        document.getElementById('bulkBlockInfo').style.display = 'none';
        document.getElementById('singleBlockInfo').style.display = 'block';

        // Set field labels and values
        document.getElementById('blockNameLabel').textContent = labels.name;
        document.getElementById('blockNameValue').textContent = nameValue || '-';

        // Show/hide description row based on descValue
        const descRow = document.getElementById('blockDescRow');
        const descLabel = document.getElementById('blockDescLabel');
        const descValueElement = document.getElementById('blockDescValue');

        if (descValue !== null && descValue !== undefined) {
            descRow.style.display = 'flex';
            descLabel.textContent = labels.desc;
            descValueElement.textContent = descValue || '-';
        } else {
            descRow.style.display = 'none';
        }

        document.getElementById('blockStatusValue').innerHTML = `${currentStatus} → ${newStatus}`;

        // Get buttons
        const confirmBtn = document.getElementById('BLOCK_MODAL_CONFIRM_BTN');
        const cancelBtn = document.getElementById('BLOCK_MODAL_CANCEL_BTN');
        const confirmIcon = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_ICON');
        const confirmLoading = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_LOADING_ICON');
        const confirmText = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_TEXT');

        // Set button styles and text
        confirmBtn.className = `px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2 bg-${actionColor}-600 hover:bg-${actionColor}-700`;
        confirmIcon.className = `fas ${actionIcon}`;
        confirmText.textContent = actionTitle;

        // Reset button states
        confirmBtn.disabled = false;
        cancelBtn.disabled = false;
        cancelBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
        confirmIcon.style.display = '';
        confirmLoading.classList.add('hidden');
        confirmLoading.classList.remove('inline-block');

        // Clone button to remove all old event listeners
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        // Add new event listener
        newConfirmBtn.addEventListener('click', async () => {
            // Disable buttons
            newConfirmBtn.disabled = true;
            cancelBtn.disabled = true;
            cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');

            // Show loading
            const newConfirmIcon = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_ICON');
            const newConfirmLoading = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_LOADING_ICON');
            newConfirmIcon.style.display = 'none';
            newConfirmLoading.classList.remove('hidden');
            newConfirmLoading.classList.add('inline-block');

            try {
                const result = await actionFuncCallback();
                closeBlockModalGeneric_Global();
                if (result) {
                    successFuncCallback();
                } else {
                    failFuncCallback();
                }
            } catch (error) {
                closeBlockModalGeneric_Global();
                failFuncCallback();
            }
        }, { once: true });

        // Show modal
        document.getElementById('BLOCK_MODAL').style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Auto focus on confirm button
        setTimeout(() => newConfirmBtn.focus(), 100);
    }

    /**
     * Hiển thị modal xác nhận khóa/mở khóa hàng loạt
     *
     * @param {Object} params
     * @param {Array} params.arrayIds - Mảng ID các đối tượng
     * @param {string} params.objectName - Loại đối tượng (ROLE, USER, PERMISSION, COURSE, LESSON, OTHER)
     * @param {boolean} params.isBlock - true = khóa, false = mở khóa
     * @param {Function} params.actionFuncCallback - Hàm xử lý
     * @param {Function} params.successFuncCallback - Callback thành công
     * @param {Function} params.failFuncCallback - Callback thất bại
     * @param {string|null} params.title - Tiêu đề modal tùy chỉnh
     * @param {string|null} params.message - Nội dung cảnh báo tùy chỉnh
     */
    function openBulkBlockModalGeneric_Global({
        arrayIds = [],
        objectName = OBJECTNAMEMODAL.OTHER,
        isBlock = true,
        actionFuncCallback = async () => { return false; },
        successFuncCallback = async () => { return false; },
        failFuncCallback = async () => { return false; },
        title = null,
        message = null
    } = {}) {

        const objectNameLabels = {
            [OBJECTNAMEMODAL.USER]: 'người dùng',
            [OBJECTNAMEMODAL.ROLE]: 'vai trò',
            [OBJECTNAMEMODAL.COURSE]: 'khóa học',
            [OBJECTNAMEMODAL.LESSON]: 'bài học',
            [OBJECTNAMEMODAL.VIDEO]: 'video',
            [OBJECTNAMEMODAL.OTHER]: 'đối tượng'
        };
        const objectNameLabel = objectNameLabels[objectName] || 'đối tượng';

        const action = isBlock ? 'khóa' : 'mở khóa';
        const actionTitle = isBlock ? 'Khóa' : 'Mở khóa';
        const actionColor = isBlock ? 'orange' : 'green';
        const actionIcon = isBlock ? 'fa-lock' : 'fa-lock-open';

        // Set modal icon and colors
        const iconContainer = document.getElementById('blockModalIconContainer');
        const icon = document.getElementById('blockModalIcon');
        if (isBlock) {
            iconContainer.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/20';
            icon.className = 'text-xl fas fa-lock text-orange-600 dark:text-orange-400';
        } else {
            iconContainer.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20';
            icon.className = 'text-xl fas fa-lock-open text-green-600 dark:text-green-400';
        }

        document.getElementById('blockModalTitle').textContent = title ?? `${actionTitle} nhiều ${objectNameLabel}`;
        document.getElementById('blockModalMessage').textContent = message ?? `Bạn có chắc chắn muốn ${action} các ${objectNameLabel} đã chọn không?`;

        document.getElementById('singleBlockInfo').style.display = 'none';
        document.getElementById('bulkBlockInfo').style.display = 'block';
        document.getElementById('bulkBlockCount').textContent = arrayIds.length;
        document.getElementById('bulkBlockObjectName').textContent = objectNameLabel;
        document.getElementById('bulkBlockAction').textContent = action;

        // Get buttons
        const confirmBtn = document.getElementById('BLOCK_MODAL_CONFIRM_BTN');
        const cancelBtn = document.getElementById('BLOCK_MODAL_CANCEL_BTN');
        const confirmIcon = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_ICON');
        const confirmLoading = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_LOADING_ICON');
        const confirmText = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_TEXT');

        // Set button styles
        confirmBtn.className = `px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2 bg-${actionColor}-600 hover:bg-${actionColor}-700`;
        confirmIcon.className = `fas ${actionIcon}`;
        confirmText.textContent = actionTitle;

        // Reset button states
        confirmBtn.disabled = false;
        cancelBtn.disabled = false;
        cancelBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
        confirmIcon.style.display = '';
        confirmLoading.classList.add('hidden');

        // Clone button
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        newConfirmBtn.addEventListener('click', async () => {
            newConfirmBtn.disabled = true;
            const newCancelBtn = document.getElementById('BLOCK_MODAL_CANCEL_BTN');
            newCancelBtn.disabled = true;
            newCancelBtn.classList.add('opacity-50', 'cursor-not-allowed');

            const newConfirmIcon = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_ICON');
            const newConfirmLoading = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_LOADING_ICON');
            newConfirmIcon.style.display = 'none';
            newConfirmLoading.classList.remove('hidden');
            newConfirmLoading.classList.add('inline-block');

            try {
                const result = await actionFuncCallback();
                closeBlockModalGeneric_Global();
                if (result) {
                    successFuncCallback();
                } else {
                    failFuncCallback();
                }
            } catch (error) {
                closeBlockModalGeneric_Global();
                failFuncCallback();
            }
        }, { once: true });

        document.getElementById('BLOCK_MODAL').style.display = 'block';
        document.body.style.overflow = 'hidden';
        setTimeout(() => newConfirmBtn.focus(), 100);
    }

    // Close block modal
    function closeBlockModalGeneric_Global() {
        const modal = document.getElementById('BLOCK_MODAL');
        const confirmBtn = document.getElementById('BLOCK_MODAL_CONFIRM_BTN');
        const cancelBtn = document.getElementById('BLOCK_MODAL_CANCEL_BTN');
        const confirmIcon = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_ICON');
        const confirmLoading = document.getElementById('BLOCK_MODAL_CONFIRM_BTN_LOADING_ICON');

        modal.style.display = 'none';
        document.body.style.overflow = '';

        if (confirmBtn) confirmBtn.disabled = false;
        if (cancelBtn) {
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
        }
        if (confirmIcon) confirmIcon.style.display = '';
        if (confirmLoading) {
            confirmLoading.classList.add('hidden');
            confirmLoading.classList.remove('inline-block');
        }
    }
</script>


<script>
    // ===== Timezone Utilities =====
    // Tự động nhận biết timezone của client
    function getClientTimezone_Global() {
        try {
            return Intl.DateTimeFormat().resolvedOptions().timeZone;
        } catch (e) {
            // Fallback: tính toán offset
            const offset = -new Date().getTimezoneOffset();
            const hours = Math.floor(Math.abs(offset) / 60);
            const minutes = Math.abs(offset) % 60;
            const sign = offset >= 0 ? '+' : '-';
            return `UTC${sign}${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
        }
    }

    // Chuyển đổi UTC datetime từ Laravel sang local time format cho datetime-local input
    // Tự động sử dụng timezone của client
    function formatDateTimeLocal(utcDateTimeString) {
        if (!utcDateTimeString) return '';

        // Parse UTC datetime từ Laravel (ISO 8601 với Z hoặc +00:00)
        const date = new Date(utcDateTimeString);
        if (isNaN(date.getTime())) return '';

        // JavaScript Date tự động chuyển UTC sang local time của client
        // Format: YYYY-MM-DDTHH:mm (local time, không có timezone)
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    // Format date - hiển thị theo timezone của client
    // Laravel lưu và trả về datetime ở UTC, JavaScript tự động chuyển sang local time
    function formatDate_Global(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '-';

        // Laravel trả về datetime ở UTC (có timezone Z hoặc +00:00)
        // JavaScript Date tự động parse và chuyển đổi sang local time của client
        // Sử dụng local time methods để hiển thị đúng theo timezone của client
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${hours}:${minutes} ${day}/${month}/${year}`;
    }
</script>


<script>
    function removeVietnameseAccentsInString_Global(str) {
        str = str.toLowerCase();
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
        str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
        str = str.replace(/đ/g, "d");

        // Loại bỏ các ký tự đặc biệt khác nếu cần
        // str = str.replace(/[^0-9a-z ]/g, "");

        return str;
    }

    function escapeHtml_Global(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
</script>

<script>
    function formatSecondsToHHMMSS_Global(totalSeconds, displayUnit = false) {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        const paddedHours = String(hours).padStart(2, '0');
        const paddedMinutes = String(minutes).padStart(2, '0');
        const paddedSeconds = String(seconds).padStart(2, '0');

        if (displayUnit) {
            return `${paddedHours} giờ ${paddedMinutes} phút ${paddedSeconds} giây`;
        }

        return `${paddedHours}:${paddedMinutes}:${paddedSeconds}`;
    }
</script>

<script>
    function renderInputErrors_Global(errors = [], isClear = false) {

        if (isClear) {
            // Clear previous errors first
            document.querySelectorAll('label[name$="_LABEL"]').forEach(el => {
                el.classList.remove('text-red-500');
                el.classList.add('text-blue-700', 'dark:text-gray-300');
            });
            document.querySelectorAll('input.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
            });
            document.querySelectorAll('span[id$="_MSG"]').forEach(el => {
                el.classList.add('hidden', 'text-gray-500', 'dark:text-gray-400');
                el.classList.remove('text-red-500');
                el.textContent = '';
            });
        }

        for (const field in errors) {
            const labelElement = document.getElementsByName(field + '_LABEL')[0];
            const inputElement = document.getElementById(field);
            const msgElement = document.getElementById(field + '_MSG');

            if (labelElement) {
                labelElement.classList.remove('text-blue-700', 'dark:text-gray-300');
                labelElement.classList.add('text-red-500');
            }

            if (inputElement) {
                inputElement.classList.add('border-red-500');
            }

            if (msgElement) {
                msgElement.classList.remove('hidden', 'text-gray-500', 'dark:text-gray-400');
                msgElement.classList.add('text-red-500');
                msgElement.textContent = errors[field][0];
            }
        }
    }
</script>
