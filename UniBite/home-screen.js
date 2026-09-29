// Λίστα αγγελιών που θα γεμίσει από το backend
let foods = [];


// ΦΟΡΤΩΣΗ ΑΓΓΕΛΙΩΝ ΑΠΟ ΤΗ ΒΑΣΗ
// Καλεί το get_ads.php, το οποίο επιστρέφει JSON από τον πίνακα food_posts
function loadFoodPosts() {
  fetch("get_ads.php")
    .then(function(response) {
      return response.json();
    })
    .then(function(result) {
      if (result.status === "success") {
        foods = result.data;
        renderFoodCards();

      } else {
        showFeedMessage("Δεν ήταν δυνατή η φόρτωση των αγγελιών.");
      }
    })
    .catch(function(error) {
      console.error("Σφάλμα fetch:", error);
      showFeedMessage("Προέκυψε σφάλμα κατά τη φόρτωση των αγγελιών.");
    });
}


// ΔΗΜΙΟΥΡΓΙΑ ΚΑΡΤΩΝ ΦΑΓΗΤΟΥ
function renderFoodCards() {
  const feed = document.querySelector("#food-feed");

  feed.innerHTML = "";

  if (foods.length === 0) {
    showFeedMessage("Δεν υπάρχουν διαθέσιμες αγγελίες αυτή τη στιγμή.");
    return;
  }

  foods.forEach(function(food) {
    const card = document.createElement("div");
    card.className = "food-card";

    if (Number(food.portions_available) === 0) {
      card.classList.add("inactive");
    }

    const imagePath = food.photo || "images/food-default.png";

    card.innerHTML = `
      <img class="food-img"
        src="${imagePath}"
        alt="${food.title}">

      <h3>${food.title}</h3>

      <p>${food.description || ""}</p>

      <p><strong>Μερίδες:</strong> ${food.portions_available}</p>

      <p><strong>Παραλαβή:</strong> ${food.pickup_location}</p>

      <p><strong>Ώρα:</strong> ${formatPickupTime(food.pickup_time)}</p>

      <div class="allergen-list">
        ${food.allergens.length > 0
          ? food.allergens.map(allergen => `<span class="allergen-badge">${allergen}</span>`).join("")
          : `<span class="allergen-badge no-allergen">Χωρίς δηλωμένα αλλεργιογόνα</span>`
        }
      </div>

      ${Number(food.cook_id) === Number(loggedInUserId)
        ? `<button class="reserve-btn" disabled>Δική σου Αγγελία</button>`
        : `<button
            class="reserve-btn"
            data-post-id="${food.id}"
            ${Number(food.portions_available) === 0 ? "disabled" : ""}>
            Κράτηση Μερίδας
           </button>`
   }
    `;

    feed.appendChild(card);

    const reserveBtn = card.querySelector(".reserve-btn[data-post-id]");

    if (reserveBtn) {
      reserveBtn.addEventListener("click", function () {
        const formData = new FormData();

        formData.append("post_id", food.id);

        fetch("request_portion.php", {
          method: "POST",
          body: formData
        })
          .then(function(response) {
            return response.json();
          })
          .then(function(result) {
            alert(result.message);
          })
          .catch(function(error) {
            console.error("Σφάλμα:", error);
            alert("Προέκυψε σφάλμα κατά την κράτηση.");
          });
      });
    }

  });
}  

// ΜΗΝΥΜΑ ΣΤΟ FEED
function showFeedMessage(message) {
  const feed = document.querySelector("#food-feed");

  feed.innerHTML = `
    <p class="feed-message">${message}</p>
  `;
}

// ΜΟΡΦΟΠΟΙΗΣΗ ΗΜΕΡΟΜΗΝΙΑΣ / ΩΡΑΣ
function formatPickupTime(dateTime) {
  if (!dateTime) {
    return "Δεν έχει οριστεί";
  }

  const date = new Date(dateTime);

  if (isNaN(date.getTime())) {
    return dateTime;
  }

  return date.toLocaleString("el-GR", {
    dateStyle: "short",
    timeStyle: "short"
  });
}

// Ξεκινά η φόρτωση αγγελιών όταν ανοίγει η σελίδα
loadFoodPosts();

//feed μετά το φιλτράρισμα
window.addEventListener("mapFiltered", function(e) {

  const filteredAdsFromMap = e.detail; // Εδώ έρχονται τα data.ads του PHP

  const formatedAds = filteredAdsFromMap.map(item => {
    return {
      id: item.id || null,
      title: item.title,
      description: item.description || "",
      portions_available: item.portions, 
      latitude: item.lat,                
      longitude: item.lng,               
      pickup_location: item.pickup_location,
      pickup_time: item.pickup_time || null,
      allergens: item.allergens || [],
      photo: item.photo || null,
      cook_id: item.cook_id || null
    };
  });
  foods=formatedAds;
  renderFoodCards();
});