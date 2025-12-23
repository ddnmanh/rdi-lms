{{--
    Block/Unblock Confirmation Modal Component

    Cách sử dụng:
    1. Include component trong layout (chỉ cần 1 lần):
        @include('components.block-modal')

    2. Gọi trong JavaScript:
        // Khóa/Mở khóa một đối tượng
        BlockModal.openSingle({
            objectName: OBJECT_NAME.ROLE,  // USER, ROLE, COURSE, LESSON, VIDEO, OTHER
            idBlock: 123,
            nameValue: 'Admin',
            descValue: 'Quản trị viên',      // optional
            isCurrentlyBlocked: false,       // true = đang bị khóa, false = đang hoạt động
            actionFuncCallback: async () => {
                // Gọi API khóa/mở khóa
                return true;
            },
            successFuncCallback: () => {},
            failFuncCallback: () => {}
        });

        // Khóa/Mở khóa nhiều đối tượng
        BlockModal.openBulk({
            arrayIds: [1, 2, 3],
            objectName: OBJECT_NAME.ROLE,
            isBlock: true,  // true = khóa, false = mở khóa
            actionFuncCallback: async () => { return true; },
            successFuncCallback: () => {},
            failFuncCallback: () => {}
        });

        // Đóng modal
        BlockModal.close();

    3. Các OBJECT_NAME có sẵn:
        - OBJECT_NAME.USER
        - OBJECT_NAME.ROLE
        - OBJECT_NAME.COURSE
        - OBJECT_NAME.LESSON
        - OBJECT_NAME.VIDEO
        - OBJECT_NAME.OTHER
--}}

