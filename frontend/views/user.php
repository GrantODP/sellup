<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="/public/">
  <title>User Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>
  <?php include('navbar.html'); ?>

  <div class="cotainer-fluid">
    <div class="row min-vh-100">
      <nav class="col-md-3 col-lg-2 d-md-block bg-dark py-4 pe-0">
        <div class="d-grid">
          <button id="btn-profile"
            class=" btn btn-primary w-100 text-start border-top border-bottom border-0 rounded-0 text-white bg-transparent">
            Profile
          </button>
          <button id="btn-orders"
            class=" btn btn-outline-light w-100 text-start border-top border-bottom border-0 rounded-0 text-white bg-transparent">
            Orders
          </button>
          <button id="btn-cart"
            class=" btn btn-outline-light w-100 text-start border-top border-bottom border-0 rounded-0 text-white bg-transparent">
            Cart
          </button>
          <a id="btn-seller" href='/c2c-commerce-site/seller'
            class=" btn btn-outline-light w-100 text-start border-top border-bottom border-0 rounded-0 text-white bg-transparent">
            Seller info
          </a>
        </div>
      </nav>
      <!-- Main content -->
      <main id="main-content" class=" col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      </main>
    </div>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>
  <script type="module" src="user.js"></script>
  <script src="js/navbar.js"></script>
</body>

</html>
