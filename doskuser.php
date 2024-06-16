<?php
require_once __DIR__ . '/src/helpers.php';
checkAuth();

$user = currentUser();

// Подключение к базе данных и проверка сессии block_id
session_start();
// Устанавливаем информацию о пользователе в сессию
$_SESSION['user']['id'] = $user['id'];
$_SESSION['user']['role'] = $user['role'];
$_SESSION['user']['unique_id'] = $user['unique_id'];
// Определяем ссылку в зависимости от роли пользователя
switch ($_SESSION['user']['role']) {
    case 1:
        $link = '/home.php';
        break;
    case 2:
        $link = '/homeruk.php';
        break;
    case 3:
        $link = '/homeuser.php';
        break;
    default:
        $link = '/'; // Перенаправляем на главную страницу или страницу ошибки
        break;
}

function encryptMessage($message, $key) {
  $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
  $ciphertext = sodium_crypto_secretbox($message, $nonce, $key);
  return base64_encode($nonce . $ciphertext);
}

// Функция для расшифрования текста с использованием библиотеки Sodium
function decryptMessage($encryptedMessage, $key) {
  $decoded = base64_decode($encryptedMessage);
  $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
  $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
  return sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
}

// Получаем blockId из сессии
$blockId = isset($_SESSION['blockId']) ? $_SESSION['blockId'] : '';






// Подключение к базе данных
require_once __DIR__ . '/src/config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Подготовленный запрос к базе данных для получения данных для текущего block_id
$sql = "SELECT * FROM info WHERE block_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blockId);
$stmt->execute();
$result = $stmt->get_result();
?>

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
<link href="assets/style.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
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
      </div>
    </div>
  </section>
  <hr class="hr-dashed">
  <div class="like-list-modal info-like-list-modal modal">
                                <div class="modal-content">
                                    <p>Комментарий понравился:</p>
                                    <span class="close">&times;</span>
                                    <div id="infoLikeList" class="info-like-list"></div>
                                </div>
                            </div>
  <div class="container-fluid">
    <div class="row justify-content-end">
        <?php
        $blocks = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ob_start();
                $file = $row['file'];
                $fileOutput = '';

                // Check if file is an image
                if (preg_match('/\.(jpeg|jpg|gif|png)$/i', $file)) {
                    $fileOutput = '<div class="photo" style="background-image: url(\'' . $file . '\')"></div>';
                }
                // Check if file is a URL
                elseif (filter_var($file, FILTER_VALIDATE_URL)) {
                    // Check if the URL is a YouTube or Vimeo link for thumbnail
                    if (preg_match('/(youtube\.com|youtu\.be)/', $file)) {
                        // Extract video ID and generate thumbnail URL
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $file, $match);
                        if (!empty($match[1])) {
                            $videoId = $match[1];
                            $thumbnailUrl = 'https://img.youtube.com/vi/' . $videoId . '/0.jpg';
                            // Add link wrapper around photo for video
                            if (!empty($thumbnailUrl)) {
                                $fileOutput = '<a href="' . $file . '" target="_blank" class="video-link">' . '<div class="photo" style="background-image: url(\'' . $thumbnailUrl . '\')"></div>' . '</a>';
                            }
                        }
                    } elseif (preg_match('/vimeo\.com/', $file)) {
                        // Vimeo thumbnails require an API call, using a placeholder for brevity
                        $fileOutput = '<div class="photo" style="background-image: url(\'default_vimeo_thumbnail.jpg\')"></div>';
                    } else {
                        // Default link handling for external URLs
                        $fileOutput = '<a href="' . $file . '" target="_blank"><i class="fas fa-external-link-alt"></i></a>';
                    }
                }
                // Check if file is PDF, DOCX, or other files
                elseif (preg_match('/\.(pdf|docx|doc|txt)$/i', $file)) {
                    // Choose appropriate icon based on file extension
                    $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                    switch ($file_extension) {
                        case 'pdf':
                            $icon = '<i class="far fa-file-pdf"></i>';
                            break;
                        case 'docx':
                            $icon = '<i class="far fa-file-word"></i>';
                            break;
                        // Add more cases for other file types if needed
                        default:
                            $icon = '<i class="far fa-file"></i>';
                    }
                    $fileOutput = '<a href="' . $file . '" target="_blank">' . $icon . '</a>';
                }
                // Assume any other file type should be linked to
                else {
                    $fileOutput = '<a href="' . $file . '" target="_blank"><i class="far fa-file"></i></a>';
                }

// Преобразуем дату из формата строки в формат временной метки
$date1Timestamp = strtotime($row['date1']);
// Вычисляем количество секунд до указанной даты
$timeUntilDate1 = $date1Timestamp - time();

// Вычисляем количество дней до указанной даты
$daysUntilDate1 = floor($timeUntilDate1 / (60 * 60 * 24));

