<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="/c2c-commerce-site/public/">
  <title>User Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<style>
  .side .nav-link:hover {
    background-color: grey;
  }
</style>

<body>
  <?php include('navbar.html'); ?>


  <div class="container-fluid">
    <div class="row min-vh-100">
      <!-- Sidebar -->
      <nav class="col-3 bg-dark text-white p-3">
        <h4 class="text-center mb-4">User Panel</h4>
        <ul class="nav nav-pills flex-column w-100 side">
          <li class="nav-item mb-2">
            <button id="btn-profile" type="button"
              class="btn btn-link nav-link text-white text-start p-2 w-100">Profile</button>
          </li>
          <li class="nav-item mb-2">
            <button id="btn-orders" type="button"
              class="btn btn-link nav-link text-white text-start p-2 w-100">Orders</button>
          </li>
          <li class="nav-item mb-2">
            <button id="btn-cart" type="button"
              class="btn btn-link nav-link text-white text-start p-2 w-100">Cart</button>
          </li>
          <li class="nav-item mb-2">
            <a id="btn-seller" href="/c2c-commerce-site/seller"
              class="btn btn-link nav-link text-white text-start p-2 w-100">Seller info</a>
          </li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main id="main-content" class="col-md-9 ms-sm-auto col-lg-9 px-md-4 py-4">
      </main>
    </div>
  </div>


  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>
  <script type="module" src="user.js"></script>
  <script src="js/navbar.js"></script>
</body>

</html>
