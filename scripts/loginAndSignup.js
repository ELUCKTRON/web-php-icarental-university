// Input event handlers
const inputs = document.querySelectorAll('.form input, .form textarea');

inputs.forEach((input) => {
  input.addEventListener('keyup', handleEvent);
  input.addEventListener('blur', handleEvent);
  input.addEventListener('focus', handleEvent);

  function handleEvent(event) {
    const label = input.previousElementSibling;

    if (event.type === 'keyup') {
      if (input.value === '') {
        label.classList.remove('active', 'highlight');
      } else {
        label.classList.add('active', 'highlight');
      }
    } else if (event.type === 'blur') {
      if (input.value === '') {
        label.classList.remove('active', 'highlight');
      } else {
        label.classList.remove('highlight');
      }
    } else if (event.type === 'focus') {
      if (input.value !== '') {
        label.classList.add('highlight');
      }
    }
  }
});

// Tab switching
const tabs = document.querySelectorAll('.tab a');
const tabContent = document.querySelectorAll('.tab-content > div');

tabs.forEach((tab) => {
  tab.addEventListener('click', (event) => {
    event.preventDefault();

    tabs.forEach((t) => t.parentElement.classList.remove('active'));
    tab.parentElement.classList.add('active');

    tabContent.forEach((content) => (content.style.display = 'none'));

    const target = document.querySelector(tab.getAttribute('href'));
    target.style.display = 'block';
  });
});

