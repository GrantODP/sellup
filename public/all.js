
import { getAdListings, getCategories, getCookie, getTemplate, getUrlParams, loadTemplates, navigateWindow, NotfoundError, renderErrorPage, renderStandardMessage, searchListing, setActionSearchListener, setOnClick } from './script.js';

async function renderCategories() {

  const categories = await getCategories();
  const container = document.getElementById("category-content")
  const category_name = getCookie('cat_name') ?? "";
  const name_container = document.getElementById("category-name");
  name_container.innerText = category_name;

  categories.forEach(cat => {

    const button = document.createElement('button');
    button.textContent = cat.name;
    button.className = "category-btn";
    button.addEventListener("click", () => {

      document.cookie = `cat_name=${cat.name}`;
      window.location = `/c2c-commerce-site/ads?id=${cat.cat_id}`;
    })
    container.appendChild(button);

  });
}
async function populateListings(listings) {
  console.log(listings);
  const container = document.getElementById("ads-container");
  container.innerHTML = "";
  if (!listings || listings.length === 0) {
    container.innerHTML = `<p>No listings available.</p>`;
    return;
  }
  const template_html = await getTemplate('../frontend/views/ad_article.html');
  const template = document.createElement('article');
  template.className = 'ad-listing';
  template.innerHTML = template_html.trim();
  console.log(template);
  listings.forEach(listing => {
    const ad_article = template.cloneNode(true);

    console.log(ad_article);
    ad_article.querySelector('a').href = `/c2c-commerce-site/ads/${listing.slug}`;
    ad_article.querySelector('.ad-title').textContent = listing.title;
    ad_article.querySelector('.ad-price').textContent = `R${listing.price}`;
    ad_article.querySelector('.ad-description').textContent = listing.description;
    ad_article.querySelector('.ad-date').textContent = `Posted on: ${listing.date.split(' ')[0]}`;
    ad_article.querySelector('.ad-location').textContent = `Location: ${listing.province}, ${listing.city}`;
    container.appendChild(ad_article);
  })
}


async function renderListings() {
  const params = getUrlParams();


  const query = params.get('query') ?? '';
  const id = params.get('id') ?? 0; //category
  const sort_val = params.get('sort') ?? 'date';
  const sort_dir = params.get('dir') ?? 'desc';
  const page = params.get('page') ?? 1;
  const limit = params.get('limit') ?? 10;

  try {

    let listings;
    if (query) {
      listings = await searchListing(query)
    }
    else {

      listings = await getAdListings(id, page, limit, sort_val, sort_dir);
    }
    populateListings(listings);
  }
  catch (err) {
    const container = document.getElementById("ads-container")
    if (err instanceof NotfoundError) {
      renderStandardMessage(container, err.message);
    }
    else {
      renderErrorPage(container, err.message);
    }
  }

}
async function renderPage() {
  initSearch();
  const container = document.getElementById('page-body');
  await loadTemplates(container, '../frontend/views/ad_listings_template.html');
  renderCategories();
  renderListings()
}

async function initSearch() {
  setActionSearchListener(searchAd);
  setOnClick("search-btn", searchAd);
}

function searchAd() {
  console.log("searching")
  const search = document.getElementById("search-input").value;
  navigateWindow(`ads?query=${search}`);
}

renderPage();
