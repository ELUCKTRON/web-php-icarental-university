
document.querySelector("#car-bookings").addEventListener('click', function (event) {



  // Delete Booking
  if (event.target.classList.contains('Cancel')) {
    const confirmed = confirm('Are you sure you want to delete this booking?');
    if (!confirmed) return;

    const bookingId = event.target.dataset.id;
    console.log('Booking ID to delete:', bookingId);

    fetch('DeleteBooking.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ booking_id: bookingId })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Booking deleted successfully.');
          const card = event.target.closest('.booking-data');
          card.remove();
        } else {
          console.error('Failed to delete booking:', data.message);
          document.querySelector("#error").innerHTML = `Failed to delete booking: ${data.message}`;
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
  }
});

document.querySelector("#car-listings").addEventListener('click', function (event) {

  if (event.target.classList.contains('image')) {
    const carCard = event.target.closest('.car-card');
    const carId = carCard.querySelector('.button.Delete').dataset.id;
    window.location.href = `carDetails.php?carid=${carId}`;
  }


  // Delete Car
  if (event.target.classList.contains('Delete')) {
    const confirmed = confirm('Are you sure you want to delete this car?');
    if (!confirmed) return;

    const carId = event.target.dataset.id;
    console.log('Car ID to delete:', carId);

    fetch('DeleteCar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ car_id: carId })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Car deleted successfully.');
          const card = event.target.closest('.car-card');
          card.remove();
        } else {
          console.error('Failed to delete car:', data.message);
          document.querySelector("#form-result").innerHTML = `Failed to delete car: ${data.message}`;
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
  }


    // edit Car
    if (event.target.classList.contains('Edit')) {
      const confirmed = confirm('Are you sure you want to edit this car?');
      if (!confirmed) return;

      const car = JSON.parse(event.target.dataset.car);

      document.querySelector('#add-car-form').style.display = "flex";
      document.querySelector('#add-car-btn').style.display = "none";


      document.querySelector('#add-car-form').querySelector('h3').innerHTML = `editing ${car.id}`;

      document.querySelector("#form-result").innerHTML = "";

      document.querySelector('#brand').value = car.brand;
      document.querySelector('#model').value = car.model;
      document.querySelector('#year').value = car.year;
      document.querySelector('#transmission').value = car.transmission;
      document.querySelector('#fuel_type').value = car.fuel_type;
      document.querySelector('#passengers').value = car.passengers;
      document.querySelector('#daily_price_huf').value = car.daily_price_huf;

      document.querySelector('#image').value = "";

    }



});

// Filter Button AJAX
document.querySelector("#filter-button").addEventListener("click", function (event) {
  event.preventDefault();

  const result = document.querySelector("#result");
  const seats = document.querySelector("#seats").value;
  const gear = document.querySelector("#gear-type").value;
  const minPrice = document.querySelector("#price-min").value;
  const maxPrice = document.querySelector("#price-max").value;
  const fromDate = document.querySelector("#from").value;
  const untilDate = document.querySelector("#until").value;
  const timeline = document.querySelector("input[name='available']:checked")?.value || "all";

  if (minPrice && maxPrice && Number(minPrice) > Number(maxPrice)) {
    result.style.color = 'red';
    result.innerHTML = "Minimum price cannot be greater than maximum price.";
    return;
  }

  result.innerHTML = "Loading...";

  fetch('AdminFilterAjax.php', {
    method: "POST",
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      seats: seats || null,
      gearType: gear || null,
      minPrice: minPrice || null,
      maxPrice: maxPrice || null,
      from: fromDate || null,
      until: untilDate || null,
      available: timeline
    })
  })
    .then(response => response.json())
    .then(data => {
      const list = data.result;
      const carListings = document.querySelector("#car-bookings");

      if (data.success) {
        result.style.color = 'green';
        result.innerHTML = "Filtered results received successfully.";
      } else {
        result.style.color = 'red';
        result.innerHTML = data.message || "Filtering failed.";
      }
      carListings.innerHTML = bookingGenerator(list);
    })
    .catch(error => {
      result.style.color = 'red';
      result.innerHTML = "An error occurred while fetching filtered results.";
      console.error('Error:', error);
    });
});

// Reset Filters
document.querySelector("#filter-reset").addEventListener('click', function () {
  document.querySelector("#seats").value = "";
  document.querySelector("#gear-type").value = "";
  document.querySelector("#price-min").value = "";
  document.querySelector("#price-max").value = "";
  document.querySelector("#from").value = "";
  document.querySelector("#until").value = "";
  document.querySelector("input[name='available'][value='all']").checked = true;

  const filterButton = document.querySelector("#filter-button");
  filterButton.click();

  const result = document.querySelector("#result");
  result.innerHTML = "";
});


