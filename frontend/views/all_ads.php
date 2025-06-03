<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <base href="/public/">
  <title>Ad listings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
  .lift-up {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .lift-up:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  }
</style>

<body class="bg-light">

  <?php include('navbar.html'); ?>
  <form class="row g-2 my-2 mx-2" role="search" id="search-bar">
    <div class="col-11">
      <input class="form-control border border-dark" type="search" name="q" placeholder="Search ads...">
    </div>
    <div class="col-1">
      <button class="btn btn-success w-100" type="submit">Search</button>
    </div>
  </form>
  <div class="container my-5 mx-1">
    <div class="card text-center">
      <div class="card-body" id="page-body">
        <div class="container-fluid mt-5 justify-content-start">
          <div id="category-name" class="mb-5 fw-bold"></div>
          <div id="products-page-container" class="row">
            <!-- Sidebar: Categories -->
            <nav id="category-container" class="col-md-3 mb-4">
              <div class="bg-light rounded p-3 w-100 h-auto">
                <h5 class="mb-3 fw-bold">Categories</h5>
                <ul class="list-group btn-group-vertical " id="category-list">
                </ul>
              </div>
            </nav>
            <div id="products-container" class="col-md-9 mt-0">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="module" src="all.js"></script>
  <script src="js/navbar.js"></script>
</body>

</html>
