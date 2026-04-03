const form = document.getElementById("register-form");

form.addEventListener("submit", function (e) {
  e.preventDefault();

  // inputs
  const name = form.querySelector("input[type='text']");
  const email = form.querySelector("input[type='email']");
  const phone = form.querySelector("input[type='tel']");
  const region = document.getElementById("region");
  const password = form.querySelectorAll("input[type='password']")[0];
  const confirmPassword = form.querySelectorAll("input[type='password']")[1];
  const checkbox = form.querySelector("input[type='checkbox']");

  // errors
  let valid = true;

  function setError(id, msg) {
    document.getElementById(id).textContent = msg;
    valid = false;
  }

  function clearErrors() {
    document.querySelectorAll(".error-msg").forEach(e => e.textContent = "");
  }

  clearErrors();

  // Name
  if (name.value.trim().length < 3) {
    setError("name-error", "Enter a valid full name.");
  }

  // Email
  if (!email.value.includes("@")) {
    setError("email-error", "Invalid email.");
  }

  // Phone (Saudi format)
  if (!/^05\d{8}$/.test(phone.value)) {
    setError("phone-error", "Phone must start with 05 and be 10 digits.");
  }

  // Region
  if (region.value === "") {
    setError("region-error", "Please select a region.");
  }

  // Password
  if (password.value.length < 6) {
    setError("password-error", "Password must be at least 6 characters.");
  }

  // Confirm password
  if (password.value !== confirmPassword.value) {
    setError("confirm-error", "Passwords do not match.");
  }

  // Terms
  if (!checkbox.checked) {
    alert("You must agree to terms.");
    valid = false;
  }

  if (valid) {
    alert("Account created successfully 🎉");
    form.reset();
  }
});


document.getElementById("requestForm").addEventListener("submit", function(e) {
  let valid = true;

  const med = document.getElementById("medication");
  const priority = document.getElementById("priority");
  const file = document.getElementById("file");

  clearErrors();

  if (med.value.trim() === "") {
    showError(med, "Medication required");
    valid = false;
  }

  if (priority.value === "") {
    showError(priority, "Select priority");
    valid = false;
  }

  const zone = document.getElementById("zone");

if (zone.value === "") {
  showError(zone, "Select your zone");
  valid = false;
}

  if (file.files.length === 0) {
    showError(file, "Upload prescription");
    valid = false;
  }

  if (!valid) e.preventDefault();
});

function showError(input, message) {
  const error = input.nextElementSibling;
  error.textContent = message;
}

function clearErrors() {
  document.querySelectorAll(".error-msg").forEach(e => e.textContent = "");
}


// SAVE CHANGES
document.getElementById("editRequestForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const medication = document.getElementById("medication");
  const priority = document.getElementById("priority");
  const zone = document.getElementById("zone");
  const notes = document.getElementById("notes");
  const file = document.getElementById("file");

  if (
    medication.value.trim() === "" ||
    notes.value.trim() === "" ||
    file.files.length === 0
  ) {
    alert("Please fill all required fields!");
    return;
  }

  alert("Changes saved successfully!");
});

// DELETE
function deleteRequest() {
  if (confirm("Are you sure you want to delete this request?")) {
    alert("Request deleted");
    window.location.href = "myRequests.html";
  }
}

function enableEdit(icon) {
  const box = icon.parentElement;
  const input = box.querySelector("input, select, textarea");

  if (input.tagName === "SELECT") {
    input.disabled = false;
  } else {
    input.readOnly = false;
  }

  input.focus();
}

document.getElementById("file").addEventListener("change", function() {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();

    reader.onload = function(e) {
      document.getElementById("previewImage").src = e.target.result;
    };

    reader.readAsDataURL(file);
  }
});

function enableEdit(icon) {
  const box = icon.parentElement;
  const input = box.querySelector("input, select, textarea");

  if (!input) return;

  input.removeAttribute("readonly");
  input.removeAttribute("disabled");

  input.focus();
}