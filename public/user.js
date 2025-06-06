import { checkout, getCart, getListing, getLocalData, storeLocalData, getOrder, getOrders, getSessionData, getUrlParams, getUserInfo, isLoggedIn, navigateWindow, removeCartItem, storeSessionData, updatePassword, updateUserInfo, getResource } from "./script.js";
import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm';

async function loadSection(section) {
  const content = document.getElementById('main-content');

  if (section === 'user-info') {
    await loadUser();
  } else if (section === 'orders') {
    await loadOrders(content);
  } else if (section === 'cart') {
    await loadCart(content);
  }
}

async function loadUser() {
  const content = document.getElementById('main-content');
  content.innerHTML = '';

  try {
    const user = getLocalData("user") ?? await getUserInfo();;
    console.log(user);
    storeLocalData('user', user);

    const userCard = document.createElement('div');
    userCard.className = 'card p-4';

    userCard.innerHTML = `
      <h4 class="mb-3">User Profile</h4>
      <div class="mb-2"><strong>Name:</strong> ${user.name}</div>
      <div class="mb-2"><strong>Email:</strong> ${user.email}</div>
      <div class="mb-3"><strong>Contact:</strong> ${user.contact}</div>
      
      <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" id="edit-profile-btn">Edit Profile</button>
        <button class="btn btn-secondary btn-sm" id="change-password-btn">Change Password</button>
      </div>
    `;

    content.appendChild(userCard);

    document.getElementById('edit-profile-btn').addEventListener('click', () => {
      loadEditProfile();
    });

    document.getElementById('change-password-btn').addEventListener('click', () => {
      loadChangePassword();
    });

  } catch (err) {
    content.innerHTML = `<div class="alert alert-danger">Failed to load user info</div>`;
  }
}



async function loadOrders(container) {
  container.innerHTML = '';

  const orders = await getOrders();

  if (!orders || orders.length === 0) {
    container.innerHTML = `
    <div class="alert alert-info text-center mt-4">
      You have no orders at the moment.
    </div>
  `;
  }
  const grouped = {};
  orders.forEach(order => {
    if (!grouped[order.status]) grouped[order.status] = [];
    grouped[order.status].push(order);
  });

  // Sort statuses: paid first, others after alphabetically
  const sortedStatuses = Object.keys(grouped).sort((a, b) => {
    return a.localeCompare(b);
  });

  sortedStatuses.forEach((status, index) => {
    const group = grouped[status];
    const collapseId = `collapse-${status.replace(/\s+/g, '-')}-${index}`;
    const section = document.createElement('div');
    section.className = 'mb-3';

    section.innerHTML = `
<div class="accordion-item border-top border-bottom border-3 rounded-3 mb-4 shadow">
  <h2 class="accordion-header">
    <button class="accordion-button bg-light fw-bold text-primary fs-4 py-4 collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#${collapseId}">
      ${status.charAt(0).toUpperCase() + status.slice(1)} Orders (${group.length})
    </button>
  </h2>
  <div id="${collapseId}" class="accordion-collapse collapse">
    <div class="accordion-body bg-white fs-5 py-3" id="${collapseId}-body">
      <!-- Orders will be inserted here -->
    </div>
  </div>
</div>
`;

    const body = section.querySelector(`#${collapseId}-body`);
    group.forEach(order => {
      const orderDiv = document.createElement('div');
      orderDiv.className = 'card mb-2 p-2';

      orderDiv.innerHTML = `
        <h5>Order #${order.order_id}</h5>
        <p>Status: <strong>${order.status}</strong></p>
        <p>Total: R${order.total_amount}</p>
        <p>Created at: ${order.created_at}</p>
        <button class="btn btn-sm btn-outline-primary view-order-btn" data-id="${order.order_id}">View Order</button>
        ${order.status !== 'paid'
          ? `<button class="btn btn-sm btn-success ms-2 pay-order-btn" data-id="${order.order_id}">Pay Now</button>`
          : ''}
      `;

      body.appendChild(orderDiv);
      storeSessionData(`o${order.order_id}`, order);
    });

    container.appendChild(section);
  });

  document.querySelectorAll('.view-order-btn').forEach(button => {
    button.addEventListener('click', async function (e) {
      const orderId = parseInt(e.target.dataset.id);
      await loadOrderDetail(orderId);
    });
  });

  document.querySelectorAll('.pay-order-btn').forEach(button => {
    button.addEventListener('click', function (e) {
      const orderId = parseInt(e.target.dataset.id);
      navigateWindow(`pay?order=${orderId}`);
    });
  });
}



