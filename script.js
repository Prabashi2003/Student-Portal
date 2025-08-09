// script.js
const wrapper = document.querySelector('.wrapper');
const registerLink = document.querySelector('.reg-Link');
const loginLink = document.querySelector('.Log-Link');

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const formType = urlParams.get('form');

    if (formType === 'register') {
        wrapper.classList.add('active');
    } else if (formType === 'login') {
        wrapper.classList.remove('active');
    }

    setupFormValidation();
    setupPasswordToggle();
    setupModeToggle();

});


registerLink.onclick = () => {
    wrapper.classList.add('active');
}

loginLink.onclick = () => {
    wrapper.classList.remove('active');
}

function setupFormValidation(){
    const loginForm = document.querySelector('.form-box.login form');
    const registerForm = document.querySelector('.form-box.register form');

    loginForm.addEventListener('submit', (e) => {
        const username = loginForm.username.value.trim();
        const password = loginForm.password.value.trim();

        if (!username || !password) {
            e.preventDefault();
            alert('Please fill in all fields.');
            return;
        }
    });

    registerForm.addEventListener('submit', (e) => {
        const username = registerForm.username.value.trim();
        const email = registerForm.email.value.trim();
        const password = registerForm.password.value.trim();

        if (!username || !email || !password) {
            e.preventDefault();
            alert('Please fill in all registration fields.');
            return;
        }

        if (!validateEmail(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters.');
            return;
        }
    });
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function setupPasswordToggle() {
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    
    passwordInputs.forEach(input => {
        const toggleButton = document.createElement('i');
        toggleButton.className = input.type === 'password' ? 'bx bx-hide' : 'bx bx-show';
        toggleButton.style.cursor = 'pointer';
        toggleButton.style.position = 'absolute';
        toggleButton.style.right = '5px';
        toggleButton.style.top = '50%';
        toggleButton.style.transform = 'translateY(-50%)';
        toggleButton.style.color = '#0ef';

        input.parentElement.style.position = 'relative';
        input.parentElement.appendChild(toggleButton);

        toggleButton.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                toggleButton.className = 'bx bx-show toggle-password';
            } else {
                input.type = 'password';
                toggleButton.className = 'bx bx-hide toggle-password';
            }
        });

    });
}


function setupModeToggle() {
    // Find the navigation bar and nav-links container
    const navbar = document.querySelector('nav');
    const navLinks = document.querySelector('.nav-links ul');
    
    if (!navbar || !navLinks) {
        console.warn('Navigation bar not found. Creating fallback button.');
        // Fallback to original floating button if navbar not found
        const btn = document.createElement('button');
        btn.textContent = 'Toggle Light/Dark Mode';
        btn.style.position = 'fixed';
        btn.style.bottom = '20px';
        btn.style.right = '20px';
        btn.style.padding = '10px 20px';
        btn.style.border = 'none';
        btn.style.borderRadius = '5px';
        btn.style.cursor = 'pointer';
        btn.style.backgroundColor = '#0ef';
        btn.style.color = '#081b29';
        btn.style.fontWeight = 'bold';
        document.body.appendChild(btn);
        
        btn.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');
        });
        return;
    }

    // Create a new list item for the toggle button
    const toggleLi = document.createElement('li');
    const toggleBtn = document.createElement('button');
    
    // Set up the button
    toggleBtn.innerHTML = '<i class="bx bx-sun"></i>';
    toggleBtn.className = 'mode-toggle-btn';
    toggleBtn.setAttribute('aria-label', 'Toggle light/dark mode');
    
    // Style the button to match nav links
    toggleBtn.style.background = 'transparent';
    toggleBtn.style.border = 'none';
    toggleBtn.style.color = '#0ef';
    toggleBtn.style.fontSize = '1.5rem';
    toggleBtn.style.cursor = 'pointer';
    toggleBtn.style.padding = '8px 12px';
    toggleBtn.style.borderRadius = '50%';
    toggleBtn.style.transition = 'all 0.3s ease';
    toggleBtn.style.display = 'flex';
    toggleBtn.style.alignItems = 'center';
    toggleBtn.style.justifyContent = 'center';

    // Add hover effect
    toggleBtn.addEventListener('mouseenter', () => {
        toggleBtn.style.backgroundColor = 'rgba(14, 239, 255, 0.1)';
        toggleBtn.style.transform = 'scale(1.1)';
    });
    
    toggleBtn.addEventListener('mouseleave', () => {
        toggleBtn.style.backgroundColor = 'transparent';
        toggleBtn.style.transform = 'scale(1)';
    });

    // Add the button to the list item and then to the nav
    toggleLi.appendChild(toggleBtn);
    navLinks.appendChild(toggleLi);

    // Toggle functionality
    toggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('light-mode');
        
        // Change icon based on mode
        if (document.body.classList.contains('light-mode')) {
            toggleBtn.innerHTML = '<i class="bx bx-moon"></i>'; // Moon icon for dark mode
        } else {
            toggleBtn.innerHTML = '<i class="bx bx-sun"></i>'; // Sun icon for light mode
        }
    });
}