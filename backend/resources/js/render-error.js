
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


// expose to Blade inline scripts
window.renderInputErrors_Global = renderInputErrors_Global;
