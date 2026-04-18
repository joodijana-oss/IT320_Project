document.addEventListener("DOMContentLoaded", function () {
  loadReviewRequests();
  loadRequestDetails();
  loadManageUsers();
  loadAdminDashboard();
});

/* =========================
   REVIEW REQUESTS PAGE
   ========================= */
function loadReviewRequests() {
  const requestList = document.getElementById("requestList");
  if (!requestList) return;

  fetch("get_review_requests.php")
    .then(res => res.json())
    .then(data => {
      requestList.innerHTML = "";

      data.forEach(request => {
        const statusClass =
          request.request_status.toLowerCase() === "pending"
            ? "request-card__status--pending"
            : request.request_status.toLowerCase() === "approved"
            ? "request-card__status--approved"
            : "request-card__status--rejected";

        const btnClass =
          request.request_status.toLowerCase() === "pending"
            ? "admin-btn--primary"
            : "admin-btn--secondary";

        const card = document.createElement("article");
        card.className = "request-card";
        card.setAttribute("data-status", request.request_status.toLowerCase());
        card.setAttribute("data-priority", request.priority_level.toLowerCase());
        card.setAttribute("data-medication", request.medication_name.toLowerCase());
        card.setAttribute("data-id", request.request_id);

        card.innerHTML = `
          <div class="request-card__top">
            <div>
              <span class="request-card__id">Request #${request.request_id}</span>
              <h2 class="request-card__title">${request.medication_name}</h2>
            </div>
            <span class="request-card__status ${statusClass}">${request.request_status}</span>
          </div>

          <div class="request-card__meta">
            <span><strong>Patient:</strong> ${request.patient_name}</span>
            <span><strong>Priority:</strong> ${request.priority_level}</span>
            <span><strong>Submitted:</strong> ${formatDate(request.request_date)}</span>
          </div>

          <p class="request-card__text">
            ${request.notes ? request.notes : "No note provided."}
          </p>

          <div class="request-card__actions">
            <a href="request-details.html?id=${request.request_id}" class="admin-btn ${btnClass}">
              View Details
            </a>
          </div>
        `;

        requestList.appendChild(card);
      });

      setupReviewFilters();
    });
}

function setupReviewFilters() {
  const statusFilter = document.getElementById("statusFilter");
  const priorityFilter = document.getElementById("priorityFilter");
  const searchInput = document.getElementById("searchRequest");
  const noResultsMessage = document.getElementById("noResultsMessage");

  if (!statusFilter || !priorityFilter || !searchInput) return;

  function filterRequests() {
    const cards = document.querySelectorAll(".request-card");
    const selectedStatus = statusFilter.value.toLowerCase();
    const selectedPriority = priorityFilter.value.toLowerCase();
    const searchValue = searchInput.value.trim().toLowerCase();

    let visibleCount = 0;

    cards.forEach(card => {
      const cardStatus = (card.dataset.status || "").toLowerCase();
      const cardPriority = (card.dataset.priority || "").toLowerCase();
      const cardMedication = (card.dataset.medication || "").toLowerCase();
      const cardId = (card.dataset.id || "").toLowerCase();

      const matchesStatus = selectedStatus === "all" || cardStatus === selectedStatus;
      const matchesPriority = selectedPriority === "all" || cardPriority === selectedPriority;
      const matchesSearch =
        searchValue === "" ||
        cardMedication.includes(searchValue) ||
        cardId.includes(searchValue);

      if (matchesStatus && matchesPriority && matchesSearch) {
        card.style.display = "";
        visibleCount++;
      } else {
        card.style.display = "none";
      }
    });

    if (noResultsMessage) {
      noResultsMessage.style.display = visibleCount === 0 ? "block" : "none";
    }
  }

  statusFilter.addEventListener("change", filterRequests);
  priorityFilter.addEventListener("change", filterRequests);
  searchInput.addEventListener("input", filterRequests);

  filterRequests();
}

/* =========================
   REQUEST DETAILS PAGE
   ========================= */
function loadRequestDetails() {
  const titleEl = document.querySelector(".admin-page-head__title");
  if (!titleEl || !window.location.search.includes("id=")) return;

  const params = new URLSearchParams(window.location.search);
  const requestId = params.get("id");

  fetch("get_request_details.php?id=" + requestId)
    .then(res => res.json())
    .then(data => {
      if (data.error) return;

      document.querySelector(".admin-page-head__title").textContent =
        "Medication Request #" + data.request_id;

      document.querySelector(".ad-details-grid").innerHTML = `
        <div class="ad-details-item">
          <span class="ad-details-item__label">Medication Name</span>
          <span class="ad-details-item__value">${data.medication_name}</span>
        </div>

        <div class="ad-details-item">
          <span class="ad-details-item__label">Priority</span>
          <span class="ad-details-item__value">${data.priority_level}</span>
        </div>

        <div class="ad-details-item">
          <span class="ad-details-item__label">Request Date</span>
          <span class="ad-details-item__value">${formatDate(data.request_date)}</span>
        </div>

        <div class="ad-details-item">
          <span class="ad-details-item__label">Patient Name</span>
          <span class="ad-details-item__value">${data.patient_name}</span>
        </div>

        <div class="ad-details-item">
          <span class="ad-details-item__label">City</span>
          <span class="ad-details-item__value">${data.city}</span>
        </div>

        <div class="ad-details-item">
          <span class="ad-details-item__label">Zone</span>
          <span class="ad-details-item__value">${data.zone}</span>
        </div>

        <div class="ad-details-item ad-details-item--full">
          <span class="ad-details-item__label">Submitted Note</span>
          <span class="ad-details-item__value">${data.notes ? data.notes : "No note provided."}</span>
        </div>
      `;

      document.querySelector(".ad-prescription-box__info h4").textContent = data.prescription_file;
      document.querySelector(".ad-prescription-box__info p").textContent = "Uploaded " + formatDate(data.request_date);
      document.querySelector(".ad-prescription-box__info a").setAttribute("href", "images/" + data.prescription_file);

      const approveBtn = document.querySelector(".admin-btn--approve");
      const rejectBtn = document.querySelector(".admin-btn--reject");

      approveBtn.onclick = function () {
        submitDecision(requestId, "Approved");
      };

      rejectBtn.onclick = function () {
        submitDecision(requestId, "Rejected");
      };
    });
}

