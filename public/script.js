export class NotfoundError extends Error { };
export class Unauthorized extends Error { };

export function getCookie(cname) {
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

export async function loadTemplates(container, url = 'templates.html') {
  const res = await fetch(url);
  const html = await res.text();
  container.innerHTML = html;
}
export async function getTemplate(url = 'templates.html') {
  const res = await fetch(url);
  const html = await res.text();
  return html;
}

export async function getResource(uri, method = 'GET', data = null, headers = {}) {
  const url = `/c2c-commerce-site/api/${uri}`;
  const options = {
    method: method.toUpperCase(),
    headers: {
      'Content-Type': 'application/json',
      ...headers
    }
  };
  if (data && method.toUpperCase() !== 'GET') {
    options.body = JSON.stringify(data);
  }
  const response = await fetch(url, options);
  const json = await response.json();

  if (response.status == 404) {
    const message = json.message;
    throw new NotfoundError(message);
  }
  if (response.status == 401) {
    const message = json.message;
    throw new Unauthorized(message);
  }
  if (!response.ok) {
    const message = json.message;
    throw new Error(message);
  }

  return json.data;

}
export async function getCategories() {
  return getResource('categories');
}


export async function getAdImagesLinks(id) {
  // returns links
  return await getResource(`listings/media?id=${id}`);
}
export async function getSeller(id) {
  let seller = await getResource(`sellers?id=${id}`);
  let rating = await getSellerRating(seller.seller_id);
  seller.rating = rating;

  return seller;
}

export async function getSingleAd(slug) {

  return getResource(`listings/${slug}`);
}
export async function getSellerRating(id) {
  return getResource(`sellers/rating?id=${id}`);
}
export async function getSingleAdRating(id) {

  return getResource(`listings/rating?id=${id}`);
}

export function renderStars(container, rating, maxStars = 5) {

  container.innerText = '';
  const fullStars = Math.floor(rating);
  const halfStar = rating % 1 >= 0.5;

  for (let i = 0; i < fullStars; i++) {
    container.innerText += '★'; // full star
  }

  if (halfStar) {
    container.innerText += '☆'; // optional: use a special half-star icon if needed
  }

  for (let i = fullStars + halfStar; i < maxStars; i++) {
    container.innerText += '☆'; // empty star
  }
}

export async function getAdReviews(id) {
  return getResource(`listings/reviews?id=${id}`);
}

export function switchImage(img, container_id) {
  const main = document.getElementById(container_id);
  if (main) {
    main.src = img.src;
  }
}

export function getUrlParams() {
  const queryString = window.location.search;
  return new URLSearchParams(queryString);
}

export function getAdListings(category = 0, page = 1, limit = 5, sort = 'date', dir = 'desc') {
  const listings = getResource(`listings/category?id=${category}&page=${page}&sort=${sort}&limit=${limit}&dir=${dir}`);
  return listings;
}
export function populateProductImages(images) {
  const main_container = document.getElementById('product-image-container');
  const thumbnails_container = document.getElementById('product-image-thumbnails');

  if (!images || images.length === 0) return;

  const main_id = 'main-product-image';
  const main = document.createElement('img');
  main.id = main_id;
  main.src = `/c2c-commerce-site/${images[0].path}`;
  main.alt = "Main Product Image";
  main_container.appendChild(main);


  images.forEach((img, index) => {
    const thumb = document.createElement('img');
    thumb.src = `/c2c-commerce-site/${img.path}`;
    thumb.alt = `Thumbnail ${index + 1} `;
    thumb.onclick = () => switchImage(thumb, main_id);
    thumbnails_container.appendChild(thumb);
  });
  document.addEventListener('DOMContentLoaded', () => {
    const mainImage = document.getElementById('main-product-image');
    const container = document.getElementById('product-image-container');

    let zoomed = false;

    mainImage.addEventListener('click', () => {
      zoomed = !zoomed;
      container.classList.toggle('zoomed', zoomed);
    });
  });
}
export function renderErrorPage(container, message) {
  container.innerHTML = `
    <section class="error-page" role="alert" aria-live="assertive">
      <div class="error-content">
        <h3 class="error-title">Oops! Something went wrong.</h3>
        <p class="error-message">${message}</p>
        <button class="back-button" onclick="window.history.back()">Go Back</button>
      </div>
    </section>
  `;
}
export function renderStandardMessage(container, message) {
  container.innerHTML = `
    <section class="standard-page" role="alert" aria-live="assertive">
      <div class="standard-content">
        <p class="standard-message">${message}</p>
      </div>
    </section>
  `;
}

export async function searchListing(input) {
  const listings = await getResource(`listings/search?query=${input}`)
  return listings;
}

export function setOnClick(container_id, action) {
  document.getElementById(container_id).addEventListener("click", (e) => action());
}

export async function setActionSearchListener(action) {
  const input = document.getElementById("search-input")
  input.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
      action();
    }
  });
  input.addEventListener("invalid", function () {
    input.setCustomValidity("Please enter a search term.");
  });

  input.addEventListener("input", function () {
    input.setCustomValidity("");
  });
}

export function navigateWindow(page) {
  return window.location.href = `/c2c-commerce-site/${page}`;
}

export function login(email, password) {
  const data = {
    email: email,
    password: password
  }
  return getResource('api/login', 'GET', data);
}
