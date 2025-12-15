{{--
    Custom Date Picker Component - Tương thích mọi trình duyệt

    Cách sử dụng:
    1. Include component:
       @include('admin.components.date-picker', [
           'id' => 'birthday',
           'placeholder' => 'Chọn ngày sinh...',
       ])

    2. Khởi tạo trong JavaScript:
       DatePicker.init('birthday', {
           minDate: '1900-01-01',           // Ngày nhỏ nhất (optional)
           maxDate: '2024-12-31',           // Ngày lớn nhất (optional)
           defaultValue: '2000-01-15',      // Giá trị mặc định (optional)
           format: 'DD/MM/YYYY',            // Định dạng hiển thị (optional)
           onChange: (value) => {           // Callback khi thay đổi (optional)
               console.log('Selected:', value);
           }
       });

    3. Các API khác:
       - DatePicker.getValue('birthday')               // Lấy giá trị (YYYY-MM-DD)
       - DatePicker.setValue('birthday', '2000-01-15') // Set giá trị
       - DatePicker.setMinDate('birthday', '1990-01-01') // Set ngày min
       - DatePicker.setMaxDate('birthday', '2025-12-31') // Set ngày max
       - DatePicker.clear('birthday')                  // Xóa giá trị
       - DatePicker.setRange('birthday', min, max)     // Set khoảng ngày
--}}

@php
    $placeholder = $placeholder ?? 'Chọn ngày...';
@endphp

<div class="relative z-[100]" data-datepicker-id="{{ $id }}">
    {{-- Hidden input để lưu giá trị thực (YYYY-MM-DD) --}}
    <input type="hidden" id="{{ $id }}" name="{{ $id }}" />

    {{-- Display input --}}
    <div class="relative">
        <input type="text" id="{{ $id }}_display" placeholder="{{ $placeholder }}" readonly
            class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none cursor-pointer" />
        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-2 cursor-pointer">
            <button type="button" id="{{ $id }}_clear" class="hidden text-gray-400 hover:text-red-500 transition-all duration-300 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <label for="{{ $id }}_display" class="cursor-pointer">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </label>
        </div>
    </div>

    {{-- Calendar Dropdown --}}
    <div id="{{ $id }}_calendar"
        class="hidden absolute z-[100] mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl p-2 w-[260px]">

        {{-- Header: Month/Year Navigation --}}
        <div class="flex items-center justify-between mb-4">
            <button type="button" id="{{ $id }}_prevMonth"
                class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
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
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
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

        {{-- Footer: Today button --}}
        <div class="mt-1.5 pt-1.5 border-t border-gray-200 dark:border-gray-700 flex justify-center">
            <button type="button" id="{{ $id }}_todayBtn"
                class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                Hôm nay
            </button>
        </div>
    </div>
</div>

