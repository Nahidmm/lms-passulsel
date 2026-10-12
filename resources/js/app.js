import './bootstrap';

// Auto-inject eye toggle into every password field
window.addEventListener('DOMContentLoaded', function () {
    const eyeSvg = '<svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
    const eyeOffSvg = '<svg class="eye-off-icon hidden" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>';

    document.querySelectorAll('input[type="password"]').forEach(function (input) {
        if (input.closest('.password-wrap')) return;

        const wrap = document.createElement('div');
        wrap.className = 'relative password-wrap';
        input.parentNode.insertBefore(wrap, input);
        wrap.appendChild(input);
        input.classList.add('pr-10');

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.tabIndex = -1;
        btn.setAttribute('aria-label', 'Lihat password');
        btn.className = 'absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors';
        btn.innerHTML = eyeSvg + eyeOffSvg;
        btn.addEventListener('click', function () {
            const willShow = input.type === 'password';
            input.type = willShow ? 'text' : 'password';
            const eye = wrap.querySelector('.eye-icon');
            const eyeOff = wrap.querySelector('.eye-off-icon');
            if (eye) eye.classList.toggle('hidden', willShow);
            if (eyeOff) eyeOff.classList.toggle('hidden', !willShow);
        });
        wrap.appendChild(btn);
    });
});
