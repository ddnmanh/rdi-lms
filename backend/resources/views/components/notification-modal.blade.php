{{--
    Notification Modal Component

    Cách sử dụng:
    1. Include component trong layout (chỉ cần 1 lần):
        @include('components.notification-modal')

    2. Gọi trong JavaScript:
        // Hiển thị thông báo
        NotificationModal.show('Thao tác thành công!', 'success');
        NotificationModal.show('Có lỗi xảy ra!', 'error');
        NotificationModal.show('Cảnh báo!', 'warning');
        NotificationModal.show('Thông tin!', 'info');

        // Với callback khi đóng
        NotificationModal.show('Tạo thành công!', 'success', () => {
            window.location.href = '/admin/users';
        });

        // Đóng modal
        NotificationModal.close();

    3. Các type hỗ trợ:
        - success: Thành công (màu xanh lá)
        - error: Lỗi (màu đỏ)
        - warning: Cảnh báo (màu vàng)
        - info: Thông tin (màu xanh dương)
        - default: Mặc định (màu xám)
--}}

{{-- Notification Modal --}}
<div id="NOTIFICATION_MODAL" class="fixed inset-0 z-[200] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="NotificationModal.close()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-md p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="notificationIcon" class="flex h-12 w-12 items-center justify-center rounded-xl"></div>
                    <div>
                        <h3 id="notificationTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Thông báo
                        </h3>
                    </div>
                </div>
                <button type="button" onclick="NotificationModal.close()"
                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div>
                <p id="notificationMessage" class="text-gray-700 dark:text-gray-300"></p>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" id="notificationCloseBtn" onclick="NotificationModal.close()"
                    class="px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2">
                    <span>Đóng</span>
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
             * Notification Modal Component Manager
             */
            const NotificationModal = (function() {
                let currentCallback = null;

                const TYPES = {
                    success: {
                        title: 'Thành công',
                        iconClass: 'flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400',
                        icon: `<svg class="w-6 h-6" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 512a256 256 0 1 1 0-512 256 256 0 1 1 0 512zM374 145.7c-10.7-7.8-25.7-5.4-33.5 5.3L221.1 315.2 169 263.1c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l72 72c5 5 11.8 7.5 18.8 7s13.4-4.1 17.5-9.8L379.3 179.2c7.8-10.7 5.4-25.7-5.3-33.5z"/>
                        </svg>`,
                        buttonClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all duration-300 flex items-center gap-2'
                    },
                    error: {
                        title: 'Lỗi',
                        iconClass: 'flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                        icon: `<svg class="w-6 h-6" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 512a256 256 0 1 1 0-512 256 256 0 1 1 0 512zm0-192a32 32 0 1 0 0 64 32 32 0 1 0 0-64zm0-192c-18.2 0-32.7 15.5-31.4 33.7l7.4 104c.9 12.6 11.4 22.3 23.9 22.3 12.6 0 23-9.7 23.9-22.3l7.4-104c1.3-18.2-13.1-33.7-31.4-33.7z"/>
                        </svg>`,
                        buttonClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2'
                    },
                    warning: {
                        title: 'Cảnh báo',
                        iconClass: 'flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400',
                        icon: `<svg class="w-6 h-6" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 0c14.7 0 28.2 8.1 35.2 21l216 400c6.7 12.4 6.4 27.4-.8 39.5S486.1 480 472 480L40 480c-14.1 0-27.2-7.4-34.4-19.5s-7.5-27.1-.8-39.5l216-400c7-12.9 20.5-21 35.2-21zm0 352a32 32 0 1 0 0 64 32 32 0 1 0 0-64zm0-192c-18.2 0-32.7 15.5-31.4 33.7l7.4 104c.9 12.5 11.4 22.3 23.9 22.3 12.6 0 23-9.7 23.9-22.3l7.4-104c1.3-18.2-13.1-33.7-31.4-33.7z"/>
                        </svg>`,
                        buttonClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition-all duration-300 flex items-center gap-2'
                    },
                    info: {
                        title: 'Thông tin',
                        iconClass: 'flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
                        icon: `<svg class="w-6 h-6" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM224 160a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm-8 64l48 0c13.3 0 24 10.7 24 24l0 88 8 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-80 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l24 0 0-64-24 0c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                        </svg>`,
                        buttonClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2'
                    },
                    default: {
                        title: 'Thông báo',
                        iconClass: 'flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                        icon: `<svg class="w-6 h-6" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M224 0c-17.7 0-32 14.3-32 32l0 3.2C119 50 64 114.6 64 192l0 21.7c0 48.1-16.4 94.8-46.4 132.4L7.8 358.3C2.7 364.6 0 372.4 0 380.5 0 400.1 15.9 416 35.5 416l376.9 0c19.6 0 35.5-15.9 35.5-35.5 0-8.1-2.7-15.9-7.8-22.2l-9.8-12.2C400.4 308.5 384 261.8 384 213.7l0-21.7c0-77.4-55-142-128-156.8l0-3.2c0-17.7-14.3-32-32-32zM162 464c7.1 27.6 32.2 48 62 48s54.9-20.4 62-48l-124 0z"/>
                        </svg>`,
                        buttonClass: 'px-4 py-2.5 text-sm font-semibold text-white bg-gray-600 rounded-xl hover:bg-gray-700 transition-all duration-300 flex items-center gap-2'
                    }
                };

                /**
                 * Show notification modal
                 * @param {string} message - Message to display
                 * @param {string} type - Type: 'success', 'error', 'warning', 'info', 'default'
                 * @param {Function} callback - Callback function when modal is closed
                 */
                function show(message, type = 'success', callback = null) {
                    const modal = document.getElementById('NOTIFICATION_MODAL');
                    const iconEl = document.getElementById('notificationIcon');
                    const titleEl = document.getElementById('notificationTitle');
                    const messageEl = document.getElementById('notificationMessage');
                    const closeBtn = document.getElementById('notificationCloseBtn');

                    if (!modal) {
                        console.error('NotificationModal: Modal element not found');
                        return;
                    }

                    // Get type config
                    const config = TYPES[type] || TYPES.default;

                    // Set content
                    messageEl.textContent = message;
                    titleEl.textContent = config.title;
                    iconEl.className = config.iconClass;
                    iconEl.innerHTML = config.icon;
                    closeBtn.className = config.buttonClass;

                    // Store callback
                    currentCallback = callback;

                    // Show modal
                    modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';

                    // Focus close button
                    setTimeout(() => closeBtn.focus(), 100);
                }

                /**
                 * Close notification modal
                 */
                function close() {
                    const modal = document.getElementById('NOTIFICATION_MODAL');
                    if (!modal) return;

                    modal.style.display = 'none';
                    document.body.style.overflow = '';

                    // Execute callback if exists
                    if (currentCallback && typeof currentCallback === 'function') {
                        const callback = currentCallback;
                        currentCallback = null;
                        callback();
                    }
                }

                /**
                 * Shorthand methods
                 */
                function success(message, callback) {
                    show(message, 'success', callback);
                }

                function error(message, callback) {
                    show(message, 'error', callback);
                }

                function warning(message, callback) {
                    show(message, 'warning', callback);
                }

                function info(message, callback) {
                    show(message, 'info', callback);
                }

                // Keyboard handler
                document.addEventListener('keydown', (e) => {
                    const modal = document.getElementById('NOTIFICATION_MODAL');
                    if (modal && modal.style.display !== 'none' && e.key === 'Escape') {
                        close();
                    }
                });

                // Public API
                return {
                    show,
                    close,
                    success,
                    error,
                    warning,
                    info
                };
            })();

        </script>
    @endpush
@endonce