{{-- Chỉ include script 1 lần --}}
@once
    @push('scripts')
        <script>
            /**
             * Custom DatePicker Component Manager
             */
            const DatePicker = (function() {
                const instances = {};

                const MONTHS = [
                    'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                ];

                /**
                 * Initialize a date picker component
                 */
                function init(id, config = {}) {
                    const hiddenInput = document.getElementById(id);
                    const displayInput = document.getElementById(`${id}_display`);
                    const calendar = document.getElementById(`${id}_calendar`);
                    const clearBtn = document.getElementById(`${id}_clear`);
                    const daysGrid = document.getElementById(`${id}_daysGrid`);
                    const monthSelect = document.getElementById(`${id}_monthSelect`);
                    const yearSelect = document.getElementById(`${id}_yearSelect`);
                    const prevMonthBtn = document.getElementById(`${id}_prevMonth`);
                    const nextMonthBtn = document.getElementById(`${id}_nextMonth`);
                    const todayBtn = document.getElementById(`${id}_todayBtn`);

                    if (!hiddenInput || !displayInput || !calendar) {
                        console.error(`DatePicker: Elements not found for id "${id}"`);
                        return;
                    }

                    const instance = {
                        id,
                        hiddenInput,
                        displayInput,
                        calendar,
                        clearBtn,
                        daysGrid,
                        monthSelect,
                        yearSelect,
                        isOpen: false,
                        currentMonth: new Date().getMonth(),
                        currentYear: new Date().getFullYear(),
                        selectedDate: null,
                        config: {
                            minDate: config.minDate ? parseDate(config.minDate) : null,
                            maxDate: config.maxDate ? parseDate(config.maxDate) : null,
                            format: config.format || 'DD/MM/YYYY',
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

                    // Set default value
                    if (config.defaultValue) {
                        setValueInternal(instance, config.defaultValue);
                    }

                    // Event listeners
                    displayInput.addEventListener('click', () => toggleCalendar(id));

                    clearBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        clear(id);
                    });

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
                            selectDate(instance, today);
                            closeCalendar(id);
                        } else {
                            // NotificationModal.show('Ngày hôm nay không nằm trong khoảng cho phép', 'warning');
                            NotificationModal.show('Ngày hôm nay không nằm trong khoảng cho phép', 'warning');
                        }
                    });

                    // Close on outside click
                    document.addEventListener('click', (e) => {
                        if (!e.target.closest(`[data-datepicker-id="${id}"]`)) {
                            closeCalendar(id);
                        }
                    });

                    // Render initial calendar
                    renderCalendar(instance);

                    return instance;
                }

                /**
                 * Populate year select based on min/max dates
                 */
                function populateYearSelect(instance) {
                    const {
                        yearSelect,
                        config
                    } = instance;
                    yearSelect.innerHTML = '';

                    const currentYear = new Date().getFullYear();
                    let minYear = config.minDate ? config.minDate.getFullYear() : currentYear - 100;
                    let maxYear = config.maxDate ? config.maxDate.getFullYear() : currentYear + 10;

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
                 * Navigate to previous/next month
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

                    // Check year bounds
                    const {
                        config
                    } = instance;
                    if (config.minDate && instance.currentYear < config.minDate.getFullYear()) {
                        instance.currentYear = config.minDate.getFullYear();
                        instance.currentMonth = config.minDate.getMonth();
                    }
                    if (config.maxDate && instance.currentYear > config.maxDate.getFullYear()) {
                        instance.currentYear = config.maxDate.getFullYear();
                        instance.currentMonth = config.maxDate.getMonth();
                    }

                    renderCalendar(instance);
                }

                /**
                 * Render the calendar
                 */
                function renderCalendar(instance) {
                    const {
                        daysGrid,
                        monthSelect,
                        yearSelect,
                        currentMonth,
                        currentYear,
                        selectedDate,
                        config
                    } = instance;

                    // Update selects
                    monthSelect.value = currentMonth;
                    yearSelect.value = currentYear;

                    // Clear grid
                    daysGrid.innerHTML = '';

                    // Get first day of month and total days
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

                    // Next month days (fill remaining cells)
                    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
                    const remainingCells = totalCells - (firstDay + daysInMonth);
                    for (let day = 1; day <= remainingCells; day++) {
                        const date = new Date(currentYear, currentMonth + 1, day);
                        const dayEl = createDayElement(instance, date, true);
                        daysGrid.appendChild(dayEl);
                    }
                }

                /**
                 * Create a day element
                 */
                function createDayElement(instance, date, isOtherMonth) {
                    const {
                        selectedDate,
                        config
                    } = instance;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    const isToday = date.toDateString() === today.toDateString();
                    const isSelected = selectedDate && date.toDateString() === selectedDate.toDateString();
                    const isDisabled = !isDateInRange(instance, date);
                    const isSunday = date.getDay() === 0;

                    const dayEl = document.createElement('button');
                    dayEl.type = 'button';
                    dayEl.textContent = date.getDate();

                    let classes =
                        'w-full aspect-square text-sm rounded-md transition-all duration-200 flex items-center justify-center ';
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
                            selectDate(instance, date);
                            closeCalendar(instance.id);
                        });
                    }

                    return dayEl;
                }

                /**
                 * Check if date is within min/max range
                 */
                function isDateInRange(instance, date) {
                    const {
                        config
                    } = instance;
                    const checkDate = new Date(date);
                    checkDate.setHours(0, 0, 0, 0);

                    if (config.minDate) {
                        const min = new Date(config.minDate);
                        min.setHours(0, 0, 0, 0);
                        if (checkDate < min) return false;
                    }

                    if (config.maxDate) {
                        const max = new Date(config.maxDate);
                        max.setHours(0, 0, 0, 0);
                        if (checkDate > max) return false;
                    }

                    return true;
                }

                /**
                 * Select a date
                 */
                function selectDate(instance, date) {
                    instance.selectedDate = date;
                    instance.currentMonth = date.getMonth();
                    instance.currentYear = date.getFullYear();

                    // Update hidden input (YYYY-MM-DD)
                    instance.hiddenInput.value = formatDateISO(date);

                    // Update display input
                    instance.displayInput.value = formatDateDisplay(date, instance.config.format);

                    // Show clear button
                    instance.clearBtn.classList.remove('hidden');

                    // Trigger onChange
                    if (instance.config.onChange) {
                        instance.config.onChange(instance.hiddenInput.value);
                    }

                    renderCalendar(instance);
                }

                /**
                 * Toggle calendar visibility
                 */
                function toggleCalendar(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    if (instance.isOpen) {
                        closeCalendar(id);
                    } else {
                        openCalendar(id);
                    }
                }

                /**
                 * Open calendar
                 */
                function openCalendar(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    // Close other calendars
                    Object.keys(instances).forEach(key => {
                        if (key !== id) closeCalendar(key);
                    });

                    instance.isOpen = true;
                    instance.calendar.classList.remove('hidden');

                    // If has selected date, navigate to that month
                    if (instance.selectedDate) {
                        instance.currentMonth = instance.selectedDate.getMonth();
                        instance.currentYear = instance.selectedDate.getFullYear();
                    }

                    renderCalendar(instance);
                }

                /**
                 * Close calendar
                 */
                function closeCalendar(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.isOpen = false;
                    instance.calendar.classList.add('hidden');
                }

                /**
                 * Parse date string to Date object
                 */
                function parseDate(dateStr) {
                    if (!dateStr) return null;
                    if (dateStr instanceof Date) return dateStr;

                    // Handle ISO format with time
                    if (dateStr.includes('T')) {
                        dateStr = dateStr.split('T')[0];
                    }

                    const [year, month, day] = dateStr.split('-').map(Number);
                    return new Date(year, month - 1, day);
                }

                /**
                 * Format date to ISO string (YYYY-MM-DD)
                 */
                function formatDateISO(date) {
                    if (!date) return '';
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }

                /**
                 * Format date for display
                 */
                function formatDateDisplay(date, format = 'DD/MM/YYYY') {
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
                 * Set value internally
                 */
                function setValueInternal(instance, value) {
                    if (!value) {
                        instance.selectedDate = null;
                        instance.hiddenInput.value = '';
                        instance.displayInput.value = '';
                        instance.clearBtn.classList.add('hidden');
                        return;
                    }

                    const date = parseDate(value);
                    if (date && !isNaN(date.getTime())) {
                        instance.selectedDate = date;
                        instance.currentMonth = date.getMonth();
                        instance.currentYear = date.getFullYear();
                        instance.hiddenInput.value = formatDateISO(date);
                        instance.displayInput.value = formatDateDisplay(date, instance.config.format);
                        instance.clearBtn.classList.remove('hidden');
                    }
                }

                // ==================== PUBLIC API ====================

                /**
                 * Get the current value
                 */
                function getValue(id) {
                    const instance = instances[id];
                    return instance ? instance.hiddenInput.value : '';
                }

                /**
                 * Set the value
                 */
                function setValue(id, value) {
                    const instance = instances[id];
                    if (!instance) return;

                    setValueInternal(instance, value);

                    if (instance.config.onChange) {
                        instance.config.onChange(instance.hiddenInput.value);
                    }

                    renderCalendar(instance);
                }

                /**
                 * Set minimum date
                 */
                function setMinDate(id, minDate) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.config.minDate = minDate ? parseDate(minDate) : null;
                    populateYearSelect(instance);
                    adjustCurrentYearToValidRange(instance);
                    renderCalendar(instance);
                }

                /**
                 * Set maximum date
                 */
                function setMaxDate(id, maxDate) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.config.maxDate = maxDate ? parseDate(maxDate) : null;
                    populateYearSelect(instance);
                    adjustCurrentYearToValidRange(instance);
                    renderCalendar(instance);
                }

                /**
                 * Set date range
                 */
                function setRange(id, minDate, maxDate) {
                    setMinDate(id, minDate);
                    setMaxDate(id, maxDate);
                }

                /**
                 * Clear the value
                 */
                function clear(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    setValueInternal(instance, null);

                    if (instance.config.onChange) {
                        instance.config.onChange('');
                    }

                    renderCalendar(instance);
                }

                /**
                 * Get instance
                 */
                function getInstance(id) {
                    return instances[id] || null;
                }

                // ==================== HELPERS ====================

                function today() {
                    return formatDateISO(new Date());
                }

                function addDays(dateStr, days) {
                    const date = parseDate(dateStr) || new Date();
                    date.setDate(date.getDate() + days);
                    return formatDateISO(date);
                }

                function addMonths(dateStr, months) {
                    const date = parseDate(dateStr) || new Date();
                    date.setMonth(date.getMonth() + months);
                    return formatDateISO(date);
                }

                function addYears(dateStr, years) {
                    const date = parseDate(dateStr) || new Date();
                    date.setFullYear(date.getFullYear() + years);
                    return formatDateISO(date);
                }

                // Public API
                return {
                    init,
                    getValue,
                    setValue,
                    setMinDate,
                    setMaxDate,
                    setRange,
                    clear,
                    getInstance,
                    // Helpers
                    today,
                    addDays,
                    addMonths,
                    addYears
                };
            })();
        </script>
    @endpush
@endonce