{{-- Block/Unblock Modal --}}
<div id="BLOCK_MODAL" class="fixed inset-0 z-[200] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="BlockModal.close()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-md p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="blockModalIconContainer" class="flex h-12 w-12 items-center justify-center rounded-xl"></div>
                    <div>
                        <h3 id="blockModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">-</h3>
                        <p id="blockModalSubtitle" class="text-sm text-gray-500 dark:text-gray-400">
                            Hành động này sẽ thay đổi trạng thái
                        </p>
                    </div>
                </div>
                <button type="button" onclick="BlockModal.close()"
                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div>
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
                <button type="button" id="BLOCK_MODAL_CANCEL_BTN" onclick="BlockModal.close()"
                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    Hủy
                </button>
                <button type="button" id="BLOCK_MODAL_CONFIRM_BTN"
                    class="px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2">
                    <span id="BLOCK_MODAL_CONFIRM_BTN_ICON"></span>
                    <span class="LOADING_IN_BTN hidden" id="BLOCK_MODAL_CONFIRM_BTN_LOADING"></span>
                    <span id="BLOCK_MODAL_CONFIRM_BTN_TEXT">Xác nhận</span>
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
             * Block Modal Component Manager
             */
            const BlockModal = (function() {
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
                    [OBJECT_NAME.ROLE]: { name: 'Tên:', desc: 'Mô tả:' },
                    [OBJECT_NAME.COURSE]: { name: 'Tiêu đề:', desc: 'Mô tả:' },
                    [OBJECT_NAME.LESSON]: { name: 'Tiêu đề:', desc: 'Khóa học:' },
                    [OBJECT_NAME.VIDEO]: { name: 'Tên:', desc: 'Thông tin:' },
                    [OBJECT_NAME.OTHER]: { name: 'Tên:', desc: 'Mô tả:' }
                };

                const ICONS = {
                    lock: `<svg class="w-5 h-5" viewBox="0 0 384 512" fill="currentColor">
                        <path d="M128 96l0 64 128 0 0-64c0-35.3-28.7-64-64-64s-64 28.7-64 64zM64 160l0-64C64 25.3 121.3-32 192-32S320 25.3 320 96l0 64c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 224c0-35.3 28.7-64 64-64z"/>
                    </svg>`,
                    unlock: `<svg class="w-5 h-5" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M384 96c0-35.3 28.7-64 64-64s64 28.7 64 64l0 32c0 17.7 14.3 32 32 32s32-14.3 32-32l0-32c0-70.7-57.3-128-128-128S320 25.3 320 96l0 64-160 0c-35.3 0-64 28.7-64 64l0 224c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-224c0-35.3-28.7-64-64-64l-32 0 0-64z"/>
                    </svg>`
                };

                function getElements() {
                    return {
                        modal: document.getElementById('BLOCK_MODAL'),
                        iconContainer: document.getElementById('blockModalIconContainer'),
                        title: document.getElementById('blockModalTitle'),
                        subtitle: document.getElementById('blockModalSubtitle'),
                        message: document.getElementById('blockModalMessage'),
                        singleInfo: document.getElementById('singleBlockInfo'),
                        bulkInfo: document.getElementById('bulkBlockInfo'),
                        nameLabel: document.getElementById('blockNameLabel'),
                        nameValue: document.getElementById('blockNameValue'),
                        descRow: document.getElementById('blockDescRow'),
                        descLabel: document.getElementById('blockDescLabel'),
                        descValue: document.getElementById('blockDescValue'),
                        statusValue: document.getElementById('blockStatusValue'),
                        bulkCount: document.getElementById('bulkBlockCount'),
                        bulkObjectName: document.getElementById('bulkBlockObjectName'),
                        bulkAction: document.getElementById('bulkBlockAction'),
                        confirmBtn: document.getElementById('BLOCK_MODAL_CONFIRM_BTN'),
                        cancelBtn: document.getElementById('BLOCK_MODAL_CANCEL_BTN'),
                        confirmIcon: document.getElementById('BLOCK_MODAL_CONFIRM_BTN_ICON'),
                        confirmLoading: document.getElementById('BLOCK_MODAL_CONFIRM_BTN_LOADING'),
                        confirmText: document.getElementById('BLOCK_MODAL_CONFIRM_BTN_TEXT')
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
                 * Open single block/unblock modal
                 */
                function openSingle({
                    objectName = OBJECT_NAME.OTHER,
                    idBlock = null,
                    nameValue = '-',
                    descValue = null,
                    isCurrentlyBlocked = false,
                    actionFuncCallback = async () => false,
                    successFuncCallback = async () => {},
                    failFuncCallback = async () => {},
                    title = null,
                    message = null
                } = {}) {
                    const els = getElements();
                    const objectLabel = OBJECT_LABELS[objectName] || OBJECT_LABELS[OBJECT_NAME.OTHER];
                    const labels = FIELD_LABELS[objectName] || FIELD_LABELS[OBJECT_NAME.OTHER];

                    // Action text
                    const action = isCurrentlyBlocked ? 'mở khóa' : 'khóa';
                    const actionTitle = isCurrentlyBlocked ? 'Mở khóa' : 'Khóa';
                    const currentStatus = isCurrentlyBlocked ? 'Đã khóa' : 'Hoạt động';
                    const newStatus = isCurrentlyBlocked ? 'Hoạt động' : 'Đã khóa';

                    // Set icon and colors
                    if (isCurrentlyBlocked) {
                        els.iconContainer.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400';
                        els.iconContainer.innerHTML = ICONS.unlock;
                        els.confirmBtn.className = 'px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2 bg-green-600 hover:bg-green-700';
                        els.confirmIcon.innerHTML = ICONS.lock;
                    } else {
                        els.iconContainer.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400';
                        els.iconContainer.innerHTML = ICONS.lock;
                        els.confirmBtn.className = 'px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2 bg-orange-600 hover:bg-orange-700';
                        els.confirmIcon.innerHTML = ICONS.unlock;
                    }

                    // Set content
                    els.title.textContent = title ?? `${actionTitle} ${objectLabel}`;
                    els.subtitle.textContent = 'Hành động này sẽ thay đổi trạng thái';
                    els.message.textContent = message ?? `Bạn có chắc chắn muốn ${action} ${objectLabel} này không?`;

                    // Toggle info sections
                    els.bulkInfo.style.display = 'none';
                    els.singleInfo.style.display = 'block';

                    // Set field values
                    els.nameLabel.textContent = labels.name;
                    els.nameValue.textContent = nameValue || '-';

                    // Show/hide description row
                    if (descValue !== null && descValue !== undefined) {
                        els.descRow.style.display = 'flex';
                        els.descLabel.textContent = labels.desc;
                        els.descValue.textContent = descValue || '-';
                    } else {
                        els.descRow.style.display = 'none';
                    }

                    els.statusValue.innerHTML = `${currentStatus} → ${newStatus}`;

                    // Set button text
                    els.confirmText.textContent = actionTitle;

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
                 * Open bulk block/unblock modal
                 */
                function openBulk({
                    arrayIds = [],
                    objectName = OBJECT_NAME.OTHER,
                    isBlock = true,
                    actionFuncCallback = async () => false,
                    successFuncCallback = async () => {},
                    failFuncCallback = async () => {},
                    title = null,
                    message = null
                } = {}) {
                    const els = getElements();
                    const objectLabel = OBJECT_LABELS[objectName] || OBJECT_LABELS[OBJECT_NAME.OTHER];

                    // Action text
                    const action = isBlock ? 'khóa' : 'mở khóa';
                    const actionTitle = isBlock ? 'Khóa' : 'Mở khóa';

                    // Set icon and colors
                    if (isBlock) {
                        els.iconContainer.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400';
                        els.iconContainer.innerHTML = ICONS.lock;
                        els.confirmBtn.className = 'px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2 bg-orange-600 hover:bg-orange-700';
                        els.confirmIcon.innerHTML = ICONS.lock;
                    } else {
                        els.iconContainer.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400';
                        els.iconContainer.innerHTML = ICONS.unlock;
                        els.confirmBtn.className = 'px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2 bg-green-600 hover:bg-green-700';
                        els.confirmIcon.innerHTML = ICONS.unlock;
                    }

                    // Set content
                    els.title.textContent = title ?? `${actionTitle} nhiều ${objectLabel}`;
                    els.subtitle.textContent = 'Hành động này sẽ thay đổi trạng thái';
                    els.message.textContent = message ?? `Bạn có chắc chắn muốn ${action} các ${objectLabel} đã chọn không?`;

                    // Toggle info sections
                    els.singleInfo.style.display = 'none';
                    els.bulkInfo.style.display = 'block';
                    els.bulkCount.textContent = arrayIds.length;
                    els.bulkObjectName.textContent = objectLabel;
                    els.bulkAction.textContent = action;

                    // Set button text
                    els.confirmText.textContent = actionTitle;

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
                    const modal = document.getElementById('BLOCK_MODAL');
                    if (modal && modal.style.display !== 'none' && e.key === 'Escape') {
                        close();
                    }
                });

                // Public API
                return {
                    openSingle,
                    openBulk,
                    close
                };
            })();

            // Backward compatibility functions
            // function BlockModal.openSingle(options) {
            //     BlockModal.openSingle(options);
            // }

            // function openBulkBlockModalGeneric_Global(options) {
            //     BlockModal.openBulk(options);
            // }

            // function closeBlockModalGeneric_Global() {
            //     BlockModal.close();
            // }
        </script>
    @endpush
@endonce
