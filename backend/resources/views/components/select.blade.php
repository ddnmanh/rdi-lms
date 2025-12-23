{{--
    Custom Select Component - Thay thế select-option mặc định

    Cách sử dụng:
    1. Include component:
        @include('components.select', [
            'id' => 'status',
            'placeholder' => 'Chọn trạng thái...',
            'searchable' => true,  // Cho phép tìm kiếm (optional, default: false)
        ])

    2. Khởi tạo trong JavaScript:
       Select.init('status', {
            options: [
                { value: 'active', label: 'Hoạt động' },
                { value: 'inactive', label: 'Không hoạt động' },
                { value: 'pending', label: 'Chờ duyệt', disabled: true },
            ],
            // HOẶC load từ API:
            loadOptions: async () => {
                const data = await apiRequest('/statuses');
                return data.map(item => ({ value: item.id, label: item.name }));
            },
            defaultValue: 'active',           // Giá trị mặc định (optional)
            searchable: true,                 // Cho phép tìm kiếm (optional)
            clearable: true,                  // Cho phép xóa selection (optional, default: true)
            onChange: (value, option) => {    // Callback khi thay đổi (optional)
                console.log('Selected:', value, option);
            }
       });

    3. Các API khác:
       - Select.getValue('status')                    // Lấy giá trị
       - Select.getSelectedOption('status')           // Lấy option đã chọn { value, label }
       - Select.setValue('status', 'active')          // Set giá trị
       - Select.setOptions('status', [...])           // Set danh sách options
       - Select.clear('status')                       // Xóa selection
       - Select.disable('status')                     // Disable select
       - Select.enable('status')                      // Enable select
       - Select.refresh('status')                     // Reload options (nếu dùng loadOptions)
--}}

@php
    $placeholder = $placeholder ?? 'Chọn...';
    $searchable = $searchable ?? false;
    $loadingText = $loadingText ?? 'Đang tải...';
    $emptyText = $emptyText ?? 'Không có kết quả';
@endphp

<div class="relative" data-select-id="{{ $id }}">
    {{-- Hidden input để lưu giá trị thực --}}
    <input type="hidden" id="{{ $id }}" name="{{ $id }}" />

    {{-- Display button --}}
    <button type="button" id="{{ $id }}_trigger"
        class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-left focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none cursor-pointer transition-all duration-200 disabled:bg-gray-100 disabled:dark:bg-gray-700 disabled:cursor-not-allowed">
        <span id="{{ $id }}_label" class="block truncate text-gray-400 dark:text-gray-500">{{ $loadingText }}</span>
    </button>

    {{-- Icons --}}
    <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 pointer-events-none">
        <button type="button" id="{{ $id }}_clear"
            class="hidden text-gray-400 hover:text-red-500 transition-colors p-1 pointer-events-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <svg id="{{ $id }}_arrow" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    {{-- Dropdown --}}
    <div id="{{ $id }}_dropdown"
        class="hidden absolute z-[100] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl overflow-hidden">

        {{-- Search Input (conditional) --}}
        @if($searchable)
        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
            <div class="relative">
                <input type="text" id="{{ $id }}_search" placeholder="Tìm kiếm..."
                    class="w-full px-3 py-2 pl-9 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-sm" />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        @endif

        {{-- Options List --}}
        <div id="{{ $id }}_options" class="max-h-60 overflow-y-auto">
            <div class="flex items-center justify-center py-8">
                <div class="h-6 w-6 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></div>
            </div>
        </div>
    </div>
</div>

