{{--
    Multi-Select Component với Search

    Cách sử dụng:
    1. Include component:
       @include('admin.components.multi-select', [
           'id' => 'roles',
           'placeholder' => 'Chọn vai trò...',
           'loadingText' => 'Đang tải vai trò...',
           'searchPlaceholder' => 'Tìm kiếm vai trò...',
           'emptyText' => 'Không tìm thấy vai trò'
       ])

    2. Khởi tạo trong JavaScript:
       MultiSelect.init('roles', {
           loadData: async () => {
               const data = await apiRequest('/roles');
               return data.success ? data.data.data : [];
           },
           displayField: 'name',   // Trường hiển thị (mặc định: 'name')
           valueField: 'id',       // Trường giá trị (mặc định: 'id')
           onSelectionChange: (selectedItems) => {
               console.log('Selected:', selectedItems);
           }
       });

    3. Các API khác:
       - MultiSelect.setSelected('roles', items)     // Set các item đã chọn
       - MultiSelect.getSelected('roles')            // Lấy các item đã chọn
       - MultiSelect.clear('roles')                  // Xóa tất cả selection
       - MultiSelect.refresh('roles')                // Reload dữ liệu
--}}

@php
    // Set default values for optional parameters
    $placeholder = $placeholder ?? 'Chọn...';
    $loadingText = $loadingText ?? 'Đang tải...';
    $searchPlaceholder = $searchPlaceholder ?? 'Tìm kiếm...';
    $emptyText = $emptyText ?? 'Không tìm thấy kết quả';
@endphp

<div class="relative" data-multiselect-id="{{ $id }}">
    {{-- Selected Items Display --}}
    <div id="{{ $id }}SelectedContainer"
        class="min-h-[44px] w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 cursor-pointer flex flex-wrap gap-2 items-center">
        <div id="{{ $id }}SelectedList" class="flex flex-wrap gap-2 flex-1">
            <span class="text-gray-400 dark:text-gray-500 text-sm">{{ $loadingText }}</span>
        </div>
        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform duration-200" id="{{ $id }}DropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    {{-- Dropdown Menu --}}
    <div id="{{ $id }}Dropdown"
        class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-80 overflow-hidden flex flex-col">
        {{-- Search Input --}}
        <div class="p-3 border-b border-gray-200 dark:border-gray-700">
            <div class="relative">
                <input
                    type="text"
                    id="{{ $id }}SearchInput"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full px-3 py-2 pl-9 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        {{-- Options List --}}
        <div id="{{ $id }}OptionsList" class="overflow-y-auto flex-1">
            <div class="flex items-center justify-center py-8">
                <div class="h-8 w-8 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></div>
            </div>
        </div>
    </div>
</div>

