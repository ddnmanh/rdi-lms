{{--
    Custom DateTime Picker Component - Tương thích mọi trình duyệt

    Cách sử dụng:
    1. Include component:
        @include('components.datetime-picker', [
            'id' => 'startTime',
            'placeholder' => 'Chọn ngày giờ...',
        ])

    2. Khởi tạo trong JavaScript:
        DateTimePicker.init('startTime', {
            minDateTime: '2024-01-01 00:00',    // Ngày giờ nhỏ nhất (optional)
            maxDateTime: '2024-12-31 23:59',    // Ngày giờ lớn nhất (optional)
            defaultValue: '2024-06-15 14:30',   // Giá trị mặc định (optional)
            format: 'DD/MM/YYYY HH:mm',         // Định dạng hiển thị (optional)
            minuteStep: 5,                       // Bước nhảy phút: 1, 5, 10, 15, 30 (optional, default: 1)
            onChange: (value) => {              // Callback khi thay đổi (optional)
                console.log('Selected:', value);
            }
        });

    3. Các API khác:
       - DateTimePicker.getValue('startTime')                    // Lấy giá trị (YYYY-MM-DD HH:mm)
       - DateTimePicker.setValue('startTime', '2024-06-15 14:30') // Set giá trị
       - DateTimePicker.setMinDateTime('startTime', '...')       // Set ngày giờ min
       - DateTimePicker.setMaxDateTime('startTime', '...')       // Set ngày giờ max
       - DateTimePicker.clear('startTime')                       // Xóa giá trị
       - DateTimePicker.getDate('startTime')                     // Lấy phần ngày (YYYY-MM-DD)
       - DateTimePicker.getTime('startTime')                     // Lấy phần giờ (HH:mm)
--}}

@php
    $placeholder = $placeholder ?? 'Chọn ngày giờ...';
@endphp