function submitDecision(requestId, status) {
  const reasonInput = document.getElementById("reviewReason");
  const reason = reasonInput ? reasonInput.value.trim() : "";

  const formData = new FormData();
  formData.append("request_id", requestId);
  formData.append("status", status);
  formData.append("reason", reason);

  fetch("update_request_status.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById("reviewForm").style.display = "none";
        document.getElementById("reviewDone").style.display = "block";
        document.getElementById("finalDecision").textContent = status;
        document.getElementById("decisionText").textContent = data.message;
      } else {
        alert(data.message);
      }
    });
}

/* =========================
   MANAGE USERS PAGE
   ========================= */
function loadManageUsers() {
  const tbody = document.querySelector(".users-table tbody");
  if (!tbody) return;

  fetch("get_patients.php")
    .then(res => res.json())
    .then(data => {
      tbody.innerHTML = "";

      data.forEach(user => {
        const isBlocked = user.account_status === "Blocked";
        const row = document.createElement("tr");

        row.innerHTML = `
          <td>#${user.patient_id}</td>
          <td>${user.full_name}</td>
          <td>${user.email}</td>
          <td>${user.city}</td>
          <td>
            <span class="user-status ${isBlocked ? "user-status--blocked" : "user-status--active"}">
              ${user.account_status}
            </span>
          </td>
          <td>
            <button 
              class="admin-btn ${isBlocked ? "admin-btn--secondary" : "admin-btn--danger"} admin-btn--sm toggle-user-btn"
              data-id="${user.patient_id}">
              ${isBlocked ? "Unblock" : "Block"}
            </button>
          </td>
        `;

        tbody.appendChild(row);
      });

      document.querySelectorAll(".toggle-user-btn").forEach(btn => {
        btn.addEventListener("click", function () {
          const patientId = this.dataset.id;
          const formData = new FormData();
          formData.append("patient_id", patientId);

          fetch("toggle_patient_status.php", {
            method: "POST",
            body: formData
          })
            .then(res => res.json())
            .then(result => {
              if (result.success) {
                loadManageUsers();
              } else {
                alert(result.message);
              }
            });
        });
      });
    });
}

/* =========================
   ADMIN DASHBOARD
   ========================= */
function loadAdminDashboard() {
  const hero = document.querySelector(".ad-dashboard-page");
  if (!hero) return;

  fetch("get_admin_dashboard.php")
    .then(res => res.json())
    .then(data => {
      const statValues = document.querySelectorAll(".ad-stat-card__value");
      if (statValues.length >= 4) {
        statValues[0].textContent = data.pending_requests;
        statValues[1].textContent = data.approved_requests;
        statValues[2].textContent = data.rejected_requests;
        statValues[3].textContent = data.blocked_users;
      }

      const requestTableBody = document.querySelectorAll(".ad-table")[0]?.querySelector("tbody");
      const usersTableBody = document.querySelectorAll(".ad-table")[1]?.querySelector("tbody");

      if (requestTableBody) {
        requestTableBody.innerHTML = "";
        data.recent_requests.forEach(req => {
          requestTableBody.innerHTML += `
            <tr>
              <td>
                <div class="td-name">${req.medication_name}</div>
                <div class="td-id">#${req.request_id}</div>
              </td>
              <td><span class="ad-badge ad-badge--${req.priority_level.toLowerCase()}">${req.priority_level}</span></td>
              <td><span class="ad-badge ad-badge--pending">${req.request_status}</span></td>
              <td><a href="request-details.html?id=${req.request_id}" class="ad-view-link">View</a></td>
            </tr>
          `;
        });
      }

      if (usersTableBody) {
        usersTableBody.innerHTML = "";
        data.recent_users.forEach(user => {
          usersTableBody.innerHTML += `
            <tr>
              <td>
                <div class="td-name">${user.full_name}</div>
                <div class="td-id">#${user.patient_id}</div>
              </td>
              <td>${user.email}</td>
              <td><span class="user-status ${user.account_status === "Blocked" ? "user-status--blocked" : "user-status--active"}">${user.account_status}</span></td>
            </tr>
          `;
        });
      }
    });
}

/* =========================
   HELPERS
   ========================= */
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  });
}