/**
 * Smart Park & Share — Shared JavaScript
 * public/js/app.js
 */

/* ── NAV ACTIVE STATE ──────────────────────────────────────── */
(function () {
  const path = window.location.pathname;

  // Top navbar links
  document.querySelectorAll('.app-navbar .nav-link[data-route]').forEach(link => {
    const route = link.getAttribute('data-route');
    if (path === route || (route !== '/' && path.startsWith(route))) {
      link.classList.add('active');
    }
  });

  // Bottom nav links
  document.querySelectorAll('.bottom-nav-link[data-route]').forEach(link => {
    const route = link.getAttribute('data-route');
    if (path === route || (route !== '/' && path.startsWith(route))) {
      link.classList.add('active');
    }
  });
})();

/* ── TOAST HELPER ──────────────────────────────────────────── */
/**
 * showToast(message, type?)
 * type: 'success' | 'warning' | 'error' (default: 'success')
 */
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container-sp');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container-sp';
    document.body.appendChild(container);
  }

  const icons = {
    success: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`,
    warning: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 3h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
    error:   `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
  };

  const colors = {
    success: 'var(--color-available)',
    warning: 'var(--color-warning)',
    error:   'var(--color-danger)',
  };

  const toast = document.createElement('div');
  toast.className = 'sp-toast';
  toast.style.borderLeftColor = colors[type] || colors.success;
  toast.innerHTML = `<span style="color:${colors[type] || colors.success};flex-shrink:0">${icons[type] || icons.success}</span><span>${message}</span>`;
  container.appendChild(toast);

  // Force reflow, then show
  requestAnimationFrame(() => {
    requestAnimationFrame(() => toast.classList.add('show'));
  });

  setTimeout(() => {
    toast.classList.remove('show');
    toast.addEventListener('transitionend', () => toast.remove(), { once: true });
    // Fallback for reduced-motion
    setTimeout(() => { if (toast.parentNode) toast.remove(); }, 400);
  }, 3500);
}

/* ── STAR RATING INPUT HANDLER ─────────────────────────────── */
/**
 * initStarRating(containerSelector)
 * Initialises a set of .star elements inside the container.
 * Stars must have data-value="1..5".
 */
function initStarRating(containerSelector) {
  const containers = document.querySelectorAll(containerSelector);
  containers.forEach(container => {
    const stars = container.querySelectorAll('.star');

    stars.forEach(star => {
      star.addEventListener('click', () => {
        const value = parseInt(star.getAttribute('data-value'), 10);
        // Update hidden input if present
        const hiddenInput = container.closest('form')?.querySelector(`input[name="${container.dataset.name}"]`);
        if (hiddenInput) hiddenInput.value = value;
        // Fill visuals
        stars.forEach(s => {
          s.classList.toggle('filled', parseInt(s.getAttribute('data-value'), 10) <= value);
        });
        container.setAttribute('data-selected', value);
      });

      // Hover preview
      star.addEventListener('mouseenter', () => {
        const value = parseInt(star.getAttribute('data-value'), 10);
        stars.forEach(s => {
          s.classList.toggle('filled', parseInt(s.getAttribute('data-value'), 10) <= value);
        });
      });

      star.addEventListener('mouseleave', () => {
        const selected = parseInt(container.getAttribute('data-selected') || '0', 10);
        stars.forEach(s => {
          s.classList.toggle('filled', parseInt(s.getAttribute('data-value'), 10) <= selected);
        });
      });
    });
  });
}

/* ── FORM VALIDATION HELPER ────────────────────────────────── */
/**
 * initFormValidation(formSelector)
 * Adds Bootstrap was-validated class on submit, blocks submission if invalid.
 * Returns true if valid, false if not.
 */
function initFormValidation(formSelector) {
  const forms = document.querySelectorAll(formSelector);
  forms.forEach(form => {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        showToast('Please fill in all required fields correctly.', 'warning');
      }
      form.classList.add('was-validated');
    }, false);
  });
}

/* ── OTP INPUT AUTO-ADVANCE ────────────────────────────────── */
function initOtpInputs() {
  const inputs = document.querySelectorAll('.otp-input-group input');
  inputs.forEach((input, i) => {
    input.addEventListener('input', () => {
      if (input.value.length === 1 && i < inputs.length - 1) {
        inputs[i + 1].focus();
      }
    });
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && i > 0) {
        inputs[i - 1].focus();
      }
    });
  });
}

/* ── DAY CHIP TOGGLES ──────────────────────────────────────── */
function initDayChips() {
  document.querySelectorAll('.day-chip').forEach(chip => {
    chip.addEventListener('click', () => chip.classList.toggle('selected'));
  });
}

/* ── INIT ON DOM READY ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initStarRating('.star-input');
  initStarRating('.star-input-sm');
  initFormValidation('.sp-validated-form');
  initOtpInputs();
  initDayChips();
});