{{-- Chỉ include script 1 lần --}}
@once
    @push('scripts')
        <script>
            /**
             * Custom Select Component Manager
             */
            const Select = (function() {
                const instances = {};

                /**
                 * Initialize a select component
                 */
                function init(id, config = {}) {
                    const hiddenInput = document.getElementById(id);
                    const trigger = document.getElementById(`${id}_trigger`);
                    const label = document.getElementById(`${id}_label`);
                    const dropdown = document.getElementById(`${id}_dropdown`);
                    const optionsList = document.getElementById(`${id}_options`);
                    const clearBtn = document.getElementById(`${id}_clear`);
                    const arrow = document.getElementById(`${id}_arrow`);
                    const searchInput = document.getElementById(`${id}_search`);

                    if (!hiddenInput || !trigger || !dropdown) {
                        console.error(`Select: Elements not found for id "${id}"`);
                        return;
                    }

                    const instance = {
                        id,
                        hiddenInput,
                        trigger,
                        label,
                        dropdown,
                        optionsList,
                        clearBtn,
                        arrow,
                        searchInput,
                        isOpen: false,
                        isDisabled: false,
                        options: [],
                        selectedOption: null,
                        config: {
                            options: config.options || [],
                            loadOptions: config.loadOptions || null,
                            placeholder: config.placeholder || 'Chọn...',
                            emptyText: config.emptyText || 'Không có kết quả',
                            searchable: config.searchable ?? !!searchInput,
                            clearable: config.clearable ?? true,
                            onChange: config.onChange || null
                        }
                    };

                    instances[id] = instance;

                    // Set placeholder
                    label.textContent = instance.config.placeholder;
                    label.classList.add('text-gray-400', 'dark:text-gray-500');

                    // Load options
                    loadOptions(instance).then(() => {
                        // Set default value after options loaded
                        if (config.defaultValue !== undefined && config.defaultValue !== null) {
                            setValueInternal(instance, config.defaultValue);
                        }
                    });

                    // ==================== Event Listeners ====================

                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (!instance.isDisabled) {
                            toggleDropdown(id);
                        }
                    });

                    clearBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        clear(id);
                    });

                    if (searchInput) {
                        searchInput.addEventListener('input', (e) => {
                            filterOptions(instance, e.target.value);
                        });

                        searchInput.addEventListener('click', (e) => {
                            e.stopPropagation();
                        });
                    }

                    // Close on outside click
                    document.addEventListener('click', (e) => {
                        if (!e.target.closest(`[data-select-id="${id}"]`)) {
                            closeDropdown(id);
                        }
                    });

                    // Keyboard navigation
                    trigger.addEventListener('keydown', (e) => {
                        if (instance.isDisabled) return;

                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            toggleDropdown(id);
                        } else if (e.key === 'Escape') {
                            closeDropdown(id);
                        } else if (e.key === 'ArrowDown' && !instance.isOpen) {
                            e.preventDefault();
                            openDropdown(id);
                        }
                    });

                    return instance;
                }

                /**
                 * Load options from config or async function
                 */
                async function loadOptions(instance) {
                    const { config, optionsList } = instance;

                    // Show loading
                    optionsList.innerHTML = `
                        <div class="flex items-center justify-center py-8">
                            <div class="h-6 w-6 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></div>
                        </div>
                    `;

                    try {
                        if (config.loadOptions) {
                            instance.options = await config.loadOptions();
                        } else {
                            instance.options = config.options || [];
                        }
                        renderOptions(instance);
                    } catch (error) {
                        console.error(`Select: Error loading options for "${instance.id}":`, error);
                        optionsList.innerHTML = `
                            <div class="flex items-center justify-center py-8 text-red-500">
                                <span class="text-sm">Lỗi tải dữ liệu</span>
                            </div>
                        `;
                    }
                }

                /**
                 * Render options list
                 */
                function renderOptions(instance, searchTerm = '') {
                    const { options, optionsList, selectedOption, config } = instance;

                    // Filter options by search term
                    const filteredOptions = searchTerm
                        ? options.filter(opt => opt.label.toLowerCase().includes(searchTerm.toLowerCase()))
                        : options;

                    if (filteredOptions.length === 0) {
                        optionsList.innerHTML = `
                            <div class="flex items-center justify-center py-8">
                                <span class="text-sm text-gray-500 dark:text-gray-400">${config.emptyText}</span>
                            </div>
                        `;
                        return;
                    }

                    let html = '';
                    filteredOptions.forEach(option => {
                        const isSelected = selectedOption && selectedOption.value === option.value;
                        const isDisabled = option.disabled;

                        let classes = 'w-full px-4 py-2.5 text-left text-sm transition-colors flex items-center justify-between ';

                        if (isDisabled) {
                            classes += 'text-gray-400 dark:text-gray-500 cursor-not-allowed bg-gray-50 dark:bg-gray-900/30 ';
                        } else if (isSelected) {
                            classes += 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium ';
                        } else {
                            classes += 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer ';
                        }

                        const checkIcon = isSelected ? `
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        ` : '';

                        html += `
                            <button type="button"
                                class="${classes}"
                                data-value="${option.value}"
                                ${isDisabled ? 'disabled' : ''}>
                                <span class="truncate">${option.label}</span>
                                ${checkIcon}
                            </button>
                        `;
                    });

                    optionsList.innerHTML = html;

                    // Add click listeners
                    optionsList.querySelectorAll('button:not([disabled])').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            const value = btn.dataset.value;
                            selectOption(instance, value);
                            closeDropdown(instance.id);
                        });
                    });
                }

                /**
                 * Filter options by search term
                 */
                function filterOptions(instance, searchTerm) {
                    renderOptions(instance, searchTerm.trim());
                }

                /**
                 * Select an option by value
                 */
                function selectOption(instance, value) {
                    const option = instance.options.find(opt => String(opt.value) === String(value));
                    if (!option || option.disabled) return;

                    instance.selectedOption = option;
                    instance.hiddenInput.value = option.value;

                    // Update label
                    instance.label.textContent = option.label;
                    instance.label.classList.remove('text-gray-400', 'dark:text-gray-500');
                    instance.label.classList.add('text-gray-900', 'dark:text-white');

                    // Show clear button if clearable
                    if (instance.config.clearable) {
                        instance.clearBtn.classList.remove('hidden');
                    }

                    // Trigger onChange
                    if (instance.config.onChange) {
                        instance.config.onChange(option.value, option);
                    }

                    renderOptions(instance);
                }

                /**
                 * Toggle dropdown
                 */
                function toggleDropdown(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    if (instance.isOpen) {
                        closeDropdown(id);
                    } else {
                        openDropdown(id);
                    }
                }

                /**
                 * Open dropdown
                 */
                function openDropdown(id) {
                    const instance = instances[id];
                    if (!instance || instance.isDisabled) return;

                    // Close other dropdowns
                    Object.keys(instances).forEach(key => {
                        if (key !== id) closeDropdown(key);
                    });

                    instance.isOpen = true;
                    instance.dropdown.classList.remove('hidden');
                    instance.arrow.classList.add('rotate-180');

                    // Focus search input if exists
                    if (instance.searchInput) {
                        instance.searchInput.value = '';
                        instance.searchInput.focus();
                        renderOptions(instance);
                    }

                    // Scroll selected option into view
                    setTimeout(() => {
                        const selectedBtn = instance.optionsList.querySelector('.bg-blue-50, .dark\\:bg-blue-900\\/30');
                        if (selectedBtn) {
                            selectedBtn.scrollIntoView({ block: 'nearest' });
                        }
                    }, 10);
                }

                /**
                 * Close dropdown
                 */
                function closeDropdown(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.isOpen = false;
                    instance.dropdown.classList.add('hidden');
                    instance.arrow.classList.remove('rotate-180');

                    // Reset search
                    if (instance.searchInput) {
                        instance.searchInput.value = '';
                        renderOptions(instance);
                    }
                }

                /**
                 * Set value internally
                 */
                function setValueInternal(instance, value) {
                    if (value === null || value === undefined || value === '') {
                        instance.selectedOption = null;
                        instance.hiddenInput.value = '';
                        instance.label.textContent = instance.config.placeholder;
                        instance.label.classList.add('text-gray-400', 'dark:text-gray-500');
                        instance.label.classList.remove('text-gray-900', 'dark:text-white');
                        instance.clearBtn.classList.add('hidden');
                        renderOptions(instance);
                        return;
                    }

                    const option = instance.options.find(opt => String(opt.value) === String(value));
                    if (option && !option.disabled) {
                        selectOption(instance, value);
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
                 * Get selected option object
                 */
                function getSelectedOption(id) {
                    const instance = instances[id];
                    return instance ? instance.selectedOption : null;
                }

                /**
                 * Set value
                 */
                function setValue(id, value) {
                    const instance = instances[id];
                    if (!instance) return;

                    setValueInternal(instance, value);

                    if (instance.config.onChange) {
                        instance.config.onChange(instance.hiddenInput.value, instance.selectedOption);
                    }
                }

                /**
                 * Set options
                 */
                function setOptions(id, options) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.options = options || [];
                    instance.config.options = options || [];

                    // Re-select current value if exists
                    const currentValue = instance.hiddenInput.value;
                    instance.selectedOption = null;

                    renderOptions(instance);

                    if (currentValue) {
                        setValueInternal(instance, currentValue);
                    }
                }

                /**
                 * Clear selection
                 */
                function clear(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    const hadValue = !!instance.selectedOption;
                    setValueInternal(instance, null);

                    if (hadValue && instance.config.onChange) {
                        instance.config.onChange('', null);
                    }
                }

                /**
                 * Disable select
                 */
                function disable(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.isDisabled = true;
                    instance.trigger.disabled = true;
                    closeDropdown(id);
                }

                /**
                 * Enable select
                 */
                function enable(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    instance.isDisabled = false;
                    instance.trigger.disabled = false;
                }

                /**
                 * Refresh options (reload from loadOptions)
                 */
                async function refresh(id) {
                    const instance = instances[id];
                    if (!instance) return;

                    await loadOptions(instance);
                }

                /**
                 * Get instance
                 */
                function getInstance(id) {
                    return instances[id] || null;
                }

                // Public API
                return {
                    init,
                    getValue,
                    getSelectedOption,
                    setValue,
                    setOptions,
                    clear,
                    disable,
                    enable,
                    refresh,
                    getInstance
                };
            })();
        </script>
    @endpush
@endonce
