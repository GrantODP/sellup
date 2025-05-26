import {
  renderErrorPage,
  getAdImagesLinks,
  getAdReviews,
  getCookie,
  getSeller,
  getSingleAd,
  getSingleAdRating,
  loadTemplates,
  populateProductImages,
  renderStars,
  NotfoundError,
  renderStandardMessage,
  addToCart,
  storeSessionData,
  getSessionData,
  isLoggedIn,
  reportAd,
} from './script.js';

import { marked } from 'https://cdn.jsdelivr.net/npm/marked@5.1.0/lib/marked.esm.js';
import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm';
const slug = getCookie("ad_slug");



async function eval_product(id) {
  console.log("Evaluating");
  const container = document.getElementById('product-data');
  let button = container.querySelector('#eval_btn');
  button.innerText = 'evaluating';
  button.disabled = true;
  const eval_ad = await fetch(`/c2c-commerce-site/api/listings/evaluate?id=${id}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to evaluate product');
      return response.json();
    });
  console.log(eval_ad);
  container.querySelector('#eval-body').innerHTML = marked.parse(eval_ad.data);

  button.disabled = false;
  button.innerText = 'Evaluate';
}


async function renderAdScore(id) {
  const container = document.getElementById('product-data');
  try {
    const score = await getSingleAdRating(id);
    console.log(score);
    const star_body = container.querySelector('#ascore-body');
    renderStars(star_body, score.rating);
  } catch (err) {
    console.log(err);
    renderErrorPage(container, err.message);
  }
}
async function renderSeller(id) {
  const container = document.getElementById('seller-info');
  try {
    const seller = await getSeller(id);
    console.log(seller);
    container.querySelector('#sel-name').innerText = seller.name;
    container.querySelector('#rate-count').innerText = seller.rating.count + " reviews";
    container.querySelector('#contact').innerText = "Phone number: " + seller.contact;
    container.querySelector('#verification').innerText = "Verification: " + seller.verification;

    const rate_body = container.querySelector('#rate-body');
    renderStars(rate_body, seller.rating.rating);
  } catch (err) {

    renderErrorPage(container, err.message);
  }
}

function renderSingleReview(review_data) {
  const container = document.getElementById('reviews-container');
  const review = document.createElement('div');
  review.classList.add('review');
  review.innerHTML = `
    <div class="review-header">
      <div>
        <div class="review-username">${review_data.user_name}</div>
        <div class="review-date">${review_data.created_at}</div>
      </div>
      <div class="review-score">⭐ ${review_data.rating}</div>
    </div>
    <div class="review-text">${review_data.message}</div>
  `;

  container.appendChild(review);

}

async function renderReviews(id) {
  try {
    const reviews = await getAdReviews(id);
    reviews.forEach(element => {
      renderSingleReview(element)
    });
  } catch (err) {
    const container = document.getElementById('reviews-container');
    if (err instanceof NotfoundError) {
      renderStandardMessage(container, "No reviews");
      return;
    }
    renderErrorPage(container, err.message);
  }
}
async function renderAdImages(id) {
  try {
    const images = await getAdImagesLinks(id);
    console.log(images);
    populateProductImages(images);
  } catch (err) {
    const container = document.getElementById('product-image-container');
    if (err instanceof NotfoundError) {
      renderStandardMessage(container, "No images");
      return;
    }
    renderErrorPage(container, err.message);
  }
}
async function setReportAd() {

  const logged_in = await isLoggedIn();
  if (!logged_in) {
    Swal.fire({
      icon: 'info',
      title: 'Must log in first'
    });
    return
  }
  const { value: message } = await Swal.fire(
    {
      icon: "question",
      input: "textarea",
      inputPlaceholder: "Why are you reporting?",
      showCancelButton: true
    }
  );

  const listing = getSessionData('ad').listing_id;
  if (message && listing) {
    try {

      await reportAd(listing, message);
      Swal.fire({
        icon: 'success',
        title: 'Submitted report'
      });
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Error reporting ad'
      });
    }
  }

}
async function setAddToCart() {

  const logged_in = await isLoggedIn();

  if (!logged_in) {
    Swal.fire({
      icon: 'info',
      title: 'Must log in first'
    });
    return
  }
  const { value: count } = await Swal.fire(
    {
      title: "Select how many to add to cart",
      input: "select",
      inputOptions: {
        1: '1',
        2: '2',
        3: '3',
        4: '4',
        5: '5',
        6: '6',
        7: '7',
        8: '8',
        9: '9',
        10: '10'
      },
    }
  );

  const listing = getSessionData('ad').listing_id;
  if (count && listing) {
    try {
      await addToCart(listing, count);

      Swal.fire({
        icon: 'success',
        title: 'Added to cart'
      });
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Error adding to cart'
      });
    }
  }

}
async function renderAd(slug) {
  const container = document.getElementById('page-body');
  const ad = await getSingleAd(slug);
  storeSessionData('ad', ad);
  document.title = ad.title;
  await loadTemplates(container, '../frontend/views/ad_tempalte.html');

  container.querySelector('#ptitle').innerText = document.title;
  container.querySelector('#ad-descp-body').innerText = ad.description;
  container.querySelector('#price').innerText = "R" + ad.price;
  container.querySelector('#eval_btn').onclick = () => { eval_product(ad.listing_id) };
  container.querySelector('#add-cart-btn').onclick = setAddToCart;
  container.querySelector('#report-ad').onclick = setReportAd;
  await renderAdScore(ad.listing_id);
  await renderSeller(ad.seller_id);
  await renderReviews(ad.listing_id);
  await renderAdImages(ad.listing_id);






}
await renderAd(slug);