// Adding or Edit Car
document.querySelector('#save-car-btn').addEventListener('click', function () {

  const result = document.querySelector("#form-result");

  const brand = document.querySelector('#brand').value.trim();
  const model = document.querySelector('#model').value.trim();
  const year = parseInt(document.querySelector('#year').value, 10);
  const transmission = document.querySelector('#transmission').value;
  const fuel_type = document.querySelector('#fuel_type').value;
  const passengers = parseInt(document.querySelector('#passengers').value, 10);
  const daily_price_huf = parseInt(document.querySelector('#daily_price_huf').value, 10);
  const imageFile = document.querySelector('#image').files[0];

  if (!brand || !model || !year || !transmission || !fuel_type || !passengers || !daily_price_huf) {
    result.innerHTML = "Please fill out all fields.";
    return;
  }

  const typeOf = document.querySelector('#add-car-form').querySelector('h3').innerHTML.split(" ")
  const carId = (typeOf[0] === "editing") ? typeOf[1] : null ;

  const formData = new FormData();
  if (carId !== null) {
    formData.append('carId', carId);
  }
  formData.append('brand', brand);
  formData.append('model', model);
  formData.append('year', year);
  formData.append('transmission', transmission);
  formData.append('fuel_type', fuel_type);
  formData.append('passengers', passengers);
  formData.append('daily_price_huf', daily_price_huf);
  if (imageFile) {
    formData.append('image', imageFile);
   }

  fetch('AddOrEditCar.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        result.style.color = "green";
        result.innerHTML = "Car add Or Edit successfully!";
        document.querySelector("#car-listings").innerHTML = carGenerator(data.allcars);

        document.querySelector('#add-car-form').style.display = 'none';
        document.querySelector('#add-car-btn').style.display = "block";
      } else {
        result.style.color = "red";
        result.innerHTML = "Failed to add Or Edit car: " + data.message;
      }
    })
    .catch(error => {
      result.style.color = 'red';
      result.innerHTML = "An error occurred while Add Or Edit the car.";
      console.error(error);
    });


});


// Show Add Car Form
document.querySelector('#add-car-btn').addEventListener('click', function () {
  document.querySelector('#add-car-form').style.display = "flex";
  this.style.display = "none";

  document.querySelector('#add-car-form').querySelector('h3').innerHTML = "add new car";

  document.querySelector("#form-result").innerHTML = "";

  document.querySelector('#brand').value = "";
  document.querySelector('#model').value = "";
  document.querySelector('#year').value = "";
  document.querySelector('#transmission').value = "";
  document.querySelector('#fuel_type').value = "";
  document.querySelector('#passengers').value = "";
  document.querySelector('#daily_price_huf').value = "";
  document.querySelector('#image').value = "";

});

// hide add Car form

document.querySelector('#cancel-save-car-btn').addEventListener("click", function () {
  document.querySelector('#add-car-form').style.display = "none";
  document.querySelector('#add-car-btn').style.display = "flex";
})


// Function to Generate Car Cards
function carGenerator(list) {
  if (!Array.isArray(list)) {
    console.error("Invalid data format: Expected an array", list);
    return "<p>No cars available.</p>";
  }

  return list
    .map((car) => {
      // Properly serialize the car object to JSON and escape quotes
      const carData = JSON.stringify(car).replace(/"/g, '&quot;');

      return `
        <div class="car-card">
          <div class="car-image">
            <img src="${car.image || 'pictures/cars/placeholder.png'}" alt="Car Image">
            <p class="data">${car.daily_price_huf || 'N/A'} Ft</p>
          </div>
          <div class="car-info">
            <div class="content">
              <div>
                <h2>${car.brand || 'Unknown Brand'} ${car.model || 'Unknown Model'}</h2>
                <p>${car.passengers || 'N/A'} seats - ${car.transmission || 'N/A'}</p>
              </div>
              <div class="cars-buttons">
                <button data-id="${car.id || ''}" data-car="${carData}" class="button Edit">edit</button>
                <button data-id="${car.id || ''}" class="button Delete">Delete</button>
              </div>
            </div>
          </div>
        </div>
      `;
    })
    .join('');
}

// Function to Generate Booking Cards
function bookingGenerator(list) {
  if (!Array.isArray(list)) {
    console.error("Invalid data format: Expected an array", list);
    return "<p>No bookings available.</p>";
  }

  return list
    .map(item => {
      const car = item.car || {};
      const user = item.user || {};
      const booking = item.booking || {};

      return `
        <div class="booking-data">
          <div class="container user-info">
            <div class="user-photo">
              <img src="pictures/users/${user.image}" alt="User Photo" />
            </div>
            <div class="user-details">
              <h2>${user.full_name || 'Unknown User'}</h2>
              <p>Email: ${user.email || 'N/A'}</p>
              <p>${user.is_admin ? 'Admin' : 'Regular User'}</p>
            </div>
          </div>
          <div class="car-card">
            <div class="car-image">
              <img src="${car.image || 'pictures/cars/placeholder.png'}" alt="Car Image">
              <p class="data">${booking.start_date || 'N/A'} - ${booking.end_date || 'N/A'}</p>
            </div>
            <div class="car-info">
              <div class="content">
                <div>
                  <h2>${car.brand || 'Unknown Brand'} ${car.model || 'Unknown Model'}</h2>
                  <p>${car.passengers || 'N/A'} seats - ${car.transmission || 'N/A'}</p>
                </div>
                ${
                  booking.end_date && new Date(booking.end_date) > new Date()
                    ? `<button class="button Cancel" data-id="${booking.id || ''}">Cancel</button>`
                    : ''
                }
              </div>
            </div>
          </div>
        </div>
      `;
    })
    .join('');
}

// Back to Main Page Button
document.querySelector("#mainPage").addEventListener("click", () => {
  window.location.href = "index.php";
});
