{{--
    Delete Confirmation Modal Component

    Cách sử dụng:
    1. Include component trong layout (chỉ cần 1 lần):
        @include('components.delete-modal')

    2. Gọi trong JavaScript:
        // Xóa một đối tượng
        DeleteModal.openSingle({
            objectName: OBJECT_NAME.USER,  // USER, ROLE, COURSE, LESSON, VIDEO, OTHER
            idDelete: 123,
            nameValue: 'Nguyễn Văn A',
            descValue: 'email@example.com',
            actionFuncCallback: async () => {
                // Gọi API xóa
                return true; // hoặc false nếu thất bại
            },
            successFuncCallback: () => {
                // Sau khi xóa thành công
            },
            failFuncCallback: () => {
                // Sau khi xóa thất bại
            }
        });

        // Xóa nhiều đối tượng
        DeleteModal.openBulk({
            arrayIds: [1, 2, 3],
            objectName: OBJECT_NAME.USER,
            actionFuncCallback: async () => { return true; },
            successFuncCallback: () => {},
            failFuncCallback: () => {}
        });

        // Modal đăng xuất
        DeleteModal.openLogout({
            actionFuncCallback: async () => { return true; },
            successFuncCallback: () => {},
            failFuncCallback: () => {}
        });

        // Đóng modal
        DeleteModal.close();

    3. Các OBJECT_NAME có sẵn:
        - OBJECT_NAME.USER
        - OBJECT_NAME.ROLE
        - OBJECT_NAME.COURSE
        - OBJECT_NAME.LESSON
        - OBJECT_NAME.VIDEO
        - OBJECT_NAME.OTHER
--}}

{{-- Delete Confirmation Modal --}}
<div id="DELETE_MODAL" class="fixed inset-0 z-[200] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="DeleteModal.close()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-md p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="deleteModalIcon" class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                        <svg class="w-6 h-6" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 0c14.7 0 28.2 8.1 35.2 21l216 400c6.7 12.4 6.4 27.4-.8 39.5S486.1 480 472 480L40 480c-14.1 0-27.2-7.4-34.4-19.5s-7.5-27.1-.8-39.5l216-400c7-12.9 20.5-21 35.2-21zm0 352a32 32 0 1 0 0 64 32 32 0 1 0 0-64zm0-192c-18.2 0-32.7 15.5-31.4 33.7l7.4 104c.9 12.5 11.4 22.3 23.9 22.3 12.6 0 23-9.7 23.9-22.3l7.4-104c1.3-18.2-13.1-33.7-31.4-33.7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="deleteModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">-</h3>
                        <p id="deleteModalSubtitle" class="text-sm text-gray-500 dark:text-gray-400">
                            Hành động này không thể hoàn tác
                        </p>
                    </div>
                </div>
                <button type="button" onclick="DeleteModal.close()"
                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div>
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
                <div id="bulkDeleteInfo" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4" style="display: none;"></div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" id="DELETE_MODAL_CANCEL_BTN" onclick="DeleteModal.close()"
                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    Hủy
                </button>
                <button type="button" id="DELETE_MODAL_CONFIRM_BTN"
                    class="px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2">
                    <span id="DELETE_MODAL_CONFIRM_BTN_ICON">
                        <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M136.7 5.9L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-8.7-26.1C306.9-7.2 294.7-16 280.9-16L167.1-16c-13.8 0-26 8.8-30.4 21.9zM416 144L32 144 53.1 467.1C54.7 492.4 75.7 512 101 512L347 512c25.3 0 46.3-19.6 47.9-44.9L416 144z"/>
                        </svg>
                    </span>
                    <span class="LOADING_IN_BTN hidden" id="DELETE_MODAL_CONFIRM_BTN_LOADING"></span>
                    <span id="DELETE_MODAL_CONFIRM_BTN_TEXT">Xác nhận xóa</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Chỉ include script 1 lần --}}
