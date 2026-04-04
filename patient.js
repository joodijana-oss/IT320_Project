let locked = false;

function toast(msg, cls) {
  const t = document.createElement('div');
  t.className = 'sn-toast' + (cls ? ' ' + cls : '');
  t.textContent = msg;
  document.getElementById('toasts').appendChild(t);
  setTimeout(() => t.remove(), 4000);
}

function accept(id) {
  if (locked) { toast('You have already accepted an offer.', 'sn-toast--err'); return; }
  locked = true;

  document.getElementById('oc-' + id).classList.add('offer-card--accepted');
  document.getElementById('ob-' + id).innerHTML = '<span class="offer-accepted-label">✓ Accepted</span>';
  document.getElementById('btns-' + id).remove();

  [1, 2, 3].forEach(i => {
    if (i === id) return;
    const c = document.getElementById('oc-' + i);
    if (c && c.dataset.status !== 'dismissed') {
      c.classList.add('offer-card--dismissed');
      const btns = document.getElementById('btns-' + i);
      if (btns) btns.querySelectorAll('button').forEach(b => {
        b.disabled = true;
        b.style.opacity = '0.4';
        b.style.cursor = 'not-allowed';
      });
    }
  });

  document.getElementById('confirmedBar').style.display = 'block';
  toast('Offer accepted. Request is now Confirmed.', 'sn-toast--ok');
}

function dismiss(id) {
  const c = document.getElementById('oc-' + id);
  c.classList.add('offer-card--dismissed');
  c.dataset.status = 'dismissed';
  document.getElementById('ob-' + id).innerHTML =
    '<span class="request-card__status request-card__status--rejected">Dismissed</span>';
  document.getElementById('btns-' + id).remove();
  toast('Offer dismissed.', '');

  const open = [...document.querySelectorAll('[id^="oc-"]')]
    .filter(x => x.dataset.status === 'open');
  if (!open.length && !locked) {
    document.getElementById('emptyState').style.display = 'block';
  }
}

function openMap(name, addr, phone, mapsUrl, embedUrl) {
  document.getElementById('mapTitle').textContent = name;
  document.getElementById('mName').textContent    = name;
  document.getElementById('mAddr').textContent    = addr;
  document.getElementById('mPhone').textContent   = phone;
  document.getElementById('mapsLink').href        = mapsUrl;
  document.getElementById('mapFrame').src         = embedUrl;
  document.getElementById('mapOverlay').classList.add('open');
}

function closeMap(e) {
  if (!e || e.target === document.getElementById('mapOverlay')) {
    document.getElementById('mapOverlay').classList.remove('open');
    document.getElementById('mapFrame').src = '';
  }
}