// Delegated event listener for booking cars

document.querySelector("#car-listings").addEventListener("click", function (event) {

  if (event.target.classList.contains('image')) {
    const carCard = event.target.closest('.car-card');
    const carId = carCard.querySelector('.button.book').dataset.id;
    window.location.href = `carDetails.php?carid=${carId}`;
  }




  if (event.target.classList.contains("book")) {
    // Confirm booking
    if (!confirm("Are you sure you want to book this Car?")) {
      return;
    }

    const carName = event.target.dataset.name;
    const carID = event.target.dataset.id;
    const startDate = document.querySelector("#from").value;
    const endDate = document.querySelector("#until").value;
    const result = document.querySelector("#result");

    console.log("Car ID to add:", carID);

    // Validate inputs
    if (!startDate || !endDate) {
      result.style.color = "red";
      result.innerHTML = "Start date and end date are required.";
      return;
    }

    const now = new Date();
    now.setHours(0, 0, 0, 0);
    const startDateObj = new Date(startDate);
    const endDateObj = new Date(endDate);

    if (startDateObj < now) {
      result.style.color = "red";
      result.innerHTML = "Start date cannot be in the past.";
      return;
    }

    if (endDateObj <= startDateObj) {
      result.style.color = "red";
      result.innerHTML = "End date must be after the start date.";
      return;
    }

    // AJAX call
    fetch("AddBooking.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ car_id: carID, start_date: startDate, end_date: endDate }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          result.style.color = "green";
          result.innerHTML = `Booking was successful for ${carName} from ${startDate} until ${endDate}`;
        } else {
          result.style.color = "red";
          result.innerHTML = data.message || "Failed to add booking.";
        }
      })
      .catch((error) => {
        result.style.color = "red";
        result.innerHTML = "An error occurred while booking.";
        console.error("Error:", error);
      });
  }
});

// Navigate to My Bookings page
document.querySelector("#mybookings").addEventListener("click", () => {
  window.location.href = "user.php";
});

// Navigate to Admin Page (if admin)
const adminPage = document.querySelector("#adminPage");
if (adminPage) {
  adminPage.addEventListener("click", () => {
    window.location.href = "admin.php";
  });
}

// Filter button functionality
document.querySelector("#filter-button").addEventListener("click", function (event) {
  event.preventDefault();

  const result = document.querySelector("#result");
  const seats = document.querySelector("#seats").value;
  const gear = document.querySelector("#gear-type").value;
  const minPrice = document.querySelector("#price-min").value;
  const maxPrice = document.querySelector("#price-max").value;

  if (minPrice && maxPrice && Number(minPrice) > Number(maxPrice)) {
    result.style.color = "red";
    result.innerHTML = "Minimum price cannot be greater than maximum price.";
    return;
  }

  result.innerHTML = "Loading...";

  fetch("IndexFilterAjax.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      seats: seats || null,
      gearType: gear || null,
      minPrice: minPrice || null,
      maxPrice: maxPrice || null,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      const carListings = document.querySelector("#car-listings");
      const list = data.cars;

      if (data.success) {
        result.style.color = "green";
        result.innerHTML = "Filtered results received successfully.";
      } else {
        result.style.color = "red";
        result.innerHTML = data.message || "Filtering failed.";
      }

      carListings.innerHTML = carGenerator(list);
    })
    .catch((error) => {
      result.style.color = "red";
      result.innerHTML = "An error occurred while fetching filtered results.";
      console.error("Error:", error);
    });
});

// Reset filters functionality
document.querySelector("#filter-reset").addEventListener("click", function () {
  document.querySelector("#seats").value = "";
  document.querySelector("#gear-type").value = "";
  document.querySelector("#price-min").value = "";
  document.querySelector("#price-max").value = "";

  document.querySelector("#filter-button").click();

  const result = document.querySelector("#result");
  result.innerHTML = "";
});

// Car generator function
function carGenerator(list) {
  if (!Array.isArray(list)) {
    console.error("Invalid data format: Expected an array", list);
    return "<p>No cars available.</p>";
  }

  return list
    .map(
      (car) => `
      <div class="car-card">
        <div class="car-image">
          <img src="${car.image}" alt="Car Image">
          <p class="data">${car.daily_price_huf} Ft</p>
        </div>
        <div class="car-info">
          <div class="content">
            <div>
              <h2>${car.brand} ${car.model}</h2>
              <p>${car.passengers} seats - ${car.transmission}</p>
            </div>
            <button data-id="${car.id}" data-name="${car.brand} ${car.model}" class="button book">Book</button>
          </div>
        </div>
      </div>
    `
    )
    .join("");
}
