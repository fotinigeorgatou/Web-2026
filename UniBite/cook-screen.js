// ΦΟΡΜΑ ΔΗΜΙΟΥΡΓΙΑΣ ΑΓΓΕΛΙΑΣ
// Στέλνει τα δεδομένα στο backend endpoint create_ad.php

const form = document.querySelector("#food-form");

form.addEventListener("submit", function(event) {
  event.preventDefault();

  const formData = new FormData();

  const photoInput = document.querySelector("#photo");

  if (photoInput && photoInput.files.length > 0) {
  formData.append("photo", photoInput.files[0]);
  }

  formData.append("title", document.querySelector("#title").value);
  formData.append("description", document.querySelector("#description").value);

  
  formData.append("portions_total", document.querySelector("#portions").value);

  formData.append("pickup_location", document.querySelector("#location").value);

  const pickupTimeValue = document.querySelector("#pickup-time").value;
  const mysqlPickupTime = pickupTimeValue.replace("T", " ") + ":00";

  formData.append("pickup_time", mysqlPickupTime);
  formData.append("allergens", document.querySelector("#allergens").value);

  fetch("create_ad.php", {
    method: "POST",
    body: formData
  })
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      if (result.status === "success") {
        alert(result.message);

        form.reset();

        // Γυρνάμε στην αρχική για να εμφανιστεί η νέα αγγελία από τη βάση
        window.location.href = "home-screen.php";
      } else {
        alert(result.message);
      }
    })
    .catch(function(error) {
      console.error("Σφάλμα:", error);
      alert("Προέκυψε σφάλμα κατά τη δημιουργία της αγγελίας.");
    });
});


function loadRequests() {
  fetch("get_requests.php")
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      if (result.status === "success") {
        renderRequests(result.data);
      } else {
        showRequestsMessage("Δεν ήταν δυνατή η φόρτωση των αιτημάτων.");
      }
    })
    .catch(function(error) {
      console.error("Σφάλμα:", error);
      showRequestsMessage("Προέκυψε σφάλμα κατά τη φόρτωση των αιτημάτων.");
    });
}

function renderRequests(requests) {
  const requestsFeed = document.querySelector("#requests-feed");

  requestsFeed.innerHTML = "";

  if (requests.length === 0) {
    showRequestsMessage("Δεν υπάρχουν αιτήματα προς το παρόν.");
    return;
  }

  requests.forEach(function(request) {
    const card = document.createElement("div");
    card.className = "food-card";

    card.innerHTML = `
      <h3>${request.food_title}</h3>

      <p><strong>Καταναλωτής:</strong> ${request.consumer_name}</p>
      <p><strong>Κατάσταση:</strong> ${request.status}</p>
      <p><strong>Ημερομηνία αιτήματος:</strong> ${request.requested_at}</p>

      <button class="reserve-btn action-positive approve-btn"
        ${request.status !== "pending" ? "disabled" : ""}>
        Αποδοχή
      </button>

      <button class="reserve-btn action-negative reject-btn"
        ${request.status !== "pending" ? "disabled" : ""}>
        Απόρριψη
      </button>

      ${request.status === "approved" ? `
        <button class="reserve-btn action-positive picked-up-btn">
          Παρελήφθη
        </button>

        <button class="reserve-btn action-negative not-picked-up-btn">
          Δεν Παρελήφθη
        </button>
      ` : ""}
    `;

    requestsFeed.appendChild(card);

    const approveBtn = card.querySelector(".approve-btn");
    const rejectBtn = card.querySelector(".reject-btn");

    approveBtn.addEventListener("click", function() {
      respondToRequest(request.request_id, "approved");
    });

    rejectBtn.addEventListener("click", function() {
      respondToRequest(request.request_id, "rejected");
    });

    const pickedUpBtn = card.querySelector(".picked-up-btn");
    const notPickedUpBtn = card.querySelector(".not-picked-up-btn");

    if (pickedUpBtn) {
      pickedUpBtn.addEventListener("click", function() {
        confirmDelivery(request.request_id, "yes");
      });
    }

    if (notPickedUpBtn) {
      notPickedUpBtn.addEventListener("click", function() {
        confirmDelivery(request.request_id, "no");
      });
    }
});
}

function respondToRequest(requestId, action) {
  const formData = new FormData();

  formData.append("request_id", requestId);
  formData.append("action", action);

  fetch("respond_to_request.php", {
    method: "POST",
    body: formData
  })
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      alert(result.message);
      loadRequests();
    })
    .catch(function(error) {
      console.error("Σφάλμα:", error);
      alert("Προέκυψε σφάλμα κατά την ενημέρωση του αιτήματος.");
    });
}

function showRequestsMessage(message) {
  const requestsFeed = document.querySelector("#requests-feed");

  requestsFeed.innerHTML = `
    <p class="feed-message">${message}</p>
  `;
}

