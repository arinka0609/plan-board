<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="../assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Главная</title>
    <link rel="stylesheet" href="assets/carousel.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard.css">


    
    <!-- Custom styles for this template -->


  </head>
  <body>
    
  <div class="loader show">
  <div class="loader-inner">
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
  </div>
</div>
<header data-bs-theme="dark">
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Plan Board</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <form class="d-flex" role="search">
          <a class="btn btn-outline-success" href="/index1.php">Войти</a></p>
        </form>
      </div>
    </div>
  </nav>
</header>

<main>
<section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light">Plan Board</h1>
        <p class="lead text-body-secondary">Все планы на одной доске,теперь это просто! Заходите и создавайте свои мероприятия для дальнейшего планирования.</p>
      </div>
    </div>
  </section>


  
<div id="app-block3">
    <!-- Карусель -->
    <div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div v-for="(news, index) in newsList" :key="index" class="carousel-item" :class="{ active: index === 0 }">
                <img :src="news.image" alt="Slide {{ index + 1 }}" class="d-block w-100">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden"></span>
        </button>
    </div>





<div class="container marketing">
    <!-- Блоки мини новостей -->
    <div class="row">
        <div class="col-lg-4" v-for="(news, index) in newsList" :key="index">
            <img :src="news.image" class="bd-placeholder-img rounded-circle" width="140" height="140" alt="Placeholder">
            <h2 class="fw-normal">{{ news.title }}</h2>
            <p>{{ news.description }}</p>
            <button class="btn btn-secondary" @click="goToSlide(index)">Увидеть детали &raquo;</button>
        </div><!-- /.col-lg-4 -->
    </div><!-- /.row -->
</div>
</div>
    

  <!-- ПОДВАЛ -->


</main>
<?php include 'script.php'; ?>
<?php include 'symbol.php'; ?>
<?php include 'color.php'; ?>
<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>