{{-- Chỉ include script 1 lần --}}
@once
@push('scripts')
<script>
    /**
     * MultiSelect Component Manager
     */
    const MultiSelect = (function() {
        // Store instances
        const instances = {};

        /**
         * Initialize a multi-select component
         * @param {string} id - Component ID
         * @param {Object} config - Configuration object
         * @param {Function} config.loadData - Async function that returns array of items
         * @param {string} [config.displayField='name'] - Field to display
         * @param {string} [config.valueField='id'] - Field for value/ID
         * @param {Function} [config.onSelectionChange] - Callback when selection changes
         */
        function init(id, config = {}) {
            const instance = {
                id,
                config: {
                    displayField: config.displayField || 'name',
                    valueField: config.valueField || 'id',
                    loadData: config.loadData || (async () => []),
                    onSelectionChange: config.onSelectionChange || null,
                    placeholder: config.placeholder || 'Chọn...',
                    emptyText: config.emptyText || 'Không tìm thấy kết quả'
                },
                items: [],
                selectedItems: [],
                isOpen: false
            };

            instances[id] = instance;

            // Get DOM elements
            const container = document.getElementById(`${id}SelectedContainer`);
            const dropdown = document.getElementById(`${id}Dropdown`);
            const searchInput = document.getElementById(`${id}SearchInput`);

            if (!container || !dropdown || !searchInput) {
                console.error(`MultiSelect: Elements not found for id "${id}"`);
                return;
            }

            // Toggle dropdown on container click
            container.addEventListener('click', (e) => {
                if (e.target.closest('.multiselect-tag-remove')) return;
                toggleDropdown(id);
            });

            // Search functionality
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase().trim();
                filterOptions(id, searchTerm);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest(`[data-multiselect-id="${id}"]`)) {
                    closeDropdown(id);
                }
            });

            // Prevent dropdown close when clicking inside
            dropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // Load data
            loadData(id);

            return instance;
        }

        /**
         * Load data using the loadData callback
         */
        async function loadData(id) {
            const instance = instances[id];
            if (!instance) return;

            try {
                instance.items = await instance.config.loadData();
                renderOptions(id);
                updateSelectedDisplay(id);
            } catch (error) {
                console.error(`MultiSelect: Error loading data for "${id}":`, error);
                const optionsList = document.getElementById(`${id}OptionsList`);
                if (optionsList) {
                    optionsList.innerHTML = `
                        <div class="flex items-center justify-center py-8">
                            <p class="text-red-500">Lỗi tải dữ liệu</p>
                        </div>
                    `;
                }
            }
        }

        /**
         * Toggle dropdown open/close
         */
        function toggleDropdown(id) {
            const instance = instances[id];
            if (!instance) return;

            instance.isOpen = !instance.isOpen;
            const dropdown = document.getElementById(`${id}Dropdown`);
            const icon = document.getElementById(`${id}DropdownIcon`);

            if (instance.isOpen) {
                dropdown.classList.remove('hidden');
                icon?.classList.add('rotate-180');
                document.getElementById(`${id}SearchInput`)?.focus();
            } else {
                dropdown.classList.add('hidden');
                icon?.classList.remove('rotate-180');
            }
        }

        /**
         * Close dropdown
         */
        function closeDropdown(id) {
            const instance = instances[id];
            if (!instance || !instance.isOpen) return;

            instance.isOpen = false;
            const dropdown = document.getElementById(`${id}Dropdown`);
            const searchInput = document.getElementById(`${id}SearchInput`);
            const icon = document.getElementById(`${id}DropdownIcon`);

            dropdown?.classList.add('hidden');
            icon?.classList.remove('rotate-180');
            if (searchInput) {
                searchInput.value = '';
                filterOptions(id, '');
            }
        }

        /**
         * Filter and render options based on search term
         */
        function filterOptions(id, searchTerm = '') {
            const instance = instances[id];
            if (!instance) return;

            const { items, selectedItems, config } = instance;
            const { displayField, valueField, emptyText } = config;

            // Filter: not selected AND matches search
            const filteredItems = items.filter(item => {
                const isNotSelected = !selectedItems.some(s => s[valueField] === item[valueField]);
                const matchesSearch = item[displayField].toLowerCase().includes(searchTerm);
                return isNotSelected && matchesSearch;
            });

            const optionsList = document.getElementById(`${id}OptionsList`);
            if (!optionsList) return;

            if (filteredItems.length === 0) {
                optionsList.innerHTML = `
                    <div class="flex items-center justify-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">${emptyText}</p>
                    </div>
                `;
                return;
            }

            let html = '';
            filteredItems.forEach(item => {
                const itemId = item[valueField];
                const itemName = item[displayField];
                html += `
                    <div onclick="MultiSelect.toggle('${id}', ${typeof itemId === 'string' ? `'${itemId}'` : itemId})"
                        class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded"></div>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">${itemName}</span>
                    </div>
                `;
            });
            optionsList.innerHTML = html;
        }

        /**
         * Render all options
         */
        function renderOptions(id) {
            filterOptions(id, '');
        }

        /**
         * Toggle item selection
         */
        function toggle(id, itemId) {
            const instance = instances[id];
            if (!instance) return;

            const { items, selectedItems, config } = instance;
            const { valueField } = config;

            const item = items.find(i => i[valueField] === itemId);
            if (!item) return;

            const index = selectedItems.findIndex(s => s[valueField] === itemId);
            if (index > -1) {
                selectedItems.splice(index, 1);
            } else {
                selectedItems.push(item);
            }

            // Clear search and refresh
            const searchInput = document.getElementById(`${id}SearchInput`);
            if (searchInput) searchInput.value = '';

            updateSelectedDisplay(id);
            renderOptions(id);

            // Trigger callback
            if (config.onSelectionChange) {
                config.onSelectionChange([...selectedItems]);
            }
        }

        /**
         * Remove item from selection
         */
        function remove(id, itemId) {
            const instance = instances[id];
            if (!instance) return;

            const { selectedItems, config } = instance;
            const { valueField } = config;

            const index = selectedItems.findIndex(s => s[valueField] === itemId);
            if (index > -1) {
                selectedItems.splice(index, 1);
                updateSelectedDisplay(id);
                renderOptions(id);

                if (config.onSelectionChange) {
                    config.onSelectionChange([...selectedItems]);
                }
            }
        }

        /**
         * Update the selected items display
         */
        function updateSelectedDisplay(id) {
            const instance = instances[id];
            if (!instance) return;

            const { selectedItems, config } = instance;
            const { displayField, valueField, placeholder } = config;
            const container = document.getElementById(`${id}SelectedList`);

            if (!container) return;

            if (selectedItems.length === 0) {
                container.innerHTML = `<span class="text-gray-400 dark:text-gray-500 text-sm">${placeholder}</span>`;
                return;
            }

            let html = '';
            selectedItems.forEach(item => {
                const itemId = item[valueField];
                const itemName = item[displayField];
                html += `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 pr-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm font-medium">
                        ${itemName}
                        <button type="button" onclick="event.stopPropagation(); MultiSelect.remove('${id}', ${typeof itemId === 'string' ? `'${itemId}'` : itemId})"
                            class="multiselect-tag-remove hover:bg-blue-200 dark:hover:bg-blue-800 rounded-full p-0.5 transition-colors">
                            <div class="w-3.5 h-3.5 text-white dark:text-black bg-blue-700 dark:bg-blue-300 rounded-full flex items-center justify-center">
                                <svg class="w-[8px] h-[8px]" viewBox="0 0 384 512" fill="currentColor">
                                    <path d="M55.1 73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L147.2 256 9.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192.5 301.3 329.9 438.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.8 256 375.1 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192.5 210.7 55.1 73.4z"/>
                                </svg>
                            </div>
                        </button>
                    </span>
                `;
            });
            container.innerHTML = html;
        }

        /**
         * Set selected items
         * @param {string} id - Component ID
         * @param {Array} items - Array of items to select
         */
        function setSelected(id, items) {
            const instance = instances[id];
            if (!instance) return;

            instance.selectedItems = Array.isArray(items) ? [...items] : [];
            updateSelectedDisplay(id);
            renderOptions(id);

            if (instance.config.onSelectionChange) {
                instance.config.onSelectionChange([...instance.selectedItems]);
            }
        }

        /**
         * Get selected items
         * @param {string} id - Component ID
         * @returns {Array} Selected items
         */
        function getSelected(id) {
            const instance = instances[id];
            return instance ? [...instance.selectedItems] : [];
        }

        /**
         * Clear all selections
         * @param {string} id - Component ID
         */
        function clear(id) {
            setSelected(id, []);
        }

        /**
         * Refresh/reload data
         * @param {string} id - Component ID
         */
        function refresh(id) {
            loadData(id);
        }

        /**
         * Get instance
         * @param {string} id - Component ID
         */
        function getInstance(id) {
            return instances[id] || null;
        }

        // Public API
        return {
            init,
            toggle,
            remove,
            setSelected,
            getSelected,
            clear,
            refresh,
            getInstance
        };
    })();
</script>
@endpush
@endonce
