
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

export async function getAdImagesLinks(id) {
  let images = await fetch(`/c2c-commerce-site/api/listings/media?id=${id}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch listing');
      return response.json();
    });
  return images.data;
}
export async function getSeller(id) {
  let seller = await fetch(`/c2c-commerce-site/api/sellers?id=${id}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch listing');
      return response.json();
    });
  let rating = await getSellerRating(seller.data.seller_id);
  seller.data.rating = rating;

  return seller.data;
}

export async function getSingleAd(slug) {
  let ad = await fetch(`/c2c-commerce-site/api/listings/${slug}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch listing');
      return response.json();
    });
  return ad.data;
}
export async function getSellerRating(id) {
  let respone = await fetch(`/c2c-commerce-site/api/sellers/rating?id=${id}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch listing');
      return response.json();
    });
  return respone.data;
}
export async function getSingleAdRating(id) {

  let rating = await fetch(`/c2c-commerce-site/api/listings/rating?id=${id}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch listing');
      return response.json();
    });
  return rating.data;


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
  let reviews = await fetch(`/c2c-commerce-site/api/listings/reviews?id=${id}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch listing');
      return response.json();
    });
  return reviews.data;
}
