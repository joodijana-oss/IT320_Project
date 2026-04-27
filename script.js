/* ============================================================
   script.js — shared JavaScript for all Member 3 pages
   PHP phase: remove login submit handler and add
   method="POST" action="login.php" to the login form.
   ============================================================ */


/* ── LOGIN PAGE ──────────────────────────────────────────── */
(function () {

    var pwInput = document.getElementById('login-password');
    var pwToggle = document.getElementById('login-pw-toggle');
    var form = document.getElementById('login-form');

    /* Only run if we are on the login page */
    if (!form)
        return;

    /* ── Show / hide password ── */
    pwToggle.addEventListener('click', function () {
        var isPassword = pwInput.type === 'password';
        pwInput.type = isPassword ? 'text' : 'password';
        document.getElementById('icon-eye-show').style.display = isPassword ? 'none' : 'block';
        document.getElementById('icon-eye-hide').style.display = isPassword ? 'block' : 'none';
    });

    /* ── Clear role error when user selects ── */
    document.getElementById('login-role').addEventListener('change', function () {
        this.style.borderColor = '';
        document.getElementById('login-role-error').style.display = 'none';
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

    /* ── On submit: validate role, email and password then redirect ── */
    /* PHP phase: remove this handler and add method="POST" action="login.php" to the form */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var roleInput = document.getElementById('login-role');
        var roleError = document.getElementById('login-role-error');
        var emailInput = document.getElementById('login-email');
        var emailError = document.getElementById('login-email-error');
        var passwordError = document.getElementById('login-password-error');
        var role = roleInput.value;
        var email = emailInput.value.trim();
        var password = pwInput.value.trim();
        var valid = true;

        roleInput.style.borderColor = '';
        emailInput.style.borderColor = '';
        pwInput.style.borderColor = '';
        roleError.style.display = 'none';
        emailError.style.display = 'none';
        passwordError.style.display = 'none';

        if (!role) {
            roleInput.style.borderColor = '#8b2020';
            roleError.style.display = 'block';
            valid = false;
        }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailInput.style.borderColor = '#8b2020';
            emailError.style.display = 'block';
            valid = false;
        }
        if (!password) {
            pwInput.style.borderColor = '#8b2020';
            passwordError.style.display = 'block';
            valid = false;
        }
        if (!valid)
            return;

        // Show spinner
        document.getElementById('login-submit-btn').disabled = true;
        document.querySelector('.login-btn-text').textContent = 'Signing in…';

        var formData = new FormData();
        formData.append('role', role);
        formData.append('email', email);
        formData.append('password', password);

        fetch('login_process.php', {method: 'POST', body: formData})
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        var box = document.getElementById('success-box');
                        box.textContent = data.message;
                        box.style.display = 'block';
                        box.style.background = '#fde8e8';
                        box.style.color = '#8b2020';
                        box.style.border = '1px solid #f5c2c2';
                        document.getElementById('login-submit-btn').disabled = false;
                        document.querySelector('.login-btn-text').textContent = 'Sign in';
                    }
                })
                .catch(function () {
                    alert('Something went wrong. Please try again.');
                    document.getElementById('login-submit-btn').disabled = false;
                    document.querySelector('.login-btn-text').textContent = 'Sign in';
                });
    });

})();


/* ── PROFILE & MY REQUESTS PAGES ─────────────────────────── */
(function () {

    var logoutLink = document.getElementById('logout-link');

    /* Only run if we are on a page that has the logout link */
    if (!logoutLink)
        return;

    logoutLink.addEventListener('click', function (e) {
        e.preventDefault();
        window.location.href = 'logout.php';
    });

})();


/* ── MY REQUESTS PAGE ────────────────────────────────────── */
/* PHP phase: remove this entire section — delete is handled
 server-side by posting to delete_request.php             */
(function () {

    var deleteModal = document.getElementById('delete-modal');

    /* Only run if we are on the my-requests page */
    if (!deleteModal)
        return;

    var pendingDeleteId = null;
    var pendingDeleteName = null;

    function openDeleteModal(requestId, medName) {
        pendingDeleteId = requestId;
        pendingDeleteName = medName;
        document.getElementById('modal-med-name').textContent = medName;
        deleteModal.classList.add('req-modal-overlay--open');
    }

    function closeDeleteModal() {
        pendingDeleteId = null;
        pendingDeleteName = null;
        deleteModal.classList.remove('req-modal-overlay--open');
    }

    function confirmDelete() {
        if (!pendingDeleteId)
            return;

        /* PHP phase: POST to delete_request.php with request_id */
        var card = document.querySelector('[data-id="' + pendingDeleteId + '"]');
        if (card) {
            card.style.transition = 'opacity 0.2s, transform 0.2s';
            card.style.opacity = '0';
            card.style.transform = 'translateY(-6px)';
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

    /* Close modal when clicking the overlay background */
    deleteModal.addEventListener('click', function (e) {
        if (e.target === this)
            closeDeleteModal();
    });

    /* Expose functions used by onclick in HTML */
    window.openDeleteModal = openDeleteModal;
    window.closeDeleteModal = closeDeleteModal;
    window.confirmDelete = confirmDelete;

})();


/* ── SUCCESS MESSAGE ─────────────────────────────────────── */
window.addEventListener('load', function () {
    var msg = localStorage.getItem('successMessage');
    if (msg) {
    var box = document.getElementById('success-box');
    if (box) {
      box.textContent   = msg;
      box.style.display = 'block';
    }
    localStorage.removeItem('successMessage');
  }
});

window.addEventListener('DOMContentLoaded', function () {
  var message = localStorage.getItem('requestSuccess');
  if (message) {
    var box = document.getElementById('success-box');
    if (box) {
      box.textContent   = message;
      box.style.display = 'block';
      setTimeout(function () { box.style.display = 'none'; }, 10000);
    }
    localStorage.removeItem('requestSuccess');
  }
});


/* ── DELETED REQUEST ─────────────────────────────────────── */
window.addEventListener('DOMContentLoaded', function () {
  var deletedId = localStorage.getItem('deletedRequestId');
  if (deletedId) {
    var card = document.querySelector('[data-id="' + deletedId + '"]');
    if (card) card.remove();
    localStorage.removeItem('deletedRequestId');
  }
});