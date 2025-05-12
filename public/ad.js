// Pass PHP variable to JS
function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

const slug = getCookie("ad_slug");

// JS function to fetch listing
function getListing(slug) {
  fetch(`/c2c-commerce-site/api/listings/${slug}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch listing');
      return response.json();
    })
    .then(data => {
      renderListing(data.data);
    })
    .catch(err => {
      document.getElementById('product').innerHTML = `<p>Error: ${err.message}</p>`;
    });
}

// Function to render listing data into the DOM
function renderListing(data) {
  const container = document.getElementById('product');
  document.title = data.title;
  console.log(data);
  container.innerHTML = `
  <h1>${data.title}</h1>
  <p class="price">R${data.price}</p>
  <p class="description">${data.description}</p>
  `;
}

// Trigger it
getListing(slug);