// Определяем цвет в зависимости от количества дней
if ($daysUntilDate1 >= 5) {
    $color = "green"; // зеленый цвет, если остается 5 и более дней
} elseif ($daysUntilDate1 >= 3) {
    $color = "orange"; // оранжевый цвет, если остается от 3 до 4 дней
} else {
    $color = "red"; // красный цвет, если остается менее 3 дней
}


                ?>
                <div class="blog-card col-12 col-md-7 mt-4" id="<?php echo $row['id']; ?>"> <!-- Используем id из БД -->
                    <div class="meta">
                        <?php echo $fileOutput; ?>
                    </div>
                    <div class="description">
                        <h1><?php echo $row['title']; ?></h1>
                        <h2><?php echo $row['date']; ?></h2>
                        <p>Выполнить до <span style="color: <?php echo $color; ?>"><i class="fas fa-clock" style="color: <?php echo $color; ?>"></i> <?php echo $row['date1']; ?></span></p>
                        <p><?php echo $row['description']; ?></p>
                        <p class="read-more">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control comment-input" placeholder="Напишите ваш комментарий" aria-label="Напишите ваш комментарий" aria-describedby="button-addon2">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="unique_id" value="<?php echo $user['unique_id']; ?>">
                                <button class="btn btn-outline-secondary submit-comment" type="button" id="button-addon2">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </p>
                        <div class="info-actions">
                            <span class="info-like-icon" data-info-id="<?php echo $row['id']; ?>">
                                <i class="fas fa-thumbs-up"></i> 
                            </span>
                            <span class="info-like-count" data-info-id="<?php echo $row['id']; ?>"><?php echo $row['like_count']; ?></span>
                        </div>
                    </div>
                </div>
                <div id="container">
                    <ul id="chat">
                        <?php
                        // Получение комментариев для данного блока контента
                        $commentsQuery = "SELECT * FROM comments WHERE info_id = {$row['id']} ORDER BY date DESC, com_id DESC";
                        $commentsResult = $conn->query($commentsQuery);
                        $comments = []; // Массив для хранения комментариев
                        if ($commentsResult->num_rows > 0) {
                            while ($comment = $commentsResult->fetch_assoc()) {
                                $comments[] = $comment; // Добавляем комментарий в массив
                            }
                            foreach ($comments as $comment) {
                                // Получение имени пользователя
                                $userId = $comment['us_id'];
                                $userNameQuery = "SELECT name FROM users WHERE unique_id = '{$userId}'";
                                $userNameResult = $conn->query($userNameQuery);
                                if ($userNameResult->num_rows > 0) {
                                    $userName = $userNameResult->fetch_assoc()['name'];
                                } else {
                                    // Если имя пользователя не найдено, можно использовать значение по умолчанию или какую-то другую логику
                                    $userName = "Unknown";
                                }

                                // Получение пути к аватару пользователя
                                $userAvatarQuery = "SELECT avatar FROM users WHERE unique_id = '{$userId}'";
                                $userAvatarResult = $conn->query($userAvatarQuery);
                                if ($userAvatarResult->num_rows > 0) {
                                    $userAvatar = $userAvatarResult->fetch_assoc()['avatar'];
                                } else {
                                    // Если аватар пользователя не найден, использовать путь к заглушке или другое дефолтное изображение
                                    $userAvatar = "path_to_default_avatar.jpg";
                                }
                                ?>
                                <li class="you">
                                    <div class="entete">
                                        <!-- Отображение аватара пользователя -->
                                        <img src="<?php echo $userAvatar; ?>" alt="avatar" class="avatar">
                                        <h2><?php echo $userName; ?></h2>
                                        <h2><?php echo date('d.m.Y H:i:s', strtotime($comment['date'])); ?></h2>
                                    </div>
                                    <div class="triangle"></div>
                                    <div class="message">
                                        <div class="comment-section">
                                            <span class="comment-info">
                                                <span class="comment-text"><?php echo decryptMessage($comment['com'], base64_decode($comment['com_key'])); ?></span>
                                            </span>
                                            <span class="comment-actions">
                                                <span class="comment-icon like" data-comment-id="<?php echo $comment['com_id']; ?>">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </span>
                                                <span class="comment-count" data-comment-id="<?php echo $comment['com_id']; ?>">
                                                    <?php echo $comment['like_count']; ?>
                                                </span>

                                                <div class="like-list-comment like-list-comment-<?php echo $comment['com_id']; ?>"></div>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Модальное окно -->
                                    <div id="likeModal" class="modal">
                                        <div class="modal-content">
                                            <p>Комментарий понравился:</p>
                                            <span class="close" onclick="closeLikeModal()">&times;</span>
                                            <div id="likeList"></div>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                        } else {
                            echo '<li>Нет комментариев</li>';
                        }
                        ?>
    </ul>
</div>
                <hr class="hr-dashed">
        <?php
                $blocks[] = ob_get_clean();
            }
        }
        // Вывод блоков в обратном порядке
        if (!empty($blocks)) {
            foreach (array_reverse($blocks) as $block) {
                echo $block;
            }
        } else {
        ?>
            <div class="no-data">
                <h1>Нет данных для отображения</h1>
            </div>
        <?php
        }
        ?>
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