function confirmDelivery(requestId, appeared) {
  const formData = new FormData();

  formData.append("request_id", requestId);
  formData.append("appeared", appeared);

  fetch("confirm_delivery.php", {
    method: "POST",
    body: formData
  })
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      alert(result.message);
      loadRequests();
    })
    .catch(function(error) {
      console.error("Σφάλμα:", error);
      alert("Προέκυψε σφάλμα κατά την επιβεβαίωση παραλαβής.");
    });
}

loadRequests();

function loadMyPosts() {
  fetch("get_my_ads.php")
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      if (result.status === "success") {
        renderMyPosts(result.data);
      } else {
        showMyPostsMessage("Δεν ήταν δυνατή η φόρτωση των αγγελιών.");
      }
    })
    .catch(function(error) {
      console.error("Σφάλμα:", error);
      showMyPostsMessage("Προέκυψε σφάλμα κατά τη φόρτωση των αγγελιών.");
    });
}

function renderMyPosts(posts) {
  const myPostsFeed = document.querySelector("#my-posts-feed");

  myPostsFeed.innerHTML = "";

  if (posts.length === 0) {
    showMyPostsMessage("Δεν έχεις δημιουργήσει αγγελίες ακόμα.");
    return;
  }

  posts.forEach(function(post) {
    const card = document.createElement("div");
    card.className = "food-card";

    if (Number(post.portions_available) === 0) {
      card.classList.add("inactive");
    }

    card.innerHTML = `
      <h3>${post.title}</h3>

      <p>${post.description || ""}</p>

      <p><strong>Σύνολο μερίδων:</strong> ${post.portions_total}</p>
      <p><strong>Διαθέσιμες μερίδες:</strong> ${post.portions_available}</p>
      <p><strong>Παραλαβή:</strong> ${post.pickup_location}</p>
      <p><strong>Ώρα:</strong> ${post.pickup_time}</p>
      <p><strong>Κατάσταση:</strong> ${post.status}</p>

      <button class="reserve-btn action-positive edit-post-btn">
        Επεξεργασία
      </button>

      <button class="reserve-btn action-negative delete-post-btn">
        Διαγραφή
      </button>
    `;

    const editBtn = card.querySelector(".edit-post-btn");
    const deleteBtn = card.querySelector(".delete-post-btn");

    editBtn.addEventListener("click", function() {
      openEditPrompt(post);
    });

    deleteBtn.addEventListener("click", function() {
      deletePost(post.id);
    });

    myPostsFeed.appendChild(card);
  });
}

function showMyPostsMessage(message) {
  const myPostsFeed = document.querySelector("#my-posts-feed");

  myPostsFeed.innerHTML = `
    <p class="feed-message">${message}</p>
  `;
}

loadMyPosts();

function deletePost(postId) {

  if (!confirm("Θέλεις σίγουρα να διαγράψεις την αγγελία;")) {
    return;
  }

  const formData = new FormData();

  formData.append("post_id", postId);

  fetch("delete_ad.php", {
    method: "POST",
    body: formData
  })
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {

      alert(result.message);

      if (result.status === "success") {
        loadMyPosts();
      }

    })
    .catch(function(error) {
      console.error(error);
      alert("Προέκυψε σφάλμα κατά τη διαγραφή.");
    });
}

function openEditPrompt(post) {
  const modal = document.querySelector("#edit-modal");

  document.querySelector("#edit-post-id").value = post.id;
  document.querySelector("#edit-title").value = post.title;
  document.querySelector("#edit-description").value = post.description || "";
  document.querySelector("#edit-portions-total").value = post.portions_total;
  document.querySelector("#edit-portions-available").value = post.portions_available;
  document.querySelector("#edit-location").value = post.pickup_location;

  const pickupDate = post.pickup_time.replace(" ", "T").slice(0, 16);
  document.querySelector("#edit-pickup-time").value = pickupDate;

  modal.classList.remove("hidden");
}

const editModal = document.querySelector("#edit-modal");
const closeEditModalBtn = document.querySelector("#close-edit-modal");
const editForm = document.querySelector("#edit-form");

closeEditModalBtn.addEventListener("click", function() {
  editModal.classList.add("hidden");
});

editForm.addEventListener("submit", function(event) {
  event.preventDefault();

  const pickupTimeValue = document.querySelector("#edit-pickup-time").value;
  const mysqlPickupTime = pickupTimeValue.replace("T", " ") + ":00";

  const formData = new FormData();

  formData.append("post_id", document.querySelector("#edit-post-id").value);
  formData.append("title", document.querySelector("#edit-title").value);
  formData.append("description", document.querySelector("#edit-description").value);
  formData.append("portions_total", document.querySelector("#edit-portions-total").value);
  formData.append("portions_available", document.querySelector("#edit-portions-available").value);
  formData.append("pickup_location", document.querySelector("#edit-location").value);
  formData.append("pickup_time", mysqlPickupTime);

  fetch("edit_ad.php", {
    method: "POST",
    body: formData
  })
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      alert(result.message);

      if (result.status === "success") {
        editModal.classList.add("hidden");
        loadMyPosts();
        loadRequests();
      }
    })
    .catch(function(error) {
      console.error(error);
      alert("Προέκυψε σφάλμα κατά την επεξεργασία.");
    });
});