// ΦΟΡΤΩΣΗ ΓΕΥΜΑΤΩΝ ΠΡΟΣ ΑΞΙΟΛΟΓΗΣΗ
function loadMealsForRating() {
  fetch("get_completed_requests.php")
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      if (result.status === "success") {
        renderRatingCards(result.data);
      } else {
        showRatingMessage("Δεν ήταν δυνατή η φόρτωση των γευμάτων.");
      }
    })
    .catch(function(error) {
      console.error("Σφάλμα:", error);
      showRatingMessage("Προέκυψε σφάλμα κατά τη φόρτωση.");
    });
}


// ΕΜΦΑΝΙΣΗ ΚΑΡΤΩΝ ΑΞΙΟΛΟΓΗΣΗΣ
function renderRatingCards(meals) {
  const ratingFeed = document.querySelector("#rating-feed");

  ratingFeed.innerHTML = "";

  if (meals.length === 0) {
    showRatingMessage("Δεν υπάρχουν γεύματα προς αξιολόγηση.");
    return;
  }

  meals.forEach(function(meal) {
    const card = document.createElement("div");
    card.className = "food-card";

    card.innerHTML = `
      <h3>${meal.food_title}</h3>

      <p><strong>Μάγειρας:</strong> ${meal.cook_name}</p>
      <p><strong>Ώρα παραλαβής:</strong> ${meal.pickup_time}</p>

      <div class="stars" data-rating="0">
        ${createStars(0)}
      </div>

      <button class="reserve-btn submit-rating-btn">
        Υποβολή Αξιολόγησης
      </button>
    `;

    const starsContainer = card.querySelector(".stars");
    const submitBtn = card.querySelector(".submit-rating-btn");

    starsContainer.addEventListener("click", function(event) {
      if (event.target.tagName !== "SPAN") {
        return;
      }

      const rating = Number(event.target.dataset.value);

      starsContainer.dataset.rating = rating;
      starsContainer.innerHTML = createStars(rating);
    });

    submitBtn.addEventListener("click", function() {
      const rating = Number(starsContainer.dataset.rating);

      if (rating === 0) {
        alert("Παρακαλώ επίλεξε βαθμολογία.");
        return;
      }

      submitRating(meal.request_id, meal.post_id, rating);
    });

    ratingFeed.appendChild(card);
  });
}


// ΔΗΜΙΟΥΡΓΙΑ ΑΣΤΕΡΙΩΝ
function createStars(rating) {
  let starsHTML = "";

  for (let i = 1; i <= 5; i++) {
    if (i <= rating) {
      starsHTML += `<span data-value="${i}">★</span>`;
    } else {
      starsHTML += `<span data-value="${i}">☆</span>`;
    }
  }

  return starsHTML;
}


// ΥΠΟΒΟΛΗ ΑΞΙΟΛΟΓΗΣΗΣ ΣΤΗ ΒΑΣΗ
function submitRating(requestId, postId, rating) {
  const formData = new FormData();

  formData.append("request_id", requestId);
  formData.append("post_id", postId);
  formData.append("rating", rating);

  fetch("submit_rating.php", {
    method: "POST",
    body: formData
  })
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      alert(result.message);

      if (result.status === "success") {
        loadMealsForRating();
      }
    })
    .catch(function(error) {
      console.error("Σφάλμα:", error);
      alert("Προέκυψε σφάλμα κατά την υποβολή της αξιολόγησης.");
    });
}


// ΜΗΝΥΜΑ ΟΤΑΝ ΔΕΝ ΥΠΑΡΧΟΥΝ ΓΕΥΜΑΤΑ
function showRatingMessage(message) {
  const ratingFeed = document.querySelector("#rating-feed");

  ratingFeed.innerHTML = `
    <p class="feed-message">${message}</p>
  `;
}


// Εκκίνηση
loadMealsForRating();