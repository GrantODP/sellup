

const BASE_API = "/api/v1/admin";

function storeSessionData(key, data) {
  sessionStorage.setItem(key, JSON.stringify(data));
}

function getSessionData(key) {
  return JSON.parse(sessionStorage.getItem(key));
}

async function getResource(uri, method = 'GET', data = null, headers = {}, overwrite_url = false) {
  let url = '';
  if (overwrite_url) {
    url = uri;
  } else {

    url = `${BASE_API}/${uri}`;
  }
  const is_form = data instanceof FormData;
  const options = {
    method: method.toUpperCase(),
    headers: is_form ? headers : {
      'Content-Type': 'application/json',
      ...headers
    }
  };
  if (data && method.toUpperCase() !== 'GET') {
    options.body = is_form ? data : JSON.stringify(data);
  }
  const response = await fetch(url, options);
  const json = await response.json();

  if (!response.ok) {
    const message = json.message;
    throw new Error(message);
  }

  return json.data;

}

function navigateWindow(page) {
  return window.location.href = `/${page}`;
}

function showSection(id) {
  const sections = document.querySelectorAll('.admin-section');
  sections.forEach(section => {
    section.style.display = section.id === id ? 'block' : 'none';
  });

  if (id == 'categories') loadCats();

}
async function loadCats() {
  const cats = await getResource('/api/v1/categories', 'GET', null, {}, true);
  storeSessionData('cats', cats);
}
async function searchUser() {
  console.log('searching user');
  const query = document.getElementById('user-search').value.toLowerCase().trim();
  if (query) {
    await displayUser(query);
    return;
  }
}

async function searchSeller() {
  const query = document.getElementById('seller-search').value.toLowerCase().trim();
  if (query) {
    displaySeller(query);
    return;
  }
}

async function displayUser(email = '') {
  const container = document.getElementById('user-list');
  container.innerHTML = '';
  if (!email)
    return;
  try {
    const user = await getResource(`users?email=${email}`);
    if (!user) {
      Swal.fire('Not found', "User not found", 'info');
      return;
    }

    const card = document.createElement('div');
    card.className = 'col-md-4';
    card.innerHTML = `
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">${user.name}</h5>
            <p class="card-text">
              <strong>Id:</strong> ${user.id}<br>
              <strong>Email:</strong> ${user.email}<br>
              <strong>Contact:</strong> ${user.contact}
            </p>
            <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">Delete</button>
          </div>
        </div>
      `;

    container.appendChild(card);

  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}



async function deleteUser(id) {
  try {
    await getResource(`users?id=${id}`, 'DELETE');
    Swal.fire('Deleted!', 'User was successfully deleted.', 'success');
    displayUser('');
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}

async function displaySeller(uid = '') {
  const container = document.getElementById('sellers-list');
  container.innerHTML = '';
  if (!uid)
    return;
  try {
    const seller = await getResource(`sellers?uid=${uid}`);
    if (!seller) {
      Swal.fire('Not found', "User not found", 'info');
      return;
    }
    const card = document.createElement('div');
    card.className = 'col-md-4';

    card.innerHTML = `
        <div class="card shadow-sm">
          <div class="card-body">
            <p class="card-text">
              <strong>Seller Id:</strong> ${seller.seller_id}<br>
              <strong>Verification:</strong> ${seller.verification}<br>
              <strong>Start Selling:</strong> ${seller.created_at}
            </p>
          </div>
        </div>
      `;

    container.appendChild(card);

  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}

async function deleteListing() {
  const id = document.getElementById('deleteListingId').value;
  try {
    await getResource(`listings?id=${id}`, 'DELETE');
    Swal.fire('Deleted!', 'Listing was successfully deleted.', 'success');
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}


async function verifySeller() {
  const status = document.getElementById('verify-status').value;
  const sid = document.getElementById('verify-id').value;
  try {
    await getResource('seller/verification', 'POST', { id: sid, status: status });
    Swal.fire('Updated!', `Updated seller to ${status}`, 'success');
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}
async function updatePassword() {
  const uid = document.getElementById('passwordUserId').value;
  const password = document.getElementById('newPassword').value;

  if (!password || !uid) {

    Swal.fire('Empty fields', "Missing fields", 'warn');
    return;
  }
  try {
    await getResource('user/password', 'POST', { id: uid, password: password });
    Swal.fire('Updated!', `Updated password`, 'success');
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}
async function addCategory() {
  const name = document.getElementById('catName').value;
  const descp = document.getElementById('catDescp').value;
  const id = document.getElementById('catId').value;

  if (!name || !descp) {
    Swal.fire('Empty fields', "Missing fields", 'warn');
    return;
  }
  if (id) {
    Swal.fire('Id', "Id must be empty", 'warn');
    return;
  }
  try {
    await getResource('categories', 'POST', { name: name, descp: descp });
    Swal.fire('Updated!', `Added category`, 'success');
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}
async function updateCategory() {
  const name = document.getElementById('catName').value;
  const descp = document.getElementById('catDescp').value;
  const id = document.getElementById('catId').value;

  if (!name || !descp || !id) {
    Swal.fire('Empty fields', "Missing fields", 'warn');
    return;
  }
  try {
    await getResource('categories', 'PUT', { id: id, name: name, descp: descp });
    Swal.fire('Updated!', `Updated category`, 'success');
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}
async function fillCategory() {
  const name = document.getElementById('catName');
  const descp = document.getElementById('catDescp');
  const id = document.getElementById('catId').value;

  const categories = getSessionData('cats');

  if (!categories || !Array.isArray(categories)) {
    return;
  }

  const selected = categories.find(cat => String(cat.cat_id) === id);

  if (selected) {
    name.value = selected.name || '';
    descp.value = selected.description || '';
  } else {
    name.value = '';
    descp.value = '';
    console.warn('Category not found for ID:', id);
  }
}
async function isLoggedIn() {
  const token = await getResource('/api/v1/auth/status', 'GET', null, {}, true);
  return token == 'valid';

}

document.addEventListener("DOMContentLoaded", async function () {
  const isIn = await isLoggedIn();
  if (!isIn) {
    navigateWindow('login');
  }

  document.getElementById('searchSeller').addEventListener('submit', async function (e) {
    e.preventDefault();
    await searchSeller();
  });
  document.getElementById('searchUser').addEventListener('submit', async function (e) {
    e.preventDefault();
    await searchUser();
  });
  showSection('users');
});

