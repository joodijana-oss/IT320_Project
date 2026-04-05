
const form = document.getElementById("register-form");

if (form) {
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const name = form.querySelector("input[type='text']");
    const email = form.querySelector("input[type='email']");
    const phone = form.querySelector("input[type='tel']");
    const dob = document.getElementById("dob");
    const today = new Date().toISOString().split("T")[0];
   dob.max = today;
    const region = document.getElementById("zone");
    const password = form.querySelectorAll("input[type='password']")[0];
    const confirmPassword = form.querySelectorAll("input[type='password']")[1];
    const checkbox = document.getElementById("terms");

    
    let valid = true;

    function setError(id, msg) {
      document.getElementById(id).textContent = msg;
      valid = false;
    }

    function clearErrors() {
      document.querySelectorAll(".error-msg").forEach(e => e.textContent = "");
    }

    clearErrors();

    // DOB validation
if (!dob.value) {
  setError("dob-error", "Please select your date of birth.");
} else {
  const birthDate = new Date(dob.value);
  const today = new Date();

  let age = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();

  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }

  if (age < 18) {
    setError("dob-error", "You must be at least 18 years old.");
  }
}
    if (name.value.trim().length < 3) {
      setError("name-error", "Enter a valid full name.");
    }

    if (!email.value.includes("@")) {
      setError("email-error", "Invalid email.");
    }

    if (!/^05\d{8}$/.test(phone.value)) {
      setError("phone-error", "Phone must start with 05 and be 10 digits.");
    }

    if (region.value === "") {
      setError("region-error", "Please select a region.");
    }

    if (password.value.length < 6) {
      setError("password-error", "Password must be at least 6 characters.");
    }

    if (password.value !== confirmPassword.value) {
      setError("confirm-error", "Passwords do not match.");
    }

    if (!checkbox.checked) {
      setError("terms-error", "You must agree to terms.");
    }

    if (valid) {
      localStorage.setItem("successMessage", "Account created successfully 🎉");
      window.location.href = "login.html";
    }
  });
}


const requestForm = document.getElementById("requestForm");

if (requestForm) {
  requestForm.addEventListener("submit", function(e) {
    e.preventDefault();

    let valid = true;

    const med = document.getElementById("medication");
    const priority = document.getElementById("priority");
    const file = document.getElementById("file");
    const zone = document.getElementById("zone");

    clearErrors();

    if (med.value.trim() === "") {
      showError(med, "Medication required");
      valid = false;
    }

    if (priority.value === "") {
      showError(priority, "Select priority");
      valid = false;
    }

    if (zone.value === "") {
      showError(zone, "Select your zone");
      valid = false;
    }

    if (file.files.length === 0) {
      showError(file, "Upload prescription");
      valid = false;
    }

    if (valid) {
      localStorage.setItem("requestSuccess", "Request submitted successfully ✅");
      window.location.href = "my-requests.html";
    }
  });
}

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
  const notes = document.getElementById("notes");
  const file = document.getElementById("file");
  const previewImage = document.getElementById("previewImage");

  if (
    medication.value.trim() === "" ||
    notes.value.trim() === "" ||
    (!file.files.length && !previewImage.src)
  ) {
    showCustomBox("Please fill all required fields!");
    return;
  }

  showModal("Success", "Changes saved successfully!", function () {
    window.location.href = "my-requests.html";
  });
});

// DELETE
function deleteRequest() {
  showModal(
    "Delete this request?",
    "This action cannot be undone.",
    function () {

      localStorage.setItem("deletedRequestId", "1001");

  localStorage.setItem("requestSuccess", "Request deleted successfully 🗑️");

      window.location.href = "my-requests.html";
    }
  );
  

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


// ===== CUSTOM MODAL (بدون HTML) =====
function showModal(title, message, onConfirm) {
  // الخلفية
  const overlay = document.createElement("div");
  overlay.style.position = "fixed";
  overlay.style.top = 0;
  overlay.style.left = 0;
  overlay.style.width = "100%";
  overlay.style.height = "100%";
  overlay.style.background = "rgba(0,0,0,0.4)";
  overlay.style.backdropFilter = "blur(6px)";
  overlay.style.webkitBackdropFilter = "blur(6px)";
    overlay.style.display = "flex";
  overlay.style.alignItems = "center";
  overlay.style.justifyContent = "center";
  overlay.style.zIndex = 9999;

  // البوكس
  const box = document.createElement("div");
  box.style.background = "#fff";
  box.style.padding = "20px";
  box.style.borderRadius = "12px";
  box.style.width = "320px";
  box.style.textAlign = "center";
  box.style.boxShadow = "0 10px 25px rgba(0,0,0,0.2)";

  box.innerHTML = `
    <h3 style="margin-bottom:10px;">${title}</h3>
    <p style="margin-bottom:20px;">${message}</p>
    <button id="modal-ok" style="
      padding:8px 16px;
      background:#8B0000;
      color:white;
      border:none;
      border-radius:6px;
      cursor:pointer;
    ">OK</button>
  `;

  overlay.appendChild(box);
  document.body.appendChild(overlay);

  document.getElementById("modal-ok").onclick = () => {
    document.body.removeChild(overlay);
    if (onConfirm) onConfirm();
  };
}