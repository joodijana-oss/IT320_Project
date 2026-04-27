<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sanad — Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
  <header class="sn-nav">
    <div class="sn-container sn-nav__inner">
      <a href="index.php" class="sn-nav__logo">
        <img src="images/slogo.png" alt="Sanad Logo" class="sn-nav__logo-img" />
        <span class="sn-nav__logo-name">Sanad</span>
      </a>

      <ul class="sn-nav__links">
        <li><a href="index.php">Home</a></li>
        <li><a href="login.php">Log in</a></li>
        <li><a href="register.php" class="sn-nav--active">Register</a></li>
        <li><a href="about.php">About us</a></li>
      </ul>
    </div>
  </header>

<!-- ── Main ── -->
<main class="sn-main login-page">
  <div class="sn-container">
    <div class="login-box">


      <!-- Brand -->
      <div class="login-brand">
        <div class="login-brand__logo">
          <img src="images/slogo.png" class="login-brand__logo-img">
          <span class="login-brand__logo-name">Sanad</span>
        </div>
        <p class="login-brand__tagline">Create your account to get started</p>
      </div>



      <!-- Card -->
      <div class="login-card">
        <h1 class="login-card__title">Create account</h1>
        <p class="login-card__subtitle">Join Sanad and connect with pharmacies</p>

        <form id="register-form" method="POST" action="register.php" novalidate>

          <!-- Full Name -->
          <div class="login-field">
            <label>Full name</label>
            <input type="text"  name="full_name" placeholder="Your full name">
            <span class="error-msg" id="name-error"></span>
          </div>

          <div class="login-field">
              <label>Date of Birth</label>
              <input type="date" name="dob" id="dob">
              <span class="error-msg" id="dob-error"></span>
          </div>

          <!-- Email -->
          <div class="login-field">
              <label>Email address</label>
              <input type="email" name="email" placeholder="you@example.com">
              <span class="error-msg" id="email-error"></span>
          </div>

          <!-- Phone -->
          <div class="login-field">
              <label>Phone number</label>
              <input type="tel" name="phone_number" placeholder="05xxxxxxxx">
              <span class="error-msg" id="phone-error"></span>
          </div>

          <!-- Location -->
          <div class="login-field">
            <label>Zone</label>
            <select name="zone" id="zone" class="login-select">
              <option value="">Select zone</option>
              <option>North Riyadh</option>
              <option>South Riyadh</option>
              <option>East Riyadh</option>
              <option>West Riyadh</option>
            </select>
            <span class="error-msg" id="region-error"></span>
          </div>

          <!-- Password -->
          <div class="login-field">
            <label>Password</label>
            <div class="login-pw-wrap">
              <input type="password" name="password" placeholder="Create password">
              <span class="error-msg" id="password-error"></span>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="login-field">
            <label>Confirm password</label>
            <div class="login-pw-wrap">
              <input type="password"  name="confirm_password" placeholder="Confirm password">
              <span class="error-msg" id="confirm-error"></span>
            </div>
          </div>

          <!-- Terms -->
          <div class="login-field" style="flex-direction:column; align-items:flex-start; gap:5px;">
            <div style="display:flex; align-items:center; gap:8px;">
              <input type="checkbox" id="terms">
              <label for="terms" style="margin:0;">I agree to Terms & Privacy</label>
            </div>
            <span class="error-msg" id="terms-error"></span>
          </div>

          <!-- Submit -->
          <button class="login-submit-btn">
            Create account
          </button>

        </form>

        <p class="login-register-link">
          Already have an account? <a href="login.php">Sign in</a>
        </p>
      </div>

    </div>
  </div>
</main>

<!-- ── Footer ── -->
<footer class="sn-footer">
  <div class="sn-container">
    <div class="sn-footer__inner">
        <span class="sn-footer__logo-name">Sanad</span>
      <span class="sn-footer__copy">© 2026 Sanad. Riyadh, Saudi Arabia.</span>
    </div>
  </div>
</footer>

<script src="valdiation.js"></script>
</body>
</html>