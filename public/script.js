
// JavaScript to handle the button click event
document.getElementById('clickButton').addEventListener('click', function () {
  document.getElementById('message').textContent = 'Button clicked! The message has changed.';
});

function getListing(slug) {
  // Define the URL with the slug parameter
  const url = `/c2c-commerce-site/api/listings/${slug}`;

  // Fetch the data from the API
  fetch(url)
    .then(response => {
      // Check if the response is okay (status code 200)
      if (!response.ok) {
        throw new Error('Failed to fetch listing');
      }
      return response.json(); // Parse the JSON data
    })
    .then(data => {
      // Handle the data, e.g., display the listing
      console.log('Listing data:', data);
      // You can update the page with the listing information here
    })
    .catch(error => {
      // Handle any errors
      console.error('Error:', error);
    });
}