<div class="relative" data-datetimepicker-id="{{ $id }}">
    {{-- Hidden input để lưu giá trị thực (YYYY-MM-DD HH:mm) --}}
    <input type="hidden" id="{{ $id }}" name="{{ $id }}" />

    {{-- Display input --}}
    <div class="relative">
        <input type="text" id="{{ $id }}_display" placeholder="{{ $placeholder }}" readonly
            class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none cursor-pointer" />
        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-2">
            <button type="button" id="{{ $id }}_clear"
                class="hidden text-gray-400 hover:text-red-500 transition-colors p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <svg class="w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
    </div>

    {{-- DateTime Picker Dropdown --}}
    <div id="{{ $id }}_picker"
        class="hidden absolute z-50 mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl w-[280px]">

        {{-- Tabs: Date / Time --}}
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button type="button" id="{{ $id }}_tabDate" class="flex-1 flex flex-row items-center justify-center px-4 py-2.5 text-sm font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20 transition-colors">
                <span class="mr-2">
                    <svg class="w-4" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M120 0c13.3 0 24 10.7 24 24l0 40 160 0 0-40c0-13.3 10.7-24 24-24s24 10.7 24 24l0 40 32 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 128C0 92.7 28.7 64 64 64l32 0 0-40c0-13.3 10.7-24 24-24zm0 112l-56 0c-8.8 0-16 7.2-16 16l0 48 352 0 0-48c0-8.8-7.2-16-16-16l-264 0zM48 224l0 192c0 8.8 7.2 16 16 16l320 0c8.8 0 16-7.2 16-16l0-192-352 0z"/>
                    </svg>
                </span>
                Ngày
            </button>
            <button type="button" id="{{ $id }}_tabTime"
                class="flex-1 flex flex-row items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                <span class="mr-2">
                    <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M464 256a208 208 0 1 1 -416 0 208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0 256 256 0 1 0 -512 0zM232 120l0 136c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2 280 120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/>
                    </svg>
                </span>
                Giờ
            </button>
        </div>

        {{-- Date Panel --}}
        <div id="{{ $id }}_datePanel" class="p-2">
            {{-- Header: Month/Year Navigation --}}
            <div class="flex items-center justify-between mb-4">
                <button type="button" id="{{ $id }}_prevMonth"
                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <div class="flex items-center gap-2">
                    <select id="{{ $id }}_monthSelect"
                        class="px-2 py-1 text-sm font-medium bg-transparent border border-gray-200 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    </select>
                    <select id="{{ $id }}_yearSelect"
                        class="px-2 py-1 text-sm font-medium bg-transparent border border-gray-200 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    </select>
                </div>

                <button type="button" id="{{ $id }}_nextMonth"
                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            {{-- Weekday Headers --}}
            <div class="grid grid-cols-7 gap-1 mb-1">
                <div class="text-center text-sm font-medium text-red-500 py-0.5">CN</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-0.5">T2</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-0.5">T3</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-0.5">T4</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-0.5">T5</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-0.5">T6</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-0.5">T7</div>
            </div>

            {{-- Days Grid --}}
            <div id="{{ $id }}_daysGrid" class="grid grid-cols-7 gap-1">
                {{-- Days will be rendered here --}}
            </div>

            {{-- Today button --}}
            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-center">
                <button type="button" id="{{ $id }}_todayBtn"
                    class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                    Hôm nay
                </button>
            </div>
        </div>

        {{-- Time Panel --}}
        <div id="{{ $id }}_timePanel" class="hidden p-4">
            {{-- Time Display --}}
            <div class="text-center mb-6">
                <div class="text-4xl font-bold text-gray-800 dark:text-gray-100 tabular-nums">
                    <span id="{{ $id }}_hourDisplay">00</span>
                    <span class="text-gray-400 animate-pulse">:</span>
                    <span id="{{ $id }}_minuteDisplay">00</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" id="{{ $id }}_selectedDateDisplay">
                    Chưa chọn ngày
                </p>
            </div>

            {{-- Hour Picker --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Giờ</label>
                <div id="{{ $id }}_hoursGrid" class="grid grid-cols-6 gap-1 max-h-[120px] overflow-y-auto p-1 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                </div>
            </div>

            {{-- Minute Picker --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Phút</label>
                <div id="{{ $id }}_minutesGrid" class="grid grid-cols-6 gap-1 max-h-[120px] overflow-y-auto p-1 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                </div>
            </div>

            {{-- Now button --}}
            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-center">
                <button type="button" id="{{ $id }}_nowBtn"
                    class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                    Bây giờ
                </button>
            </div>
        </div>

        {{-- Footer: Selected DateTime & Confirm --}}
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-300">
                <span id="{{ $id }}_previewValue" class="font-medium">--/--/---- --:--</span>
            </div>
            <button type="button" id="{{ $id }}_confirmBtn"
                class="px-4 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                Xác nhận
            </button>
        </div>
    </div>
</div>

{{-- Chỉ include script 1 lần --}}
@once
    @push('scripts')
        <script>
            /**
             * Custom DateTimePicker Component Manager
             */
            const DateTimePicker = (function() {
                const instances = {};

                const MONTHS = [
                    'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                ];

                /**
                 * Initialize a datetime picker component
                 */
                function init(id, config = {}) {
                    const hiddenInput = document.getElementById(id);
                    const displayInput = document.getElementById(`${id}_display`);
                    const picker = document.getElementById(`${id}_picker`);
                    const clearBtn = document.getElementById(`${id}_clear`);

                    // Tabs
                    const tabDate = document.getElementById(`${id}_tabDate`);
                    const tabTime = document.getElementById(`${id}_tabTime`);
                    const datePanel = document.getElementById(`${id}_datePanel`);
                    const timePanel = document.getElementById(`${id}_timePanel`);

                    // Date elements
                    const daysGrid = document.getElementById(`${id}_daysGrid`);
                    const monthSelect = document.getElementById(`${id}_monthSelect`);
                    const yearSelect = document.getElementById(`${id}_yearSelect`);
                    const prevMonthBtn = document.getElementById(`${id}_prevMonth`);
                    const nextMonthBtn = document.getElementById(`${id}_nextMonth`);
                    const todayBtn = document.getElementById(`${id}_todayBtn`);

                    // Time elements
                    const hoursGrid = document.getElementById(`${id}_hoursGrid`);
                    const minutesGrid = document.getElementById(`${id}_minutesGrid`);
                    const hourDisplay = document.getElementById(`${id}_hourDisplay`);
                    const minuteDisplay = document.getElementById(`${id}_minuteDisplay`);
                    const selectedDateDisplay = document.getElementById(`${id}_selectedDateDisplay`);
                    const nowBtn = document.getElementById(`${id}_nowBtn`);

                    // Footer
                    const previewValue = document.getElementById(`${id}_previewValue`);
                    const confirmBtn = document.getElementById(`${id}_confirmBtn`);

                    if (!hiddenInput || !displayInput || !picker) {
                        console.error(`DateTimePicker: Elements not found for id "${id}"`);
                        return;
                    }

                    const instance = {
                        id,
                        hiddenInput,
                        displayInput,
                        picker,
                        clearBtn,
                        tabDate,
                        tabTime,
                        datePanel,
                        timePanel,
                        daysGrid,
                        monthSelect,
                        yearSelect,
                        hoursGrid,
                        minutesGrid,
                        hourDisplay,
                        minuteDisplay,
                        selectedDateDisplay,
                        previewValue,
                        confirmBtn,
                        isOpen: false,
                        activeTab: 'date',
                        currentMonth: new Date().getMonth(),
                        currentYear: new Date().getFullYear(),
                        // Temporary selection (before confirm)
                        tempDate: null,
                        tempHour: 0,
                        tempMinute: 0,
                        // Confirmed selection
                        selectedDateTime: null,
                        config: {
                            minDateTime: config.minDateTime ? parseDateTime(config.minDateTime) : null,
                            maxDateTime: config.maxDateTime ? parseDateTime(config.maxDateTime) : null,
                            format: config.format || 'DD/MM/YYYY HH:mm',
                            minuteStep: config.minuteStep || 1,
                            onChange: config.onChange || null
                        }
                    };

                    instances[id] = instance;

                    // Populate month select
                    MONTHS.forEach((month, index) => {
                        const option = document.createElement('option');
                        option.value = index;
                        option.textContent = month;
                        monthSelect.appendChild(option);
                    });

                    // Populate year select
                    populateYearSelect(instance);

                    // Điều chỉnh currentYear về năm hợp lệ nếu nằm ngoài khoảng min/max
                    adjustCurrentYearToValidRange(instance);

                    // Render time grids
                    renderHoursGrid(instance);
                    renderMinutesGrid(instance);

                    // Set default value
                    if (config.defaultValue) {
                        setValueInternal(instance, config.defaultValue, true);
                    }

                    // ==================== Event Listeners ====================

                    displayInput.addEventListener('click', () => togglePicker(id));

                    clearBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        clear(id);
                    });

                    // Tabs
                    tabDate.addEventListener('click', (e) => {
                        e.stopPropagation();
                        switchTab(instance, 'date');
                    });

                    tabTime.addEventListener('click', (e) => {
                        e.stopPropagation();
                        switchTab(instance, 'time');
                    });

                    // Date navigation
                    prevMonthBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        navigateMonth(instance, -1);
                    });

                    nextMonthBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        navigateMonth(instance, 1);
                    });

                    monthSelect.addEventListener('change', (e) => {
                        instance.currentMonth = parseInt(e.target.value);
                        renderCalendar(instance);
                    });

                    yearSelect.addEventListener('change', (e) => {
                        instance.currentYear = parseInt(e.target.value);
                        renderCalendar(instance);
                    });

                    todayBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const today = new Date();
                        if (isDateInRange(instance, today)) {
                            instance.tempDate = today;
                            instance.currentMonth = today.getMonth();
                            instance.currentYear = today.getFullYear();
                            renderCalendar(instance);
                            updatePreview(instance);
                            // Auto switch to time tab
                            switchTab(instance, 'time');
                        } else {
                            NotificationModal.show('Ngày hôm nay không nằm trong khoảng cho phép', 'warning');
                        }
                    });

                    nowBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const now = new Date();
                        if (isDateTimeInRange(instance, now)) {
                            instance.tempDate = now;
                            instance.tempHour = now.getHours();
                            instance.tempMinute = now.getMinutes();
                            instance.currentMonth = now.getMonth();
                            instance.currentYear = now.getFullYear();
                            renderCalendar(instance);
                            renderHoursGrid(instance);
                            renderMinutesGrid(instance);
                            updateTimeDisplay(instance);
                            updatePreview(instance);
                        } else {
                            NotificationModal.show('Thời điểm hiện tại không nằm trong khoảng cho phép', 'warning');
                        }
                    });

                    confirmBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        confirmSelection(instance);
                    });

                    // Close on outside click
                    document.addEventListener('click', (e) => {
                        if (!e.target.closest(`[data-datetimepicker-id="${id}"]`)) {
                            closePicker(id);
                        }
                    });

                    // Render initial calendar
                    renderCalendar(instance);
                    updatePreview(instance);

                    return instance;
                }

                /**
                 * Switch between date and time tabs
                 */
                function switchTab(instance, tab) {
                    const { tabDate, tabTime, datePanel, timePanel } = instance;
                    instance.activeTab = tab;

                    if (tab === 'date') {
                        tabDate.classList.add('text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
                        tabDate.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                        tabTime.classList.remove('text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
                        tabTime.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                        datePanel.classList.remove('hidden');
                        timePanel.classList.add('hidden');
                    } else {
                        tabTime.classList.add('text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
                        tabTime.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                        tabDate.classList.remove('text-blue-600', 'dark:text-blue-400', 'border-blue-600', 'dark:border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
                        tabDate.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
                        timePanel.classList.remove('hidden');
                        datePanel.classList.add('hidden');
                        updateTimeDisplay(instance);
                    }
                }

                /**
                 * Populate year select
                 */
                function populateYearSelect(instance) {
                    const { yearSelect, config } = instance;
                    yearSelect.innerHTML = '';

                    const currentYear = new Date().getFullYear();
                    let minYear = config.minDateTime ? config.minDateTime.getFullYear() : currentYear - 100;
                    let maxYear = config.maxDateTime ? config.maxDateTime.getFullYear() : currentYear + 10;

                    // Lưu lại min/max year vào instance để sử dụng sau
                    instance.minYear = minYear;
                    instance.maxYear = maxYear;

                    for (let year = maxYear; year >= minYear; year--) {
                        const option = document.createElement('option');
                        option.value = year;
                        option.textContent = year;
                        yearSelect.appendChild(option);
                    }
                }

                /**
                 * Điều chỉnh currentYear về năm hợp lệ nếu nằm ngoài khoảng min/max
                 */
                function adjustCurrentYearToValidRange(instance) {
                    const { minYear, maxYear } = instance;

                    if (minYear !== undefined && maxYear !== undefined) {
                        if (instance.currentYear > maxYear) {
                            instance.currentYear = maxYear;
                        } else if (instance.currentYear < minYear) {
                            instance.currentYear = minYear;
                        }
                    }
                }

                /**
                 * Navigate months
                 */
                function navigateMonth(instance, direction) {
                    instance.currentMonth += direction;

                    if (instance.currentMonth > 11) {
                        instance.currentMonth = 0;
                        instance.currentYear++;
                    } else if (instance.currentMonth < 0) {
                        instance.currentMonth = 11;
                        instance.currentYear--;
                    }

                    const { config } = instance;
                    if (config.minDateTime && instance.currentYear < config.minDateTime.getFullYear()) {
                        instance.currentYear = config.minDateTime.getFullYear();
                        instance.currentMonth = config.minDateTime.getMonth();
                    }
                    if (config.maxDateTime && instance.currentYear > config.maxDateTime.getFullYear()) {
                        instance.currentYear = config.maxDateTime.getFullYear();
                        instance.currentMonth = config.maxDateTime.getMonth();
                    }

                    renderCalendar(instance);
                }

                /**
                 * Render calendar
                 */
                function renderCalendar(instance) {
                    const { daysGrid, monthSelect, yearSelect, currentMonth, currentYear, tempDate } = instance;

                    monthSelect.value = currentMonth;
                    yearSelect.value = currentYear;

                    daysGrid.innerHTML = '';

                    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
                    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
                    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Previous month days
                    for (let i = firstDay - 1; i >= 0; i--) {
                        const day = daysInPrevMonth - i;
                        const date = new Date(currentYear, currentMonth - 1, day);
                        const dayEl = createDayElement(instance, date, true);
                        daysGrid.appendChild(dayEl);
                    }

                    // Current month days
                    for (let day = 1; day <= daysInMonth; day++) {
                        const date = new Date(currentYear, currentMonth, day);
                        const dayEl = createDayElement(instance, date, false);
                        daysGrid.appendChild(dayEl);
                    }

                    // Next month days
                    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
                    const remainingCells = totalCells - (firstDay + daysInMonth);
                    for (let day = 1; day <= remainingCells; day++) {
                        const date = new Date(currentYear, currentMonth + 1, day);
                        const dayEl = createDayElement(instance, date, true);
                        daysGrid.appendChild(dayEl);
                    }
                }

                /**
                 * Create day element
                 */
                function createDayElement(instance, date, isOtherMonth) {
                    const { tempDate } = instance;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    const isToday = date.toDateString() === today.toDateString();
                    const isSelected = tempDate && date.toDateString() === tempDate.toDateString();
                    const isDisabled = !isDateInRange(instance, date);
                    const isSunday = date.getDay() === 0;

                    const dayEl = document.createElement('button');
                    dayEl.type = 'button';
                    dayEl.textContent = date.getDate();

                    let classes = 'w-full aspect-square text-sm rounded-md transition-all duration-200 flex items-center justify-center ';

                    if (isDisabled) {
                        classes += 'text-gray-300 dark:text-gray-600 cursor-not-allowed ';
                    } else if (isSelected) {
                        classes += 'bg-blue-600 text-white font-semibold shadow-lg shadow-blue-500/30 ';
                    } else if (isToday) {
                        classes += 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold ';
                    } else if (isOtherMonth) {
                        classes += 'text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 ';
                    } else if (isSunday) {
                        classes += 'text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700 ';
                    } else {
                        classes += 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 ';
                    }

                    dayEl.className = classes;

                    if (!isDisabled) {
                        dayEl.addEventListener('click', (e) => {
                            e.stopPropagation();
                            instance.tempDate = date;
                            renderCalendar(instance);
                            updatePreview(instance);
                            // Auto switch to time tab after selecting date
                            switchTab(instance, 'time');
                        });
                    }

                    return dayEl;
                }

                /**
                 * Render hours grid
                 */
                function renderHoursGrid(instance) {
                    const { hoursGrid, tempHour } = instance;
                    hoursGrid.innerHTML = '';

                    for (let h = 0; h < 24; h++) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.textContent = String(h).padStart(2, '0');

                        const isSelected = h === tempHour;
                        let classes = 'py-1.5 text-sm rounded transition-colors ';

                        if (isSelected) {
                            classes += 'bg-blue-600 text-white font-semibold ';
                        } else {
                            classes += 'text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 ';
                        }

                        btn.className = classes;

                        btn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            instance.tempHour = h;
                            renderHoursGrid(instance);
                            updateTimeDisplay(instance);
                            updatePreview(instance);
                        });

                        hoursGrid.appendChild(btn);
                    }
                }

                /**
                 * Render minutes grid
                 */
                function renderMinutesGrid(instance) {
                    const { minutesGrid, tempMinute, config } = instance;
                    minutesGrid.innerHTML = '';

                    const step = config.minuteStep || 1;

                    for (let m = 0; m < 60; m += step) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.textContent = String(m).padStart(2, '0');

                        const isSelected = m === tempMinute;
                        let classes = 'py-1.5 text-sm rounded transition-colors ';

                        if (isSelected) {
                            classes += 'bg-blue-600 text-white font-semibold ';
                        } else {
                            classes += 'text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 ';
                        }

                        btn.className = classes;

                        btn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            instance.tempMinute = m;
                            renderMinutesGrid(instance);
                            updateTimeDisplay(instance);
                            updatePreview(instance);
                        });

                        minutesGrid.appendChild(btn);
                    }
                }

                /**
                 * Update time display in time panel
                 */
                function updateTimeDisplay(instance) {
                    const { hourDisplay, minuteDisplay, selectedDateDisplay, tempDate, tempHour, tempMinute, config } = instance;

                    hourDisplay.textContent = String(tempHour).padStart(2, '0');
                    minuteDisplay.textContent = String(tempMinute).padStart(2, '0');

                    if (tempDate) {
                        selectedDateDisplay.textContent = formatDateDisplay(tempDate, 'DD/MM/YYYY');
                    } else {
                        selectedDateDisplay.textContent = 'Chưa chọn ngày';
                    }
                }

                /**
                 * Update preview in footer
                 */
                function updatePreview(instance) {
                    const { previewValue, tempDate, tempHour, tempMinute, config } = instance;

                    if (tempDate) {
                        // Tạo datetime tạm để format theo config.format
                        const tempDateTime = new Date(tempDate);
                        tempDateTime.setHours(tempHour, tempMinute, 0, 0);
                        previewValue.textContent = formatDateTimeDisplay(tempDateTime, config.format);
                    } else {
                        // Tạo placeholder dựa trên format
                        const placeholder = config.format
                            .replace('DD', '--')
                            .replace('MM', '--')
                            .replace('YYYY', '----')
                            .replace('HH', '--')
                            .replace('mm', '--');
                        previewValue.textContent = placeholder;
                    }
                }

                /**
                 * Confirm selection
                 */
                function confirmSelection(instance) {
                    const { tempDate, tempHour, tempMinute } = instance;

                    if (!tempDate) {
                        NotificationModal.show('Vui lòng chọn ngày', 'warning');
                        switchTab(instance, 'date');
                        return;
                    }

                    // Create full datetime
                    const dateTime = new Date(tempDate);
                    dateTime.setHours(tempHour, tempMinute, 0, 0);

                    // Check if in range
                    if (!isDateTimeInRange(instance, dateTime)) {
                        NotificationModal.show('Thời gian đã chọn không nằm trong khoảng cho phép', 'warning');
                        return;
                    }

                    instance.selectedDateTime = dateTime;

                    // Update inputs
                    instance.hiddenInput.value = formatDateTimeISO(dateTime);
                    instance.displayInput.value = formatDateTimeDisplay(dateTime, instance.config.format);

                    // Show clear button
                    instance.clearBtn.classList.remove('hidden');

                    // Trigger onChange
                    if (instance.config.onChange) {
                        instance.config.onChange(instance.hiddenInput.value);
                    }

                    closePicker(instance.id);
                }

                /**
                 * Check if date is in range
                 */
                function isDateInRange(instance, date) {
                    const { config } = instance;
                    const checkDate = new Date(date);
                    checkDate.setHours(0, 0, 0, 0);

                    if (config.minDateTime) {
                        const min = new Date(config.minDateTime);
                        min.setHours(0, 0, 0, 0);
                        if (checkDate < min) return false;
                    }

                    if (config.maxDateTime) {
                        const max = new Date(config.maxDateTime);
                        max.setHours(23, 59, 59, 999);
                        if (checkDate > max) return false;
                    }

                    return true;
                }

                /**
                 * Check if datetime is in range
                 */
                function isDateTimeInRange(instance, dateTime) {
                    const { config } = instance;

                    if (config.minDateTime && dateTime < config.minDateTime) return false;
                    if (config.maxDateTime && dateTime > config.maxDateTime) return false;

                    return true;
                }

                /**
                 * Toggle picker
                 */
                function togglePicker(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    if (instance.isOpen) {
                        closePicker(id);
                    } else {
                        openPicker(id);
                    }
                }

                /**
                 * Open picker
                 */
                function openPicker(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    // Close other pickers
                    Object.keys(instances).forEach(key => {
                        if (key !== id) closePicker(key);
                    });

                    instance.isOpen = true;
                    instance.picker.classList.remove('hidden');

                    // Initialize temp values from selected or current
                    if (instance.selectedDateTime) {
                        instance.tempDate = new Date(instance.selectedDateTime);
                        instance.tempHour = instance.selectedDateTime.getHours();
                        instance.tempMinute = instance.selectedDateTime.getMinutes();
                        instance.currentMonth = instance.selectedDateTime.getMonth();
                        instance.currentYear = instance.selectedDateTime.getFullYear();
                    } else {
                        instance.tempDate = null;
                        instance.tempHour = 0;
                        instance.tempMinute = 0;
                    }

                    renderCalendar(instance);
                    renderHoursGrid(instance);
                    renderMinutesGrid(instance);
                    updateTimeDisplay(instance);
                    updatePreview(instance);
                    switchTab(instance, 'date');
                }

                /**
                 * Close picker
                 */
                function closePicker(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.isOpen = false;
                    instance.picker.classList.add('hidden');
                }

                /**
                 * Parse datetime string
                 */
                function parseDateTime(str) {
                    if (!str) return null;
                    if (str instanceof Date) return str;

                    // Handle ISO format
                    if (str.includes('T')) {
                        return new Date(str);
                    }

                    // Handle "YYYY-MM-DD HH:mm" format
                    const [datePart, timePart] = str.split(' ');
                    const [year, month, day] = datePart.split('-').map(Number);
                    let hour = 0, minute = 0;

                    if (timePart) {
                        [hour, minute] = timePart.split(':').map(Number);
                    }

                    return new Date(year, month - 1, day, hour, minute);
                }

                /**
                 * Format datetime to ISO string (YYYY-MM-DD HH:mm)
                 */
                function formatDateTimeISO(date) {
                    if (!date) return '';
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const hour = String(date.getHours()).padStart(2, '0');
                    const minute = String(date.getMinutes()).padStart(2, '0');
                    return `${year}-${month}-${day} ${hour}:${minute}`;
                }

                /**
                 * Format date for display
                 */
                function formatDateDisplay(date, format) {
                    if (!date) return '';
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();

                    return format
                        .replace('DD', day)
                        .replace('MM', month)
                        .replace('YYYY', year);
                }

                /**
                 * Format datetime for display
                 */
                function formatDateTimeDisplay(date, format) {
                    if (!date) return '';
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    const hour = String(date.getHours()).padStart(2, '0');
                    const minute = String(date.getMinutes()).padStart(2, '0');

                    return format
                        .replace('DD', day)
                        .replace('MM', month)
                        .replace('YYYY', year)
                        .replace('HH', hour)
                        .replace('mm', minute);
                }

                /**
                 * Set value internally
                 */
                function setValueInternal(instance, value, isInit = false) {
                    if (!value) {
                        instance.selectedDateTime = null;
                        instance.tempDate = null;
                        instance.tempHour = 0;
                        instance.tempMinute = 0;
                        instance.hiddenInput.value = '';
                        instance.displayInput.value = '';
                        instance.clearBtn.classList.add('hidden');
                        return;
                    }

                    const dateTime = parseDateTime(value);
                    if (dateTime && !isNaN(dateTime.getTime())) {
                        instance.selectedDateTime = dateTime;
                        instance.tempDate = new Date(dateTime);
                        instance.tempHour = dateTime.getHours();
                        instance.tempMinute = dateTime.getMinutes();
                        instance.currentMonth = dateTime.getMonth();
                        instance.currentYear = dateTime.getFullYear();
                        instance.hiddenInput.value = formatDateTimeISO(dateTime);
                        instance.displayInput.value = formatDateTimeDisplay(dateTime, instance.config.format);
                        instance.clearBtn.classList.remove('hidden');
                    }
                }

                // ==================== PUBLIC API ====================

                function getValue(id) {
                    const instance = instances[id];
                    return instance ? instance.hiddenInput.value : '';
                }

                function setValue(id, value) {
                    const instance = instances[id];
                    if (!instance) return;

                    setValueInternal(instance, value);

                    if (instance.config.onChange) {
                        instance.config.onChange(instance.hiddenInput.value);
                    }
                }

                function getDate(id) {
                    const instance = instances[id];
                    if (!instance || !instance.selectedDateTime) return '';
                    return formatDateTimeISO(instance.selectedDateTime).split(' ')[0];
                }

                function getTime(id) {
                    const instance = instances[id];
                    if (!instance || !instance.selectedDateTime) return '';
                    return formatDateTimeISO(instance.selectedDateTime).split(' ')[1];
                }

                function setMinDateTime(id, minDateTime) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.config.minDateTime = minDateTime ? parseDateTime(minDateTime) : null;
                    populateYearSelect(instance);
                    adjustCurrentYearToValidRange(instance);
                    renderCalendar(instance);
                }

                function setMaxDateTime(id, maxDateTime) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.config.maxDateTime = maxDateTime ? parseDateTime(maxDateTime) : null;
                    populateYearSelect(instance);
                    adjustCurrentYearToValidRange(instance);
                    renderCalendar(instance);
                }

                function setRange(id, minDateTime, maxDateTime) {
                    setMinDateTime(id, minDateTime);
                    setMaxDateTime(id, maxDateTime);
                }

                function clear(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    setValueInternal(instance, null);

                    if (instance.config.onChange) {
                        instance.config.onChange('');
                    }
                }

                function getInstance(id) {
                    return instances[id] || null;
                }

                // ==================== HELPERS ====================

                function now() {
                    return formatDateTimeISO(new Date());
                }

                function today() {
                    const d = new Date();
                    d.setHours(0, 0, 0, 0);
                    return formatDateTimeISO(d);
                }

                function addMinutes(dateTimeStr, minutes) {
                    const date = parseDateTime(dateTimeStr) || new Date();
                    date.setMinutes(date.getMinutes() + minutes);
                    return formatDateTimeISO(date);
                }

                function addHours(dateTimeStr, hours) {
                    const date = parseDateTime(dateTimeStr) || new Date();
                    date.setHours(date.getHours() + hours);
                    return formatDateTimeISO(date);
                }

                function addDays(dateTimeStr, days) {
                    const date = parseDateTime(dateTimeStr) || new Date();
                    date.setDate(date.getDate() + days);
                    return formatDateTimeISO(date);
                }

                function addMonths(dateTimeStr, months) {
                    const date = parseDateTime(dateTimeStr) || new Date();
                    date.setMonth(date.getMonth() + months);
                    return formatDateTimeISO(date);
                }

                function addYears(dateTimeStr, years) {
                    const date = parseDateTime(dateTimeStr) || new Date();
                    date.setFullYear(date.getFullYear() + years);
                    return formatDateTimeISO(date);
                }

                // Public API
                return {
                    init,
                    getValue,
                    setValue,
                    getDate,
                    getTime,
                    setMinDateTime,
                    setMaxDateTime,
                    setRange,
                    clear,
                    getInstance,
                    // Helpers
                    now,
                    today,
                    addMinutes,
                    addHours,
                    addDays,
                    addMonths,
                    addYears
                };
            })();
        </script>
    @endpush
@endonce
