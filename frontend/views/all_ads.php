<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <base href="/c2c-commerce-site/public/">
  <title>Ad listings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

  <?php include('navbar.html'); ?>
  <form class="row g-2 my-2 mx-2" role="search" id="search-bar">
    <div class="col-11">
      <input class="form-control" type="search" name="q" placeholder="Search ads...">
    </div>
    <div class="col-1">
      <button class="btn btn-success w-100" type="submit">Search</button>
    </div>
  </form>
  <div class="container my-5 mx-1">
    <div class="card text-center">
      <div class="card-body" id="page-body">
        <p class="mb-0">Loading resource...</p>
      </div>
    </div>
  </div>
  <script type="module" src="all.js"></script>
  <script src="js/navbar.js"></script>
</body>

</html>
