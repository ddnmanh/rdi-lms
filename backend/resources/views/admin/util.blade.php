
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
                <button type="button" onclick="closeDeleteModalGeneric_Global()"
                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    Hủy
                </button>
                <button type="button" id="confirmDeleteBtn"
                    class="px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
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
        OTHER: 'OTHER',
    }

    /**
     * Hiển thị modal xác nhận xóa cho một đối tượng (người dùng, vai trò, khóa học, bài học hoặc đối tượng khác).
     *
     * Các tham số truyền vào cho phép tuỳ chỉnh đối tượng, id, tên, mô tả, callback xóa và callback sau khi thành công/thất bại.
     *
     * Khi người dùng xác nhận xóa, sẽ thực thi `deleteFuncCallback`, nếu thành công sẽ đóng modal và hiển thị thông báo thành công,
     * ngược lại sẽ hiển thị thông báo lỗi.
     *
     * @param {Object} options
     * @param {string} options.objectName - Loại đối tượng (USER, ROLE, COURSE, LESSON, OTHER)
     * @param {number|null} options.idDelete - ID đối tượng sẽ xóa
     * @param {string} options.nameValue - Tên đối tượng
     * @param {string} options.descValue - Mô tả bổ sung cho đối tượng (email, level, v.v...)
     * @param {Function} options.deleteFuncCallback - Hàm callback xử lý xóa, trả về true/false
     * @param {Function} options.successFuncCallback - Callback khi xóa thành công
     * @param {Function} options.failFuncCallback - Callback khi xóa thất bại
     * @param {string|null} options.title - Tiêu đề modal tuỳ chỉnh
     * @param {string|null} options.message - Nội dung cảnh báo tuỳ chỉnh
     */
    function openLogoutModalGeneric_Global({ 
        title = null,
        message = null,
        deleteFuncCallback = async () => { return false; },
        successFuncCallback = async () => { return false; },
        failFuncCallback = async () => { return false; },
    } = {}) { 

        document.getElementById('deleteModalTitle').textContent = title == null ? `Đăng xuất` : title;
        document.getElementById('deleteModalMessage').textContent = message == null ? `Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?` : message;

        document.getElementById('bulkDeleteInfo').style.display = 'none';
        document.getElementById('singleDeleteInfo').style.display = 'none'; 

        document.getElementById('DELETE_MODAL').style.display = 'block';
        
        document.getElementById('confirmDeleteBtn').getElementsByTagName('span')[0].textContent = 'Xác nhận đăng xuất';

        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            try {
                const result = await deleteFuncCallback();
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
     * Khi người dùng xác nhận xóa, sẽ thực thi `deleteFuncCallback`, nếu thành công sẽ đóng modal và hiển thị thông báo thành công,
     * ngược lại sẽ hiển thị thông báo lỗi.
     *
     * @param {Object} options
     * @param {string} options.objectName - Loại đối tượng (USER, ROLE, COURSE, LESSON, OTHER)
     * @param {number|null} options.idDelete - ID đối tượng sẽ xóa
     * @param {string} options.nameValue - Tên đối tượng
     * @param {string} options.descValue - Mô tả bổ sung cho đối tượng (email, level, v.v...)
     * @param {Function} options.deleteFuncCallback - Hàm callback xử lý xóa, trả về true/false
     * @param {Function} options.successFuncCallback - Callback khi xóa thành công
     * @param {Function} options.failFuncCallback - Callback khi xóa thất bại
     * @param {string|null} options.title - Tiêu đề modal tuỳ chỉnh
     * @param {string|null} options.message - Nội dung cảnh báo tuỳ chỉnh
     */
    function openSingleDeleteModalGeneric_Global({
        objectName = OBJECTNAMEMODAL.OTHER,
        idDelete = null,
        nameValue = '-',
        descValue = '-',
        deleteFuncCallback = async () => { return false; },
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

        document.getElementById('deleteModalTitle').textContent = title == null ? `Xác nhận xóa ${objectNameLabel}` : title;
        document.getElementById('deleteModalMessage').textContent = message == null ? `Bạn có chắc chắn muốn xóa ${objectNameLabel} này không?` : message;

        document.getElementById('bulkDeleteInfo').style.display = 'none';

        document.getElementById('singleDeleteInfo').style.display = 'block';
        if (objectName === OBJECTNAMEMODAL.USER) {
            document.getElementById('deleteNameLabel').textContent = 'Tên:';
            document.getElementById('deleteDescLabel').textContent = 'Email:';
        } else if (objectName === OBJECTNAMEMODAL.ROLE) {
            document.getElementById('deleteNameLabel').textContent = 'Tên:';
            document.getElementById('deleteDescLabel').textContent = 'Level:';
        } else if (objectName === OBJECTNAMEMODAL.COURSE) {
            document.getElementById('deleteNameLabel').textContent = 'Id:';
            document.getElementById('deleteDescLabel').textContent = 'Tên:';
        } else if (objectName === OBJECTNAMEMODAL.LESSON) {
            document.getElementById('deleteNameLabel').textContent = 'Tên:';
            document.getElementById('deleteDescLabel').textContent = 'Khóa học:';
        } else if (objectName === OBJECTNAMEMODAL.OTHER) {
            document.getElementById('deleteNameLabel').textContent = 'Tên:';
            document.getElementById('deleteDescLabel').textContent = 'Mô tả:';
        }
        document.getElementById('deleteNameValue').textContent = nameValue || '-';
        document.getElementById('deleteDescValue').textContent = descValue || '-';

        document.getElementById('DELETE_MODAL').style.display = 'block';
        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            try {
                const result = await deleteFuncCallback();
                if (result) {
                    closeDeleteModalGeneric_Global();
                    showNotificationModel_Global(`Xóa thành công ${objectNameLabel} ${nameValue}`, 'success', successFuncCallback);
                } else {
                    closeDeleteModalGeneric_Global();
                    showNotificationModel_Global(`Có lỗi khi xóa ${objectNameLabel} ${nameValue}`, 'error', failFuncCallback);
                }
            } catch (error) {
                closeDeleteModalGeneric_Global();
                showNotificationModel_Global(error.message, 'error', failFuncCallback);
            }
        });
        document.body.style.overflow = 'hidden';
    }

    // function openLogoutModel_Global({ 
    //     deleteFuncCallback = async () => { return false; },
    //     successFuncCallback = async () => { return false; },
    //     failFuncCallback = async () => { return false; }
    // } = {}) {
    //     document.getElementById('deleteModalTitle').textContent = 'Bạn có chắc chắn muốn đăng xuất?';
    //     document.getElementById('deleteModalMessage').textContent = 'Bạn sẽ cần đăng nhập lại để truy cập vào hệ thống quản trị. hãy chắc chắn rằng bạn đã lưu lại tất cả công việc của mình trước khi đăng xuất.';
    //     document.getElementById('bulkDeleteInfo').style.display = 'none';
    //     // document.getElementById('deleteNameValue').textContent = nameValue || '-';
    //     // document.getElementById('deleteDescValue').textContent = descValue || '-';
    //     document.getElementById('DELETE_MODAL').style.display = 'block';

    //     document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    //         try {
    //             const result = await deleteFuncCallback();
    //             if (result) {
    //                 closeDeleteModalGeneric_Global();
    //                 // showNotificationModel_Global(`Xóa thành công ${objectNameLabel} ${nameValue}`, 'success', successFuncCallback);
    //             } else {
    //                 closeDeleteModalGeneric_Global();
    //                 // showNotificationModel_Global(`Có lỗi khi xóa ${objectNameLabel} ${nameValue}`, 'error', failFuncCallback);
    //             }
    //         } catch (error) {
    //             closeDeleteModalGeneric_Global();
    //             // showNotificationModel_Global(error.message, 'error', failFuncCallback);
    //         }
    //     });
    //     document.body.style.overflow = 'hidden';
    // }


    /**
     * Hiển thị modal xác nhận xóa (hàng loạt) cho các đối tượng (người dùng, vai trò, khóa học, bài học...)
     *
     * @param {Object} params - Các tham số cho xóa bulk.
     * @param {Array} params.arrayIds - Mảng chứa ID các đối tượng sẽ xóa.
     * @param {string} params.objectName - Loại đối tượng xóa (OBJECTNAMEMODAL.USER, .ROLE, .COURSE, .LESSON, .OTHER,...).
     * @param {Function} params.deleteFuncCallback - Hàm bất đồng bộ sẽ thực thi để thực hiện xóa, trả về true nếu thành công.
     * @param {Function} params.successFuncCallback - Hàm callback gọi sau khi xóa thành công.
     * @param {Function} params.failFuncCallback - Hàm callback gọi sau khi xóa thất bại hoặc lỗi.
     * @param {string|null} params.title - Tiêu đề modal, nếu null sẽ tự động sinh phù hợp.
     * @param {string|null} params.message - Nội dung thông điệp modal, nếu null sẽ tự động sinh phù hợp.
     */
    function openBulkDeleteModalGeneric_Global({
        arrayIds = [],
        objectName = OBJECTNAMEMODAL.OTHER,
        deleteFuncCallback = async () => { return false; },
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
        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            try {
                const result = await deleteFuncCallback();
                if (result) {
                    closeDeleteModalGeneric_Global();
                    showNotificationModel_Global(`Xóa thành công ${arrayIds.length} ${objectNameLabel}`, 'success', successFuncCallback);
                } else {
                    closeDeleteModalGeneric_Global();
                    showNotificationModel_Global(`Đã xảy ra lỗi khi xóa ${arrayIds.length} ${objectNameLabel}`, 'error', failFuncCallback);
                }
            } catch (error) {
                closeDeleteModalGeneric_Global();
                showNotificationModel_Global(error.message, 'error', failFuncCallback);
            }
        });
        document.body.style.overflow = 'hidden';
    }

    // Close model
    function closeDeleteModalGeneric_Global() {
        document.getElementById('DELETE_MODAL').style.display = 'none';
        document.body.style.overflow = '';
    }


    // Close modals on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const deleteModal = document.getElementById('DELETE_MODAL');
            const alertModal = document.getElementById('NOTIFICATION_MODAL');

            if (deleteModal && deleteModal.style.display !== 'none') {
                closeDeleteModalGeneric_Global();
            }
            if (alertModal && alertModal.style.display !== 'none') {
                closeNotificationModal_Global();
            }
        }
    });


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
