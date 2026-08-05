document.addEventListener('DOMContentLoaded', function () {
  const today = new Date();
  function toStr(d) { return d.toISOString().split('T')[0]; }

  const mock1in = new Date(today.getFullYear(), today.getMonth(), 12);
  const mock1out = new Date(today.getFullYear(), today.getMonth(), 15);
  const mock2in = new Date(today.getFullYear(), today.getMonth(), 22);
  const mock2out = new Date(today.getFullYear(), today.getMonth(), 24);

  let bookedRanges = [
    { checkin_date: toStr(mock1in), checkout_date: toStr(mock1out) },
    { checkin_date: toStr(mock2in), checkout_date: toStr(mock2out) }
  ];

  let pricePerNight = window.QUATTRO_PRICE_PER_NIGHT || 4500;
  let currency = window.QUATTRO_CURRENCY || 'KES';
  let minStay = window.QUATTRO_MIN_STAY || 1;
  let discountPercent = window.QUATTRO_DISCOUNT_PERCENT || 0;
  let discountMinNights = window.QUATTRO_DISCOUNT_MIN_NIGHTS || 0;
  let includedGuests = window.QUATTRO_INCLUDED_GUESTS || 2;
  let extraGuestFee = window.QUATTRO_EXTRA_GUEST_FEE || 0;

  let viewYear = today.getFullYear();
  let viewMonth = today.getMonth();

  const calGrid = document.getElementById('cal-grid');
  const calLabel = document.getElementById('cal-month-label');
  const prevBtn = document.getElementById('cal-prev');
  const nextBtn = document.getElementById('cal-next');

  function isBooked(dateStr) {
    return bookedRanges.some(r => dateStr >= r.checkin_date && dateStr < r.checkout_date);
  }

  function renderCalendar() {
    calGrid.innerHTML = '';
    const dows = ['Su','Mo','Tu','We','Th','Fr','Sa'];
    dows.forEach(d => {
      const el = document.createElement('div');
      el.className = 'dow';
      el.textContent = d;
      calGrid.appendChild(el);
    });

    const firstDay = new Date(viewYear, viewMonth, 1);
    const startOffset = firstDay.getDay();
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

    for (let i = 0; i < startOffset; i++) {
      const el = document.createElement('div');
      el.className = 'cal-day empty';
      calGrid.appendChild(el);
    }

    const todayStr = toStr(new Date());

    for (let day = 1; day <= daysInMonth; day++) {
      const cellDate = new Date(viewYear, viewMonth, day);
      const cellStr = toStr(cellDate);
      const el = document.createElement('div');
      el.textContent = day;
      el.className = 'cal-day';

      if (cellStr < todayStr) {
        el.classList.add('past');
      } else if (isBooked(cellStr)) {
        el.classList.add('booked');
      } else {
        el.classList.add('available');
      }
      calGrid.appendChild(el);
    }

    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    calLabel.textContent = monthNames[viewMonth] + ' ' + viewYear;
  }

  prevBtn.addEventListener('click', () => {
    viewMonth--;
    if (viewMonth < 0) { viewMonth = 11; viewYear--; }
    renderCalendar();
  });
  nextBtn.addEventListener('click', () => {
    viewMonth++;
    if (viewMonth > 11) { viewMonth = 0; viewYear++; }
    renderCalendar();
  });

  renderCalendar();

  let currentCalFloor = 'floor2';
  const floorSelect = document.getElementById('floor');
  if (floorSelect) currentCalFloor = floorSelect.value || 'floor2';

  // Mock booked-date sets per floor for the static preview
  const mockRangesByFloor = {
    floor1: [
      { checkin_date: toStr(new Date(today.getFullYear(), today.getMonth(), 5)), checkout_date: toStr(new Date(today.getFullYear(), today.getMonth(), 8)) }
    ],
    floor2: [
      { checkin_date: toStr(mock1in), checkout_date: toStr(mock1out) },
      { checkin_date: toStr(mock2in), checkout_date: toStr(mock2out) }
    ],
    both: [
      { checkin_date: toStr(mock1in), checkout_date: toStr(mock1out) },
      { checkin_date: toStr(mock2in), checkout_date: toStr(mock2out) },
      { checkin_date: toStr(new Date(today.getFullYear(), today.getMonth(), 5)), checkout_date: toStr(new Date(today.getFullYear(), today.getMonth(), 8)) }
    ]
  };

  function fetchBookedDates(floor) {
    bookedRanges = mockRangesByFloor[floor] || [];
    renderCalendar();
    if (typeof updatePriceSummary === 'function') updatePriceSummary();
  }

  fetchBookedDates(currentCalFloor);

  const calFloorTabs = document.querySelectorAll('.cal-floor-tab');
  function setCalFloor(floor) {
    currentCalFloor = floor;
    calFloorTabs.forEach(t => t.classList.toggle('active', t.getAttribute('data-cal-floor') === floor));
    fetchBookedDates(floor);
  }
  calFloorTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const floor = tab.getAttribute('data-cal-floor');
      if (floorSelect) floorSelect.value = floor;
      setCalFloor(floor);
    });
  });
  if (floorSelect) {
    floorSelect.addEventListener('change', () => setCalFloor(floorSelect.value));
  }

  const checkinInput = document.getElementById('checkin_date');
  const checkoutInput = document.getElementById('checkout_date');
  const todayISO = toStr(new Date());
  checkinInput.setAttribute('min', todayISO);
  checkoutInput.setAttribute('min', todayISO);

  const priceSummary = document.getElementById('price-summary');
  const priceNights = document.getElementById('price-nights');
  const priceTotal = document.getElementById('price-total');
  const feedback = document.getElementById('form-feedback');
  const form = document.getElementById('booking-form');
  const submitBtn = document.getElementById('submit-btn');
  const guestsInput = document.getElementById('guests');

  function rangeOverlapsBooked(inVal, outVal) {
    return bookedRanges.some(r => inVal < r.checkout_date && outVal > r.checkin_date);
  }

  function updatePriceSummary() {
    const inVal = checkinInput.value;
    const outVal = checkoutInput.value;
    if (!inVal || !outVal) { priceSummary.style.display = 'none'; return; }

    const inDate = new Date(inVal);
    const outDate = new Date(outVal);
    const nights = Math.round((outDate - inDate) / (1000 * 60 * 60 * 24));

    if (nights <= 0) { priceSummary.style.display = 'none'; return; }

    if (rangeOverlapsBooked(inVal, outVal)) {
      priceSummary.style.display = 'none';
      feedback.textContent = 'One or more of the selected dates is already booked. Please choose different dates.';
      feedback.className = 'error';
      submitBtn.disabled = true;
      return;
    }
    if (submitBtn.disabled && feedback.textContent.indexOf('already booked') !== -1) {
      submitBtn.disabled = false;
    }

    const guests = Math.max(1, parseInt(guestsInput.value, 10) || 1);
    const extraGuests = Math.max(0, guests - includedGuests);
    const floorMultiplier = (floorSelect && floorSelect.value === 'both') ? 2 : 1;
    const subtotal = (pricePerNight * floorMultiplier * nights) + (extraGuestFee * extraGuests * nights);
    const discountApplies = discountPercent > 0 && discountMinNights > 0 && nights >= discountMinNights;
    const total = discountApplies ? subtotal * (1 - discountPercent / 100) : subtotal;

    priceSummary.style.display = 'flex';
    priceNights.textContent = nights + (nights === 1 ? ' night' : ' nights')
      + (floorMultiplier === 2 ? ' · both floors' : '')
      + (extraGuests > 0 && extraGuestFee > 0 ? ' · ' + extraGuests + ' extra guest(s)' : '')
      + (discountApplies ? ' · ' + discountPercent + '% off applied' : '');
    priceTotal.textContent = currency + ' ' + Math.round(total).toLocaleString();

    if (nights < minStay) {
      feedback.textContent = 'Minimum stay is ' + minStay + ' night(s).';
      feedback.className = 'error';
    } else if (feedback.classList.contains('error') && (feedback.textContent.indexOf('Minimum stay') === 0 || feedback.textContent.indexOf('already booked') !== -1)) {
      feedback.style.display = 'none';
      feedback.className = '';
    }
  }

  checkinInput.addEventListener('input', () => {
    checkoutInput.setAttribute('min', checkinInput.value);
    updatePriceSummary();
  });
  checkinInput.addEventListener('change', () => {
    checkoutInput.setAttribute('min', checkinInput.value);
    updatePriceSummary();
  });
  checkoutInput.addEventListener('input', updatePriceSummary);
  checkoutInput.addEventListener('change', updatePriceSummary);
  guestsInput.addEventListener('input', updatePriceSummary);
  guestsInput.addEventListener('change', updatePriceSummary);

  // Mock form submission (no backend in this static preview)
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (rangeOverlapsBooked(checkinInput.value, checkoutInput.value)) {
      feedback.textContent = 'One or more of the selected dates is already booked. Please choose different dates.';
      feedback.className = 'error';
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';

    setTimeout(() => {
      const name = document.getElementById('full_name').value || 'there';
      feedback.textContent = 'Thank you, ' + name + '! (Preview only — connect the PHP backend to save real bookings.) Your booking request has been received.';
      feedback.className = 'success';
      submitBtn.disabled = false;
      submitBtn.textContent = currentLang === 'sw' ? 'Omba Kuweka Nafasi' : 'Request to Book';
    }, 600);
  });

  // ---------- Auto-apply referral code from shared link (?ref=QH1023) ----------
  const referralInput = document.getElementById('referral_code');
  const referralFieldWrap = document.getElementById('referral-field-wrap');
  const referralAppliedNote = document.getElementById('referral-applied-note');
  const urlParams = new URLSearchParams(window.location.search);
  const refFromUrl = urlParams.get('ref');
  if (refFromUrl && referralInput) {
    referralInput.value = refFromUrl.toUpperCase();
    if (referralFieldWrap) referralFieldWrap.style.display = 'none';
    if (referralAppliedNote) referralAppliedNote.style.display = 'flex';
  }

  // ---------- Gallery floor tabs ----------
  const galleryTabs = document.querySelectorAll('.gallery-tab');
  const galleryPanels = document.querySelectorAll('[data-floor-panel]');
  galleryTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const floor = tab.getAttribute('data-floor');
      galleryTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      galleryPanels.forEach(panel => {
        panel.style.display = panel.getAttribute('data-floor-panel') === floor ? 'grid' : 'none';
      });
    });
  });

  // ---------- Star rating widget ----------
  const starButtons = document.querySelectorAll('#star-rating button');
  const ratingInput = document.getElementById('rating');
  if (starButtons.length && ratingInput) {
    starButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const val = parseInt(btn.getAttribute('data-value'), 10);
        ratingInput.value = val;
        starButtons.forEach(b => {
          b.classList.toggle('active', parseInt(b.getAttribute('data-value'), 10) <= val);
        });
      });
    });
  }

  // ---------- Review form (mock, preview only) ----------
  const reviewForm = document.getElementById('review-form');
  if (reviewForm) {
    const reviewFeedback = document.getElementById('review-feedback');
    const reviewSubmitBtn = document.getElementById('review-submit-btn');
    reviewForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (ratingInput.value === '0') {
        reviewFeedback.textContent = 'Please select a star rating.';
        reviewFeedback.className = 'error';
        return;
      }
      reviewSubmitBtn.disabled = true;
      reviewSubmitBtn.textContent = 'Sending...';
      setTimeout(() => {
        reviewFeedback.textContent = 'Thank you for your review! (Preview only — connect the PHP backend to save real reviews.)';
        reviewFeedback.className = 'success';
        reviewForm.reset();
        starButtons.forEach(b => b.classList.remove('active'));
        ratingInput.value = '0';
        reviewSubmitBtn.disabled = false;
        reviewSubmitBtn.textContent = 'Submit Review';
      }, 500);
    });
  }

  // ---------- Issue report form (mock, preview only) ----------
  const issueForm = document.getElementById('issue-form');
  if (issueForm) {
    const issueFeedback = document.getElementById('issue-feedback');
    const issueSubmitBtn = document.getElementById('issue-submit-btn');
    issueForm.addEventListener('submit', function (e) {
      e.preventDefault();
      issueSubmitBtn.disabled = true;
      issueSubmitBtn.textContent = 'Sending...';
      setTimeout(() => {
        issueFeedback.textContent = 'Thank you. Your report has been received. (Preview only — connect the PHP backend to save real reports.)';
        issueFeedback.className = 'success';
        issueForm.reset();
        issueSubmitBtn.disabled = false;
        issueSubmitBtn.textContent = 'Submit Report';
      }, 500);
    });
  }

  // ---------- Mobile nav toggle ----------
  const navToggle = document.getElementById('nav-toggle');
  const navLinks = document.querySelector('nav.links');
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
      const isOpen = navLinks.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // ---------- Language toggle ----------
  let currentLang = 'en';
  const langBtn = document.getElementById('lang-toggle');
  if (langBtn) {
    langBtn.addEventListener('click', () => {
      currentLang = currentLang === 'en' ? 'sw' : 'en';
      document.documentElement.lang = currentLang;
      document.querySelectorAll('[data-en]').forEach(el => {
        const text = currentLang === 'sw' ? el.getAttribute('data-sw') : el.getAttribute('data-en');
        if (text !== null) el.textContent = text;
      });
      document.querySelectorAll('[data-en-html]').forEach(el => {
        const html = currentLang === 'sw' ? el.getAttribute('data-sw-html') : el.getAttribute('data-en-html');
        if (html !== null) el.innerHTML = html;
      });
    });
  }
});
