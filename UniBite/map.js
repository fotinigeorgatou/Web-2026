const mapElement = document.getElementById('map');
//Ανάκτηση και μετατροπή των δεδομένων από το data attribute
const adDataRaw = mapElement.getAttribute('data-ads');
const ad = JSON.parse(adDataRaw);

//Δημιουργία χάρτη setview sto panepisthmio
var map = L.map(mapElement).setView([38.286621, 21.78638], 13);
//Προσθήκη επιπέδου Tile (OpenStreetMap)
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

let markersGroup = L.layerGroup().addTo(map);


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

function displayAds(adslist){
    const adListElement=document.querySelector("#food-feed"); 
    markersGroup.clearLayers(); // Σβήσιμο παλιών markers

    adListElement.innerHTML = ''; //Σβήνει τα παλιά κείμενα από τη λίστα HTML
    const usedCoordinates = {};

    adslist.forEach(item=> {
        const OutOfStock = item.portions===0;
        const list = document.createElement('li');
        list.textContent = `${item.title} - Μερίδες: ${item.portions}`;
        //an exoun idies syntetagmenes tis allazoume elafrws gia na fainontai olew oi aggelies sto xarth
        const coordKey = `${item.lat.toFixed(5)},${item.lng.toFixed(5)}`;
        if(usedCoordinates[coordKey]){
            item.lat+=(Math.random() - 0.5) * 0.0003;
            item.lng+=(Math.random() - 0.5) * 0.0003;
        }else {
            usedCoordinates[coordKey] = true;
        }

        if (OutOfStock){
            list.style.color = 'gray';
        }
        adListElement.appendChild(list);

         const marker={
            opacity: OutOfStock ? 0.5 : 1.0,
        };
        const adMarker = L.marker([item.lat, item.lng], marker).addTo(map);
        const statusText = OutOfStock ? `Εξαντλήθηκε` :` Μερίδες: ${item.portions}`;
        adMarker.bindPopup(`<b>${item.title}</b><br>${statusText}`);
        markersGroup.addLayer(adMarker);
    });
}


displayAds(ad);

document.querySelector('.search_engine').addEventListener('submit', async function(e) {
    e.preventDefault();
            
    const addressInput=document.getElementById('search-address').value;
    const formData = new FormData();
    formData.append('address', addressInput);
    try {
        const response = await fetch('filtered_map.php', {
        method: 'POST',
        body: formData
        });
        if (!response.ok) {
            throw new Error(`HTTP σφάλμα! Status: ${response.status}`);
        }

        const data = await response.json();

        if (data.error) {
            alert(data.error);
            return;
        }

        displayAds(data.ads);

        map.setView([data.userLat, data.userLng], 14);

        //Προσθήκη ενός ειδικού Marker για την τοποθεσία του χρήστη
        const userMarker = L.circleMarker([data.userLat, data.userLng], {
            radius: 8,
            fillColor: "#75070C",
            color: "#fff",
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map);
        
        userMarker.bindPopup("<b>Η τοποθεσία σας</b>").openPopup();
        markersGroup.addLayer(userMarker);

        const filterEvent = new CustomEvent("mapFiltered", { detail: data.ads });
        window.dispatchEvent(filterEvent);

    } catch (error) {
        console.error('Σφάλμα κατά την ανάκτηση των δεδομένων:', error);
        alert('Παρουσιάστηκε πρόβλημα κατά την αναζήτηση.');
    }

});