/**
 * Smart Park & Share — Parking Page Logic
 * public/js/parking.js
 *
 * Handles:
 *  - Reserve button: decrements count in DOM, appends reservation row
 *  - Check-in button: updates row status
 */

document.addEventListener('DOMContentLoaded', () => {

  /* ── RESERVE BUTTON ──────────────────────────────────────── */
  document.querySelectorAll('.btn-reserve-zone').forEach(btn => {
    btn.addEventListener('click', function () {
      const zoneCard = this.closest('.zone-card');
      const zoneName = zoneCard.querySelector('.zone-label')?.textContent.trim() || 'Zone';
      const dateInput = zoneCard.querySelector('.zone-date-input');
      const timeInput = zoneCard.querySelector('.zone-time-input');

      // Validate date/time selection
      if (!dateInput?.value) {
        showToast('Please select a date before reserving.', 'warning');
        return;
      }

      const countEl = zoneCard.querySelector('.zone-count-num');
      let count = parseInt(countEl?.textContent || '0', 10);

      if (count <= 0) {
        showToast(`${zoneName} is full — no spots available.`, 'error');
        return;
      }

      // Decrement count
      count--;
      if (countEl) countEl.textContent = count;

      // Update status dot
      const dot = zoneCard.querySelector('.status-dot');
      const label = zoneCard.querySelector('.status-label');
      if (dot && label) {
        if (count === 0) {
          dot.className = 'status-dot full';
          label.textContent = 'Full';
        } else if (count <= 3) {
          dot.className = 'status-dot low';
          label.textContent = 'Low';
        }
      }

      // Disable button on this zone (one reservation per zone per session)
      this.disabled = true;
      this.textContent = 'Reserved';
      this.classList.add('btn-outline-sp');
      this.classList.remove('btn-primary-sp');

      // Append to My Reservations table
      const tbody = document.getElementById('reservations-tbody');
      if (tbody) {
        const dateStr   = dateInput?.value || new Date().toLocaleDateString('en-GB');
        const timeStr   = timeInput?.value || '09:00';
        const now       = new Date();
        const refNum    = `R${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}-${Math.floor(Math.random()*9000)+1000}`;

        const row = document.createElement('tr');
        row.innerHTML = `
          <td><span class="fw-medium">${refNum}</span></td>
          <td>${zoneName}</td>
          <td>${dateStr}</td>
          <td>${timeStr} – ${addHour(timeStr)}</td>
          <td><span class="sp-badge reserved">Reserved</span></td>
          <td>
            <button class="btn-accent-sp btn-sm py-1 px-3 btn-checkin" style="font-size:.8125rem">
              Check in
            </button>
          </td>`;

        tbody.insertAdjacentElement('afterbegin', row);
        initCheckInButtons();

        // Scroll to reservations section
        document.getElementById('my-reservations')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        showToast(`Spot reserved in ${zoneName} for ${dateStr}.`);
      }
    });
  });

  /* ── CHECK-IN BUTTON ─────────────────────────────────────── */
  function initCheckInButtons() {
    document.querySelectorAll('.btn-checkin').forEach(btn => {
      if (btn.dataset.bound) return;
      btn.dataset.bound = '1';
      btn.addEventListener('click', function () {
        const row = this.closest('tr');
        const badgeEl = row?.querySelector('.sp-badge');
        if (badgeEl) {
          badgeEl.className = 'sp-badge checkedin';
          badgeEl.textContent = 'Checked in';
        }
        this.disabled = true;
        this.textContent = 'Checked in';
        showToast('Check-in confirmed. Your spot is active.');
      });
    });
  }

  initCheckInButtons();

  /* ── UTILITY ─────────────────────────────────────────────── */
  function addHour(timeStr) {
    const [h, m] = timeStr.split(':').map(Number);
    const next = new Date();
    next.setHours(h + 1, m, 0);
    return `${String(next.getHours()).padStart(2,'0')}:${String(next.getMinutes()).padStart(2,'0')}`;
  }
});
