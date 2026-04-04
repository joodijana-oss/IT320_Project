document.addEventListener("DOMContentLoaded", function () {
  const statusFilter = document.getElementById("statusFilter");
  const priorityFilter = document.getElementById("priorityFilter");
  const searchInput = document.getElementById("searchRequest");
  const requestCards = document.querySelectorAll(".request-card");
  const noResultsMessage = document.getElementById("noResultsMessage");

  if (!statusFilter || !priorityFilter || !searchInput || requestCards.length === 0) {
    return;
  }

  function filterRequests() {
    const selectedStatus = statusFilter.value.toLowerCase();
    const selectedPriority = priorityFilter.value.toLowerCase();
    const searchValue = searchInput.value.trim().toLowerCase();

    let visibleCount = 0;

    requestCards.forEach((card) => {
      const cardStatus = (card.dataset.status || "").toLowerCase();
      const cardPriority = (card.dataset.priority || "").toLowerCase();
      const cardMedication = (card.dataset.medication || "").toLowerCase();
      const cardId = (card.dataset.id || "").toLowerCase();

      const matchesStatus =
        selectedStatus === "all" || cardStatus === selectedStatus;

      const matchesPriority =
        selectedPriority === "all" || cardPriority === selectedPriority;

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
});


//request details page
 function toast(msg, cls = '') {
      const t = document.createElement('div');
      t.className = 'ad-toast ' + cls;
      t.textContent = msg;
      document.getElementById('toasts').appendChild(t);
      setTimeout(() => t.remove(), 4000);
    }

    function submitDecision(decision) {
      const reason = document.getElementById('reviewReason').value.trim();

      document.getElementById('finalDecision').textContent = decision;

      if (decision === 'Approved') {
        document.getElementById('decisionText').textContent =
          'The request has been approved and will now become visible to pharmacies.';
        toast('Request approved successfully.', 'ad-toast--ok');
      } else {
        document.getElementById('decisionText').textContent =
          reason !== ''
            ? 'The request has been rejected. Reason: ' + reason
            : 'The request has been rejected.';
        toast('Request rejected successfully.', 'ad-toast--err');
      }

      document.getElementById('reviewForm').style.display = 'none';
      document.getElementById('reviewDone').style.display = '';
    }

    //manage users page
    document.addEventListener("DOMContentLoaded", function () {

  // Handle all block/unblock buttons
  document.querySelectorAll(".toggle-user-btn").forEach((btn) => {
    btn.addEventListener("click", function () {

      const row = btn.closest("tr"); // get the user row
      const statusElement = row.querySelector(".user-status");

      if (!statusElement) return;

      const isBlocked = statusElement.classList.contains("user-status--blocked");

      if (isBlocked) {
        // UNBLOCK
        statusElement.textContent = "Active";
        statusElement.classList.remove("user-status--blocked");
        statusElement.classList.add("user-status--active");

        btn.textContent = "Block";
        btn.classList.remove("admin-btn--secondary");
        btn.classList.add("admin-btn--danger");

      } else {
        // BLOCK
        statusElement.textContent = "Blocked";
        statusElement.classList.remove("user-status--active");
        statusElement.classList.add("user-status--blocked");

        btn.textContent = "Unblock";
        btn.classList.remove("admin-btn--danger");
        btn.classList.add("admin-btn--secondary");
      }

    });
  });

});