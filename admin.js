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