export async function loadOrderDetail(orderId) {
  const container = document.getElementById('main-content');
  const order = await getOrder(orderId);
  const order_info = getSessionData(`o${orderId}`);
  console.log(order);

  if (!order || !order_info) {
    container.innerHTML = '<div class="alert alert-danger">Order not found.</div>';
    return;
  }

  container.innerHTML = '';
  const header = document.createElement('div');
  header.className = 'mb-4';
  header.innerHTML = `
    <h3 class="mb-2">Order Summary</h3>
    <p><strong>Status:</strong> ${order_info.status}</p>
    <p><strong>Total:</strong> R${order_info.total_amount}</p>
    <p><strong>Date:</strong> ${order_info.created_at || 'N/A'}</p>
  `;
  container.appendChild(header);

  const itemList = document.createElement('div');
  itemList.className = 'row g-3';

  for (const item of order.items) {
    const listing = await getListing(item.listing_id);

    const card = document.createElement('div');
    card.className = 'col-12';
    card.innerHTML = `
      <div class="card p-3">
        <div class="row g-3">
          <div class="col-md-8">
            <h5>${listing.title}</h5>
          </div>
          <div class="col-md-4">
            <p><strong>Price:</strong> R${item.price}</p>
            <p><strong>Quantity:</strong> ${item.quantity}</p>
            <p><strong>Subtotal:</strong> R${item.subtotal}</p>
          </div>
        </div>
      </div>
    `;
    itemList.appendChild(card);
  }

  container.appendChild(itemList);

  // Pay button if order not paid
  if (order_info.status !== 'paid') {
    const payBtn = document.createElement('button');
    payBtn.className = 'btn btn-success mt-4 me-2';
    payBtn.textContent = 'Pay Now';
    payBtn.onclick = () => navigateWindow(`pay?order=${orderId}`);
    container.appendChild(payBtn);
  }

  const backBtn = document.createElement('button');
  backBtn.className = 'btn btn-secondary mt-4';
  backBtn.textContent = 'Back to Orders';
  backBtn.onclick = () => loadSection('orders');

  container.appendChild(backBtn);
}

async function loadCart(container) {
  container.innerHTML = '';

  const cart = await getCart(); // fetch full cart object
  const cartItems = cart.cart_items;

  // If cart is empty
  if (!cartItems || Object.keys(cartItems).length === 0) {
    container.innerHTML = `
      <div class="text-center my-5">
        <h3>Your cart is empty 🛒</h3>
        <p>Looks like you haven't added anything yet.</p>
        <a href="/browse" target="_blank" class="btn btn-primary mt-3">Start Shopping</a>
      </div>
    `;
    return;
  }

  let total = 0;

  for (const [listingId, quantity] of Object.entries(cartItems)) {
    const listing = await getListing(parseInt(listingId)); // fetch listing details
    const price = parseFloat(listing.price);
    const subtotal = price * quantity;
    total += subtotal;

    const itemDiv = document.createElement('div');
    itemDiv.className = 'card mb-2 p-2';

    itemDiv.innerHTML = `
      <h5>${listing.title}</h5>
      <p>Price: R${price.toFixed(2)}</p>
      <p>Quantity: ${quantity}</p>
      <p>Subtotal: R${subtotal.toFixed(2)}</p>
      <button class="btn btn-sm btn-outline-primary view-item-btn" data-slug="${listing.slug}">View Item</button>
      <button class="btn btn-sm btn-outline-danger remove-item-btn" data-id="${listing.listing_id}">Remove</button>
    `;

    container.appendChild(itemDiv);
    storeSessionData(`c${listing.listing_id}`, { ...listing, quantity });
  }

  // Display total
  const totalDiv = document.createElement('div');
  totalDiv.className = 'mt-3 fw-bold';
  totalDiv.textContent = `Total: R${total.toFixed(2)}`;
  container.appendChild(totalDiv);

  // Attach event handlers
  document.querySelectorAll('.view-item-btn').forEach(button => {
    button.addEventListener('click', async (e) => {
      const slug = e.target.dataset.slug;
      navigateWindow(`ads/${slug}`)
    });
  });

  document.querySelectorAll('.remove-item-btn').forEach(button => {
    button.addEventListener('click', async (e) => {
      const listingId = parseInt(e.target.dataset.id);
      await removeCartItem(listingId);
      await loadCart(container); // Reload cart view
    });
  });

  handleCheckout(container);
}

