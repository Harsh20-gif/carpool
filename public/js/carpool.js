/**
 * Smart Park & Share — Carpool Page Logic
 * public/js/carpool.js
 *
 * Handles:
 *  - Tab toggle between "Find a ride" and "Offer a ride"
 *  - "Request seat" button state update
 *  - Offer form day-chip selection
 */

document.addEventListener('DOMContentLoaded', () => {

  /* ── TAB TOGGLE ──────────────────────────────────────────── */
  const tabs    = document.querySelectorAll('.sp-tab[data-panel]');
  const panels  = document.querySelectorAll('.carpool-panel');

  function activateTab(targetId) {
    tabs.forEach(t => t.classList.toggle('active', t.getAttribute('data-panel') === targetId));
    panels.forEach(p => p.classList.toggle('d-none', p.id !== targetId));
    // Persist selection
    try { sessionStorage.setItem('carpool-tab', targetId); } catch(_) {}
  }

  tabs.forEach(tab => {
    tab.addEventListener('click', () => activateTab(tab.getAttribute('data-panel')));
  });

  // Restore last tab
  const savedTab = sessionStorage.getItem('carpool-tab');
  if (savedTab && document.getElementById(savedTab)) {
    activateTab(savedTab);
  } else if (tabs.length > 0) {
    activateTab(tabs[0].getAttribute('data-panel'));
  }

  /* ── REQUEST SEAT BUTTON ─────────────────────────────────── */
  document.querySelectorAll('.btn-request-seat').forEach(btn => {
    btn.addEventListener('click', function () {
      if (this.disabled) return;

      const card = this.closest('.ride-card');
      const driverName = card?.querySelector('.driver-name')?.textContent?.trim() || 'the driver';

      this.disabled = true;
      this.textContent = 'Requested';
      this.classList.remove('btn-primary-sp');
      this.classList.add('btn-outline-sp');

      showToast(`Seat requested — waiting for ${driverName} to confirm.`);
    });
  });

  /* ── OFFER FORM: SEAT CAPACITY DISPLAY ───────────────────── */
  const seatRange = document.getElementById('offer-seats');
  const seatDisplay = document.getElementById('offer-seats-display');
  if (seatRange && seatDisplay) {
    seatRange.addEventListener('input', () => {
      seatDisplay.textContent = seatRange.value;
    });
  }

  /* ── OFFER FORM SUBMIT ───────────────────────────────────── */
  const offerForm = document.getElementById('offer-ride-form');
  if (offerForm) {
    offerForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!offerForm.checkValidity()) {
        offerForm.classList.add('was-validated');
        showToast('Please complete all required fields.', 'warning');
        return;
      }
      // Check at least one day selected
      const daySelected = document.querySelector('.day-chip.selected');
      if (!daySelected) {
        showToast('Please select at least one day of the week.', 'warning');
        return;
      }
      showToast('Ride offer posted successfully!');
      offerForm.reset();
      offerForm.classList.remove('was-validated');
      document.querySelectorAll('.day-chip').forEach(c => c.classList.remove('selected'));
    });
  }
});
