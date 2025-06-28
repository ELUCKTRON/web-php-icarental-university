// Event delegation for Cancel button
document.querySelector('.car-listing.container').addEventListener('click', (event) => {



  if (event.target.classList.contains('Cancel')) {
    const confirmed = confirm('Are you sure you want to delete this booking?');
    if (!confirmed) {
      console.log('User cancelled deletion.');
      return;
    }

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
          const card = event.target.closest('.car-card');
          card.remove();
        } else {
          console.error('Failed to delete booking:', data.message);
          const errorElement = document.querySelector("#error");
          errorElement.innerHTML = `Failed to delete booking: ${data.message}`;
          errorElement.style.color = "red";
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
  }
});

// Edit user details
const editButton = document.querySelector('#edit');
const saveButton = document.querySelector('#save');
const fileInput = document.querySelector('#file-input');
const textInput = document.querySelector('#text-input');
const userName = document.querySelector('#user-name');
const userImage = document.querySelector('#user-image');

editButton.addEventListener("click", () => {
  textInput.style.display = 'block';
  textInput.value = userName.textContent;
  fileInput.style.display = 'block';

  editButton.style.display = 'none';
  saveButton.style.display = 'inline';
});

saveButton.addEventListener('click', () => {
  const newName = textInput.value;
  const userId = userImage.dataset.id;
  const imageFile = fileInput.files[0];

  const formData = new FormData();
  formData.append('name', newName);
  formData.append('user_id', userId);
  if (imageFile) {
    formData.append('image', imageFile);
  }

  // Send data via AJAX
  fetch('EditUser.php', {
    method: 'POST',
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update UI
        userName.textContent = newName;
        if (data.newImage) {
          userImage.src = `pictures/users/${data.newImage}`;
        }

        document.querySelector("#error").innerHTML = "";
      } else {
        document.querySelector("#error").innerHTML = data.message || 'Error saving changes.';
      }
    })
    .catch((error) => {
      console.error('Error:', error);
      document.querySelector("#error").innerHTML = 'An error occurred. Please try again.';
    });

  // Hide inputs
  textInput.style.display = 'none';
  fileInput.style.display = 'none';

  // Show edit button and hide save button
  editButton.style.display = 'inline';
  saveButton.style.display = 'none';
});

// Redirect to main booking page
document.querySelector("#book").addEventListener("click", () => {
  window.location.href = "index.php";
});