function handleCheckout(container) {

  const checkoutDiv = document.createElement('div');
  checkoutDiv.className = 'text-end mt-3'; // aligns button to right

  checkoutDiv.innerHTML = `
  <button id="checkout-btn" class="btn btn-success">
    Proceed to Checkout
  </button>
`;

  container.appendChild(checkoutDiv);

  document.getElementById('checkout-btn').addEventListener('click', async () => {
    try {
      await checkout();
      Swal.fire({
        icon: 'success',
        title: 'Order created'
      });

      loadCart(container);
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Failed to create order'
      });
    }

    loadCart(container);
  });
}

async function loadEditProfile() {
  const container = document.getElementById('main-content');
  const user = await getUserInfo();

  container.innerHTML = `
    <h3>Edit Profile</h3>
    <form id="edit-profile-form" class="mb-4">
      <div class="mb-3">
        <label for="contact" class="form-label">Contact</label>
        <input type="tel" id="contact" class="form-control" value="${user.contact}" required>
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  `;

  document.getElementById('edit-profile-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const updated = {
      contact: document.getElementById('contact').value,
    };
    try {
      await updateUserInfo(updated);
      Swal.fire('Success', 'Profile updated successfully', 'success');
    } catch (err) {
      Swal.fire('Error', err.message || 'Failed to update profile', 'error');
    }
  });
}

async function loadChangePassword() {
  const container = document.getElementById('main-content');

  container.innerHTML = `
    <h3>Change Password</h3>
    <form id="change-password-form">
      <div class="mb-3">
        <label for="current-password" class="form-label">Current Password</label>
        <input type="password" id="current-password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="new-password" class="form-label">New Password</label>
        <input type="password" id="new-password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="confirm-password" class="form-label">Confirm New Password</label>
        <input type="password" id="confirm-password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
  `;

  document.getElementById('change-password-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const current = document.getElementById('current-password').value;
    const newPass = document.getElementById('new-password').value;
    const confirm = document.getElementById('confirm-password').value;

    if (newPass !== confirm) {
      Swal.fire('Error', 'New password and Confirm are not the same', 'error');
      return;
    }

    try {
      await updatePassword(current, newPass);
      Swal.fire('Success', 'Password updated successfully', 'success');
    } catch (err) {
      Swal.fire('Error', err.message || 'Failed to update password', 'error');
    }
  });
}

async function logout() {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'This action will log you out.',
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Logout',
    cancelButtonText: 'Cancel'
  });

  if (!result.isConfirmed) return;

  try {
    await getResource('logout', 'POST');
    localStorage.clear();
    Swal.fire('Logged out!', '', 'success');
    navigateWindow('browse');;
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}

function initPage() {
  document.getElementById('btn-profile').onclick = () => loadSection('user-info');
  document.getElementById('btn-orders').onclick = () => loadSection('orders');
  document.getElementById('btn-cart').onclick = () => loadSection('cart');
  document.getElementById('btn-logout').onclick = () => logout();

  const sec = getUrlParams().get('sec') ?? 'user-info';

  loadSection(sec);
}

document.addEventListener("DOMContentLoaded", async function () {
  const isIn = await isLoggedIn();
  if (!isIn) {
    navigateWindow('login');
  }
  initPage();
});

