import { Swal, navigateWindow, getSellerListings, getUserSellerInfo, isLoggedIn, getUrlParams, getLocalData, storeLocalData, SITE, updateListing, uploadLisingImages } from "../script.js";

async function loadSection(section) {
  const content = document.getElementById('main-content');
  try {

    await getSellerInfo()

    if (section === 'info') {
      await loadSeller(content);
    } else if (section === 'ads') {
      await loadAds(content);
    } else if (section === 'cart') {
      // await loadCart(content);
    }
  } catch (error) {
    content.innerHTML = `<p class="text-danger">Failed to load seller information. Please try again later.</p>`;
  }
}

async function getSellerInfo() {
  const seller = await getUserSellerInfo();
  storeLocalData('seller', seller);
}

async function loadSeller(container) {
  const seller = getLocalData('seller');
  if (!seller) {
    container.innerHTML = `
        <p>You are not a seller yet. Please post an ad to become a seller.</p>
      `;
    return;
  }

  container.innerHTML = `
      <div class="seller-info">
        <h3>Seller Profile</h3>
        <p><strong>Name:</strong> ${seller.name}</p>
        <p><strong>Contact:</strong> ${seller.contact}</p>
        <p><strong>Verification status:</strong> ${seller.verification}</p>
        <p><strong>Selling from:</strong> ${new Date(seller.created_at).toLocaleDateString()}</p>
      </div>
    `;

}


async function loadAds(container) {
  container.innerHTML = ``;
  const seller = getLocalData('seller');

  if (!seller) {
    container.innerHTML = `
        <p>You are not a seller yet. Please post an ad to become a seller.</p>
      `;
    return;
  }
  const ads = await getSellerListings(seller.seller_id);
  if (!ads || ads.length === 0) {
    content.innerHTML = `<p>You have no ads posted yet.</p>`;
    return;
  }

  // Render ads list
  container.innerHTML = ads.map(ad => `
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">${ad.title}</h5>
          <p class="card-text">${ad.description}</p>
          <p class="card-text">
            <small class="text-muted">Posted on: ${new Date(ad.date).toLocaleDateString()}</small>
          </p>
          <a href="/${SITE}/ads/${ad.slug}" target="_blank" class="btn btn-primary me-2">View</a>
          <button class="btn btn-secondary edit-btn" data-ad-id="${ad.listing_id}">Edit</button>
        </div>
      </div>
    `).join('');

  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-ad-id');
      const listing = ads.find(ad => ad.listing_id == id);
      if (listing) {
        loadListingEdit(listing);
      }
    });
  });
}


function loadListingEdit(listing) {
  const container = document.getElementById('main-content'); // or pass container as param

  container.innerHTML = `
    <h2>Edit Listing</h2>

    <!-- Listing Info Form -->
    <form id="edit-listing-form" class="mb-4">
      <input type="hidden" name="listing_id" value="${listing.listing_id}">

      <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" required value="${listing.title}">
      </div>

      <div class="mb-3">
        <label for="price" class="form-label">Price (ZAR)</label>
        <input type="number" step="0.01" class="form-control" id="price" name="price" required value="${listing.price}">
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4" required>${listing.description}</textarea>
      </div>

      <button type="submit" class="btn btn-primary">Save Info</button>
    </form>

    <form id="upload-images-form" enctype="multipart/form-data" method="post>
      <input type="hidden" name="listing_id" value="${listing.listing_id}">

      <div class="mb-3">
        <label for="images" class="form-label">Upload Images</label>
        <input class="form-control" type="file" id="images" name="images[]" accept="image/*" multiple>
        <small class="form-text text-muted">You can upload multiple images.</small>
      </div>

      <button type="submit" class="btn btn-secondary">Upload Images</button>
    </form>

    <button type="button" class="btn btn-link mt-3" id="cancel-edit">Cancel</button>
  `;

  document.getElementById('cancel-edit').addEventListener('click', () => {
    loadAds(container);
  });

  document.getElementById('edit-listing-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);


    for (const [key, value] of formData.entries()) {
      console.log(key, value);
    }
    try {
      await updateListing(formData);
      await Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Listing info updated successfully!',
        timer: 2000,
        showConfirmButton: false
      });

      loadAds(container);

    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err.message,
      });
    }
  });

  document.getElementById('upload-images-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      await uploadLisingImages(listing.listing_id, formData);

      await Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Images uploaded successfully!',
        timer: 2000,
        showConfirmButton: false
      });
      loadAds(container);

    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err.message,
      });
    }
  });
}

function initPage() {
  document.getElementById('btn-profile').onclick = () => loadSection('info');
  document.getElementById('btn-ads').onclick = () => loadSection('ads');
  const sec = getUrlParams().get('sec') ?? 'info';

  loadSection(sec);
}

document.addEventListener("DOMContentLoaded", async function () {
  const isIn = await isLoggedIn();
  if (!isIn) {
    navigateWindow('login');
  }

  initPage();
});