@once
    @push('scripts')
        <script>
            /**
             * Object Name Constants
             */
            const OBJECT_NAME = Object.freeze({
                USER: 'USER',
                ROLE: 'ROLE',
                COURSE: 'COURSE',
                LESSON: 'LESSON',
                VIDEO: 'VIDEO',
                OTHER: 'OTHER'
            });

            // Backward compatibility
            const OBJECTNAMEMODAL = OBJECT_NAME;

            /**
             * Delete Modal Component Manager
             */
            const DeleteModal = (function() {
                const OBJECT_LABELS = {
                    [OBJECT_NAME.USER]: 'người dùng',
                    [OBJECT_NAME.ROLE]: 'vai trò',
                    [OBJECT_NAME.COURSE]: 'khóa học',
                    [OBJECT_NAME.LESSON]: 'bài học',
                    [OBJECT_NAME.VIDEO]: 'video',
                    [OBJECT_NAME.OTHER]: 'đối tượng'
                };

                const FIELD_LABELS = {
                    [OBJECT_NAME.USER]: { name: 'Tên:', desc: 'Email:' },
                    [OBJECT_NAME.ROLE]: { name: 'Tên:', desc: 'Level:' },
                    [OBJECT_NAME.COURSE]: { name: 'Id:', desc: 'Tên:' },
                    [OBJECT_NAME.LESSON]: { name: 'Tên:', desc: 'Khóa học:' },
                    [OBJECT_NAME.VIDEO]: { name: 'Tên:', desc: 'Thông tin:' },
                    [OBJECT_NAME.OTHER]: { name: 'Tên:', desc: 'Mô tả:' }
                };

                function getElements() {
                    return {
                        modal: document.getElementById('DELETE_MODAL'),
                        title: document.getElementById('deleteModalTitle'),
                        subtitle: document.getElementById('deleteModalSubtitle'),
                        message: document.getElementById('deleteModalMessage'),
                        singleInfo: document.getElementById('singleDeleteInfo'),
                        bulkInfo: document.getElementById('bulkDeleteInfo'),
                        nameLabel: document.getElementById('deleteNameLabel'),
                        nameValue: document.getElementById('deleteNameValue'),
                        descLabel: document.getElementById('deleteDescLabel'),
                        descValue: document.getElementById('deleteDescValue'),
                        confirmBtn: document.getElementById('DELETE_MODAL_CONFIRM_BTN'),
                        cancelBtn: document.getElementById('DELETE_MODAL_CANCEL_BTN'),
                        confirmIcon: document.getElementById('DELETE_MODAL_CONFIRM_BTN_ICON'),
                        confirmLoading: document.getElementById('DELETE_MODAL_CONFIRM_BTN_LOADING'),
                        confirmText: document.getElementById('DELETE_MODAL_CONFIRM_BTN_TEXT')
                    };
                }

                function resetButtons(els) {
                    if (els.confirmBtn) els.confirmBtn.disabled = false;
                    if (els.cancelBtn) {
                        els.cancelBtn.disabled = false;
                        els.cancelBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
                    }
                    if (els.confirmIcon) els.confirmIcon.style.display = '';
                    if (els.confirmLoading) {
                        els.confirmLoading.classList.add('hidden');
                        els.confirmLoading.classList.remove('inline-block');
                    }
                }

                function showLoading(els) {
                    els.confirmBtn.disabled = true;
                    els.cancelBtn.disabled = true;
                    els.cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    els.confirmIcon.style.display = 'none';
                    els.confirmLoading.classList.remove('hidden');
                    els.confirmLoading.classList.add('inline-block');
                }

                /**
                 * Open single delete modal
                 */
                function openSingle({
                    objectName = OBJECT_NAME.OTHER,
                    idDelete = null,
                    nameValue = '-',
                    descValue = '-',
                    actionFuncCallback = async () => false,
                    successFuncCallback = async () => {},
                    failFuncCallback = async () => {},
                    title = null,
                    message = null,
                    confirmText = null,
                    cancelText = null
                } = {}) {
                    const els = getElements();
                    const objectLabel = OBJECT_LABELS[objectName] || OBJECT_LABELS[OBJECT_NAME.OTHER];
                    const labels = FIELD_LABELS[objectName] || FIELD_LABELS[OBJECT_NAME.OTHER];

                    // Set content
                    els.title.textContent = title ?? `Xác nhận xóa ${objectLabel}`;
                    els.subtitle.textContent = 'Hành động này không thể hoàn tác';
                    els.message.textContent = message ?? `Bạn có chắc chắn muốn xóa ${objectLabel} này không?`;

                    // Toggle info sections
                    els.bulkInfo.style.display = 'none';
                    els.singleInfo.style.display = 'block';

                    // Set field values
                    els.nameLabel.textContent = labels.name;
                    els.descLabel.textContent = labels.desc;
                    els.nameValue.textContent = nameValue || '-';
                    els.descValue.textContent = descValue || '-';

                    // Set button text
                    els.confirmText.textContent = confirmText ?? 'Xác nhận xóa';
                    if (cancelText) els.cancelBtn.textContent = cancelText;

                    // Reset buttons
                    resetButtons(els);

                    // Clone button to remove old listeners
                    const newConfirmBtn = els.confirmBtn.cloneNode(true);
                    els.confirmBtn.parentNode.replaceChild(newConfirmBtn, els.confirmBtn);

                    // Add click handler
                    newConfirmBtn.addEventListener('click', async () => {
                        const currentEls = getElements();
                        showLoading(currentEls);

                        try {
                            const result = await actionFuncCallback();
                            close();
                            result ? successFuncCallback() : failFuncCallback();
                        } catch (error) {
                            close();
                            failFuncCallback();
                        }
                    }, { once: true });

                    // Show modal
                    els.modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => newConfirmBtn.focus(), 100);
                }

                /**
                 * Open bulk delete modal
                 */
                function openBulk({
                    arrayIds = [],
                    objectName = OBJECT_NAME.OTHER,
                    actionFuncCallback = async () => false,
                    successFuncCallback = async () => {},
                    failFuncCallback = async () => {},
                    title = null,
                    message = null
                } = {}) {
                    const els = getElements();
                    const objectLabel = OBJECT_LABELS[objectName] || OBJECT_LABELS[OBJECT_NAME.OTHER];

                    // Set content
                    els.title.textContent = title ?? `Xác nhận xóa nhiều ${objectLabel}`;
                    els.subtitle.textContent = 'Hành động này không thể hoàn tác';
                    els.message.textContent = message ?? `Bạn có chắc chắn muốn xóa các ${objectLabel} này không?`;

                    // Toggle info sections
                    els.singleInfo.style.display = 'none';
                    els.bulkInfo.style.display = 'block';
                    els.bulkInfo.innerHTML = `
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Tất cả dữ liệu liên quan đến <span class="font-semibold text-red-600 dark:text-red-400">${arrayIds.length}</span> ${objectLabel} đã chọn sẽ bị xóa vĩnh viễn và không thể khôi phục.
                        </p>
                    `;

                    // Set button text
                    els.confirmText.textContent = 'Xác nhận xóa';

                    // Reset buttons
                    resetButtons(els);

                    // Clone button
                    const newConfirmBtn = els.confirmBtn.cloneNode(true);
                    els.confirmBtn.parentNode.replaceChild(newConfirmBtn, els.confirmBtn);

                    // Add click handler
                    newConfirmBtn.addEventListener('click', async () => {
                        const currentEls = getElements();
                        showLoading(currentEls);

                        try {
                            const result = await actionFuncCallback();
                            close();
                            result ? successFuncCallback() : failFuncCallback();
                        } catch (error) {
                            close();
                            failFuncCallback();
                        }
                    }, { once: true });

                    // Show modal
                    els.modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => newConfirmBtn.focus(), 100);
                }

                /**
                 * Open logout modal
                 */
                function openLogout({
                    title = null,
                    message = null,
                    actionFuncCallback = async () => false,
                    successFuncCallback = async () => {},
                    failFuncCallback = async () => {}
                } = {}) {
                    const els = getElements();

                    // Set content
                    els.title.textContent = title ?? 'Đăng xuất';
                    els.subtitle.textContent = '';
                    els.message.textContent = message ?? 'Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?';

                    // Hide info sections
                    els.bulkInfo.style.display = 'none';
                    els.singleInfo.style.display = 'none';

                    // Set button text
                    els.confirmText.textContent = 'Xác nhận đăng xuất';

                    // Reset buttons
                    resetButtons(els);

                    // Clone button
                    const newConfirmBtn = els.confirmBtn.cloneNode(true);
                    els.confirmBtn.parentNode.replaceChild(newConfirmBtn, els.confirmBtn);

                    // Add click handler
                    newConfirmBtn.addEventListener('click', async () => {
                        const currentEls = getElements();
                        showLoading(currentEls);

                        try {
                            const result = await actionFuncCallback();
                            close();
                            if (result) {
                                NotificationModal.show('Đăng xuất thành công', 'success', successFuncCallback);
                            } else {
                                NotificationModal.show('Có lỗi khi đăng xuất', 'error', failFuncCallback);
                            }
                        } catch (error) {
                            close();
                            NotificationModal.show(error.message || 'Có lỗi xảy ra', 'error', failFuncCallback);
                        }
                    }, { once: true });

                    // Show modal
                    els.modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => newConfirmBtn.focus(), 100);
                }

                /**
                 * Close modal
                 */
                function close() {
                    const els = getElements();
                    if (!els.modal) return;

                    els.modal.style.display = 'none';
                    document.body.style.overflow = '';
                    resetButtons(els);
                }

                // Keyboard handler
                document.addEventListener('keydown', (e) => {
                    const modal = document.getElementById('DELETE_MODAL');
                    if (modal && modal.style.display !== 'none' && e.key === 'Escape') {
                        close();
                    }
                });

                // Public API
                return {
                    openSingle,
                    openBulk,
                    openLogout,
                    close
                };
            })();
        </script>
    @endpush
@endonce
