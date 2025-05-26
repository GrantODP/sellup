
import {
  getAdListings,
  getCategories,
  getCookie,
  getPreview,
  getTemplate,
  getUrlParams,
  loadTemplates,
  navigateWindow,
  NotfoundError,
  renderErrorPage,
  renderStandardMessage,
  searchListing,
  titleCase,
}
  from './script.js';

async function renderCategories() {

  const categories = await getCategories();
  const container = document.getElementById(" category-container")
  const category_name = getCookie('cat_name') ?? "";
  const name_container = document.getElementById("category-name");
  name_container.innerText = category_name;

  categories.forEach(cat => {

    const button = document.createElement('button');
    button.textContent = cat.name;
    button.className = "btn btn-outline-dark w-100 text-start rounded-0 border-top border-bottom";
    button.addEventListener("click", () => {
      document.cookie = `cat_name=${cat.name}`;
      window.location = `/ads?category=${cat.cat_id}`;
    })
    container.appendChild(button);

  });
}

async function populateListings(listings) {
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

  for (const listing of listings) {
    const ad_article = template.cloneNode(true);
    const preview = await getPreview(listing.listing_id);

    if (preview) {
      ad_article.querySelector('img').src = `/${preview.path}`;
    }

    const date = new Date(listing.date);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };

    ad_article.querySelector('a').href = `/ads/${listing.slug}`;
    ad_article.querySelector('.ad-title').textContent = listing.title;
    ad_article.querySelector('.ad-price').textContent = `R${listing.price}`;
    ad_article.querySelector('.ad-description').textContent = listing.description;
    ad_article.querySelector('.ad-date').textContent = `Posted on: ${date.toLocaleDateString(undefined, options)}`;
    ad_article.querySelector('.ad-location').textContent = `Location: ${titleCase(listing.province)}, ${titleCase(listing.city)}`;

    container.appendChild(ad_article);
  }
}


async function renderListings() {
  const params = getUrlParams();


  const query = params.get('q') ?? '';
  const id = params.get('category') ?? 0;
  const sort_val = params.get('sort') ?? 'date';
  const sort_dir = params.get('dir') ?? 'desc';
  const page = params.get('page') ?? 1;
  const limit = params.get('limit') ?? 10;
  try {

    console.log(query);
    let listings;
    if (query) {
      listings = await searchListing(query)
    }
    else {

      listings = await getAdListings(id, page, limit, sort_val, sort_dir);
    }
    await populateListings(listings);
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
  document.getElementById("search-bar").addEventListener("submit", async function (e) {
    e.preventDefault();
    const form_data = new FormData(this);
    const search = form_data.get("q").trim();
    if (!search) {
      return;
    }

    document.cookie = `cat_name= Search for "${search}"`;
    navigateWindow(`ads?q=${search}`);
  });
}


document.addEventListener("DOMContentLoaded", (e) => {
  renderPage();
});
