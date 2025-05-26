<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Product Listing</title>

  <base href="/public/">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="ad_styles.css">
</head>

<body>
  <div id="navbar"></div>

  <?php include('navbar.html'); ?>
  <div class="product" id="page-body">
    <p>Loading product...</p>
  </div>


  <script type="module" src="ad.js"></script>

  <script src="js/navbar.js"></script>
</body>

</html>
