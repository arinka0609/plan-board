<?php
require_once __DIR__ . '/src/helpers.php';
checkAuth();

$user = currentUser();
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="../assets/js/color-modes.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Страница пользователя</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/album/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
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

  
  <nav class="navbar navbar-dark bg-dark" aria-label="First navbar example" id="openModal">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">PlanBoard</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample01" aria-controls="navbarsExample01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample01">
        <ul class="navbar-nav me-auto mb-2">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="<?php echo $link; ?>">Главная</a>
          </li>
          <li class="nav-item">
          <a class="nav-link" href="#" @click="openChat2Modal">Профиль</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" @click="openChatModal">Сообщения</a>
          </li>
          <li class="nav-item">
          <form id="logout-form" action="src/actions/logout.php" method="post" style="display: none;">
            <button type="submit" id="logout-button"></button>
        </form>
              <a href="#" class="nav-link d-flex align-items-center gap-2" onclick="document.getElementById('logout-button').click(); return false;">
            Выйти</a>
          </li>
        </ul>
      </div>
    </div>
    
    <?php include 'header.php'; ?>
  </nav>

<main>

  <section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light">Plan Board</h1>
        <p class="lead text-body-secondary">Здесь вы увидите всю важную информацию</p>
        <p>
          <a href="#app-block1" class="btn btn-primary my-2">Приступить</a>
        </p>
      </div>
    </div>
  </section>
  

 
  <div class="album py-5 bg-body-tertiary">
    <div class="container">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="app-block1">
            <div v-for="(block, index) in blocks" :key="block.id" class="col">
                <div class="col mb-5" v-if="blocks.length === 0">
                    <h2 style="color: Gray">Нет доступных досок...</h2>
                </div>
                <div class="card shadow-sm">
                    <div class="card-img-top-wrapper">
                        <div class="card-img-top" :style="{ backgroundColor: block.color }">
                            <span class="text">{{ block.title }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text"></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="redirectToSpecificPage(block.id)">Перейти</button>
                            </div>
                            <small class="text-body-secondary">{{ block.date }} 
                            <i class="fas fa-bell" :class="{ 'text-danger': notifications[block.id], 'cursor-pointer': !notifications[block.id] }" @click="openModal(block.id)"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-container3" v-if="isModalOpen">
            <div class="custom-modal-content">
                <span class="custom-modal-close" @click="closeModal">&times;</span>
                <div v-if="lastContent">
                    <p>{{ lastContent.date }} была добавлена новая информация и ее тема: {{ lastContent.title }}</p>
                </div>
                <div v-else>
                    <p>Нет данных для отображения.</p>
                </div>
            </div>
        </div>
        </div>
        </div>
    </div>
        </div>
    </div>
</div>


</main>

  


<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>




<?php include 'footer.php'; ?>
<?php include 'symbol.php'; ?>
<?php include 'color.php'; ?>
<?php include 'script.php'; ?>

</body>
</html>