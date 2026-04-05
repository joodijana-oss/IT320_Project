

 
/* ── LOGIN PAGE ──────────────────────────────────────────── */
(function () {
 
  var pwInput  = document.getElementById('login-password');
  var pwToggle = document.getElementById('login-pw-toggle');
  var form     = document.getElementById('login-form');
 
  if (!form) return;
 
  /* ── Show / hide password ── */
  pwToggle.addEventListener('click', function () {
    var isPassword = pwInput.type === 'password';
    pwInput.type = isPassword ? 'text' : 'password';
    document.getElementById('icon-eye-show').style.display = isPassword ? 'none' : 'block';
    document.getElementById('icon-eye-hide').style.display = isPassword ? 'block' : 'none';
  });
 
  /* ── Clear email error when user starts retyping ── */
  document.getElementById('login-email').addEventListener('input', function () {
    this.style.borderColor = '';
    document.getElementById('login-email-error').style.display = 'none';
  });
 
  /* ── Clear password error when user starts retyping ── */
  pwInput.addEventListener('input', function () {
    this.style.borderColor = '';
    document.getElementById('login-password-error').style.display = 'none';
  });
 
  /* ── On submit: validate email and password then redirect ── */
  form.addEventListener('submit', function (e) {
    e.preventDefault();
 
    var emailInput    = document.getElementById('login-email');
    var emailError    = document.getElementById('login-email-error');
    var passwordError = document.getElementById('login-password-error');
    var email         = emailInput.value.trim();
    var password      = pwInput.value.trim();
    var valid         = true;
 
    /* Reset errors */
    emailInput.style.borderColor = '';
    pwInput.style.borderColor    = '';
    emailError.style.display     = 'none';
    passwordError.style.display  = 'none';
 
    /* Validate email format */
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailInput.style.borderColor = '#8b2020';
      emailError.style.display     = 'block';
      valid = false;
    }
 
    /* Validate password not empty */
    if (!password) {
      pwInput.style.borderColor   = '#8b2020';
      passwordError.style.display = 'block';
      valid = false;
    }
 
    if (!valid) return;
 
    window.location.href = 'user-dashboard.html';
  });
 
})();


/* ── PROFILE & MY REQUESTS PAGES ─────────────────────────── */
(function () {

  var logoutLink = document.getElementById('logout-link');

  /* Only run if we are on a page that has the logout link */
  if (!logoutLink) return;

  logoutLink.addEventListener('click', function (e) {
    e.preventDefault();
    window.location.href = 'login.html';
  });

})();


/* ── MY REQUESTS PAGE ────────────────────────────────────── */
(function () {

  var deleteModal = document.getElementById('delete-modal');

  if (!deleteModal) return;

  var pendingDeleteId   = null;
  var pendingDeleteName = null;

  function openDeleteModal(requestId, medName) {
    pendingDeleteId   = requestId;
    pendingDeleteName = medName;
    document.getElementById('modal-med-name').textContent = medName;
    deleteModal.classList.add('req-modal-overlay--open');
  }

  function closeDeleteModal() {
    pendingDeleteId   = null;
    pendingDeleteName = null;
    deleteModal.classList.remove('req-modal-overlay--open');
  }

  function confirmDelete() {
    if (!pendingDeleteId) return;

    var card = document.querySelector('[data-id="' + pendingDeleteId + '"]');
    if (card) {
      card.style.transition = 'opacity 0.2s, transform 0.2s';
      card.style.opacity    = '0';
      card.style.transform  = 'translateY(-6px)';
      setTimeout(function () {
        card.remove();
        /* Show empty state if no cards remain */
        var remaining = document.querySelectorAll('.req-card');
        if (remaining.length === 0) {
          document.getElementById('empty-state').style.display = 'block';
        }
      }, 200);
    }
    closeDeleteModal();
  }
  deleteModal.addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
  });

  window.openDeleteModal  = openDeleteModal;
  window.closeDeleteModal = closeDeleteModal;
  window.confirmDelete    = confirmDelete;

})();

window.addEventListener("load", function () {
  const msg = localStorage.getItem("successMessage");

  if (msg) {
    const box = document.getElementById("success-box");
    box.textContent = msg;
    box.style.display = "block";

    localStorage.removeItem("successMessage");
  }
});

window.addEventListener("DOMContentLoaded", () => {
  const message = localStorage.getItem("requestSuccess");

  if (message) {
    const box = document.getElementById("success-box");
    box.textContent = message;
    box.style.display = "block";

    // نحذف الرسالة بعد عرضها
    localStorage.removeItem("requestSuccess");

    //  تختفي بعد 4 ثواني
    setTimeout(() => {
      box.style.display = "none";
    }, 10000);
  }
});

window.addEventListener("DOMContentLoaded", () => {
  const deletedId = localStorage.getItem("deletedRequestId");

  if (deletedId) {
    const card = document.querySelector(`[data-id="${deletedId}"]`);
    if (card) {
      card.remove();
    }

    localStorage.removeItem("deletedRequestId");
  }
});