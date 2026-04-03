/* ============================================================
   pharmacy.js — Member 4 scripts
   Covers: pharmacy-reports, pharmacy-offers, pharmacy-request-details
   ============================================================ */


/* ─────────────────────────────────────────────────────────────
   SHARED UTILITY
───────────────────────────────────────────────────────────── */

function phToast(msg, cls) {
  const wrap = document.getElementById('toasts');
  if (!wrap) return;
  const t = document.createElement('div');
  t.className = 'ph-toast' + (cls ? ' ' + cls : '');
  t.textContent = msg;
  wrap.appendChild(t);
  setTimeout(() => t.remove(), 4000);
}


/* ─────────────────────────────────────────────────────────────
   PHARMACY REPORTS
   Runs only when #actChart exists on the page.
───────────────────────────────────────────────────────────── */

(function initReports() {
  if (!document.getElementById('actChart')) return;

  const C_NAVY = '#1f2f46';
  const C_ACC  = '#4a7fa5';
  const C_GRN  = '#216b43';
  const C_AMB  = '#b07a10';
  const C_RED  = '#8b2020';
  const C_LITE = '#e8f0f7';

  const datasets = {
    '7d':  { offers:3,  confirmed:2,  rejected:1,  rate:'67%',
              dO:'↑ 10%', dC:'↑ 8%',  dR:'↑ 5%',  dRj:'↓ 2%',
              act:{ lbl:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'], sub:[1,0,1,0,0,1,0], con:[0,0,1,0,0,1,0] },
              outcomes:[2,1,0] },
    '30d': { offers:11, confirmed:6,  rejected:3,  rate:'54%',
              dO:'↑ 22%', dC:'↑ 15%', dR:'— Same', dRj:'↓ 5%',
              act:{ lbl:['W1','W2','W3','W4'], sub:[3,4,2,2], con:[2,2,1,1] },
              outcomes:[6,3,2] },
    '90d': { offers:28, confirmed:16, rejected:8,  rate:'57%',
              dO:'↑ 35%', dC:'↑ 30%', dR:'↑ 8%',  dRj:'↓ 10%',
              act:{ lbl:['Jan','Feb','Mar'], sub:[8,10,10], con:[4,6,6] },
              outcomes:[16,8,4] },
    'all': { offers:54, confirmed:31, rejected:14, rate:'57%',
              dO:'—', dC:'—', dR:'—', dRj:'—',
              act:{ lbl:["Q1 '24","Q2 '24","Q3 '24","Q4 '24","Q1 '25"], sub:[8,12,14,11,9], con:[4,7,8,7,5] },
              outcomes:[31,14,9] }
  };

  let cAct, cDonut;
  const baseOpts  = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };
  const tickFont  = { family: 'Outfit', size: 11 };
  const tickColor = '#6b7280';

  function buildCharts(d) {
    if (cAct)   cAct.destroy();
    if (cDonut) cDonut.destroy();

    cAct = new Chart(document.getElementById('actChart'), {
      type: 'bar',
      data: {
        labels: d.act.lbl,
        datasets: [
          { label:'Submitted', data:d.act.sub, backgroundColor:C_LITE, borderRadius:6, borderSkipped:false, barPercentage:.5, categoryPercentage:.6 },
          { label:'Confirmed', data:d.act.con, backgroundColor:C_GRN,  borderRadius:6, borderSkipped:false, barPercentage:.5, categoryPercentage:.6 }
        ]
      },
      options: { ...baseOpts,
        plugins: { legend:{ display:true, labels:{ usePointStyle:true, font:tickFont, color:tickColor, padding:16 } } },
        scales: {
          x: { grid:{ display:false }, ticks:{ color:tickColor, font:tickFont } },
          y: { grid:{ color:'#ede9e0' }, ticks:{ color:tickColor, font:tickFont, stepSize:1 }, beginAtZero:true, border:{ display:false } }
        }
      }
    });

    cDonut = new Chart(document.getElementById('donutChart'), {
      type: 'doughnut',
      data: {
        labels: ['Confirmed', 'Not selected', 'Pending'],
        datasets: [{ data:d.outcomes, backgroundColor:[C_GRN,C_RED,C_AMB], borderWidth:0, hoverOffset:6 }]
      },
      options: { ...baseOpts, cutout:'64%',
        plugins:{ legend:{ display:true, position:'bottom', labels:{ usePointStyle:true, padding:14, font:tickFont, color:tickColor } } }
      }
    });
  }

  window.load = function(period, btn) {
    document.querySelectorAll('.ph-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');

    const d = datasets[period];
    if (!d) {
      document.getElementById('chartsSection').style.display = 'none';
      document.getElementById('noData').style.display = 'block';
      return;
    }
    document.getElementById('chartsSection').style.display = '';
    document.getElementById('noData').style.display = 'none';

    document.getElementById('mOffers').textContent    = d.offers;
    document.getElementById('mConfirmed').textContent = d.confirmed;
    document.getElementById('mRejected').textContent  = d.rejected;
    document.getElementById('mRate').textContent      = d.rate;

    const isAllTime = period === 'all';
    const setDelta = (id, txt, cls) => {
      const el = document.getElementById(id);
      el.textContent = isAllTime ? '— All time total' : txt + ' vs prev period';
      el.className   = 'ph-stat-card__delta ' + (isAllTime ? 'delta-neu' : cls);
    };
    setDelta('dOffers',    d.dO,  d.dO.startsWith('↑')  ? 'delta-up' : 'delta-neu');
    setDelta('dConfirmed', d.dC,  d.dC.startsWith('↑')  ? 'delta-up' : 'delta-neu');
    setDelta('dRejected',  d.dRj, d.dRj.startsWith('↓') ? 'delta-up' : 'delta-down');
    setDelta('dRate',      d.dR,  d.dR.startsWith('↑')  ? 'delta-up' : d.dR.startsWith('↓') ? 'delta-down' : 'delta-neu');

    buildCharts(d);
  };

  buildCharts(datasets['30d']);
})();


/* ─────────────────────────────────────────────────────────────
   PHARMACY OFFERS
   Runs only when #offersBody exists on the page.
───────────────────────────────────────────────────────────── */

(function initOffers() {
  if (!document.getElementById('offersBody')) return;

  window.filterOffers = function() {
    const s = document.getElementById('searchInput').value.toLowerCase();
    const rows = [...document.querySelectorAll('#offersBody tr[data-med]')];
    let count = 0;
    rows.forEach(r => {
      const show = !s || r.dataset.med.includes(s) || r.dataset.id.includes(s);
      r.style.display = show ? '' : 'none';
      if (show) count++;
    });
    document.getElementById('emptyState').style.display = count ? 'none' : 'block';
    document.getElementById('countLabel').innerHTML =
      `Showing <strong>${count}</strong> offer${count !== 1 ? 's' : ''}`;
  };
})();


/* ─────────────────────────────────────────────────────────────
   PHARMACY REQUEST DETAILS
   Runs only when #offerForm exists on the page.
───────────────────────────────────────────────────────────── */

(function initRequestDetails() {
  if (!document.getElementById('offerForm')) return;

  window.submitOffer = function() {
    const price = parseFloat(document.getElementById('priceInput').value);
    if (!price || price <= 0) {
      phToast('Please enter a valid price.', 'ph-toast--err');
      return;
    }
    document.getElementById('sentPrice').textContent = `﷼ ${price.toFixed(2)}`;
    document.getElementById('offerForm').style.display = 'none';
    document.getElementById('offerSent').style.display = '';
    phToast('Offer submitted successfully.', 'ph-toast--ok');
  };
})();
