document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.querySelector('.theme-toggle');
    const body = document.body;
    const form = document.getElementById('keyForm');
    const input = document.getElementById('keyInput');
    const message = document.getElementById('message');
    const card = document.querySelector('.card');

    const container = document.querySelector('.container');

    // 3D Tilt Effect
    if (container && card) {
        container.addEventListener('mousemove', (e) => {
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            // Calculate rotation (max +/- 10 degrees)
            const rotateY = -1 * ((x - rect.width / 2) / (rect.width / 2) * 10);
            const rotateX = ((y - rect.height / 2) / (rect.height / 2) * 10);

            card.style.transform = `perspective(1000px) rotateY(${rotateY}deg) rotateX(${rotateX}deg)`;
        });

        container.addEventListener('mouseleave', () => {
            // Reset position smoothly
            card.style.transform = `perspective(1000px) rotateY(0deg) rotateX(0deg)`;
            card.style.transition = "transform 0.5s ease";
        });

        container.addEventListener('mouseenter', () => {
            // Remove transition delay when entering to make movement instant/snappy
            card.style.transition = "transform 0.1s ease-out";
        });
    }

    // Theme Logic
    const savedTheme = localStorage.getItem('theme') || 'dark';
    body.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        // Sun Icon for Dark Mode (click to switch to light)
        const sunIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>';

        // Moon Icon for Light Mode (click to switch to dark)
        const moonIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>';

        themeToggle.innerHTML = theme === 'dark' ? sunIcon : moonIcon;
    }

    // Typing Animation
    let typingTimer;
    input.addEventListener('input', () => {
        input.classList.add('typing-active');
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            input.classList.remove('typing-active');
        }, 100); // Effect duration
    });

    // Toast Function
    function showToast(message, type = 'success') {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        let icon = '';
        if (type === 'success') {
            icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        } else {
            icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        }

        toast.innerHTML = `${icon}<span>${message}</span>`;
        container.appendChild(toast);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'toastSlideOut 0.4s forwards';
            toast.addEventListener('animationend', () => {
                toast.remove();
            });
        }, 3000);
    }

    // Key Verification
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const key = input.value.trim();
        const btn = form.querySelector('button');

        if (!key) return;

        // Reset state
        if (message) message.textContent = ''; // Clear old message if exists
        input.disabled = true;
        btn.classList.add('loading'); // Show spinner

        try {
            // Enforce minimum 800ms loading time for better UX
            const [response] = await Promise.all([
                fetch('verify.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `key=${encodeURIComponent(key)}`
                }),
                new Promise(resolve => setTimeout(resolve, 800)) // Wait at least 800ms
            ]);

            const data = await response.json();

            if (data.success) {
                showToast('Giriş Başarılı! Yönlendiriliyorsunuz...', 'success');
                // Keep loading state until redirect happens
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 500);
            } else {
                showToast('Girdiğiniz anahtar hatalı!', 'error');

                card.classList.add('shake');
                input.value = '';
                input.disabled = false;
                input.focus();
                btn.classList.remove('loading'); // Restore button

                setTimeout(() => {
                    card.classList.remove('shake');
                }, 500);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Sunucu hatası oluştu, lütfen tekrar deneyin.', 'error');
            input.disabled = false;
            btn.classList.remove('loading');
        }
    });

    // Typewriter Effect
    const typeWriterElement = document.getElementById('typewriter');
    if (typeWriterElement) {
        const textToType = "Keyi Girin.";
        let charIndex = 0;
        let isDeleting = false;

        function type() {
            if (!isDeleting && charIndex < textToType.length) {
                // Typing forward
                typeWriterElement.textContent = textToType.substring(0, charIndex + 1);
                charIndex++;
                setTimeout(type, 150); // Typing speed
            } else if (isDeleting && charIndex > 0) {
                // Deleting backward
                typeWriterElement.textContent = textToType.substring(0, charIndex - 1);
                charIndex--;
                setTimeout(type, 50); // Deleting speed
            } else {
                // Switch mode (Typed Full or Deleted Full)
                isDeleting = !isDeleting;

                if (isDeleting) {
                    // Finished typing, wait before deleting
                    setTimeout(type, 2000);
                } else {
                    // Finished deleting, wait before typing again
                    setTimeout(type, 500);
                }
            }
        }

        // Start typing after a small delay
        setTimeout(type, 500);
    }
});
