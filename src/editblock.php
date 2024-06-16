<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$response = [];

// Проверяем наличие обязательных данных и id блока
if (isset($_POST['block_id'], $_POST['title'], $_POST['color'], $_POST['color1'], $_POST['userIds'])) {
    $block_id = $_POST['block_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $color = $conn->real_escape_string($_POST['color']);
    $color1 = $conn->real_escape_string($_POST['color1']); // Добавлено новое поле color1
    $userIds = $_POST['userIds']; // Массив идентификаторов пользователей

    // Обновляем запись в таблице block
    $sql = "UPDATE block SET title=?, color=?, color1=? WHERE id=?";
    $stmt_block = $conn->prepare($sql);
    if (!$stmt_block) {
        $response['error'] = "Ошибка подготовки запроса: " . $conn->error;
    } else {
        $stmt_block->bind_param("sssi", $title, $color, $color1, $block_id);
        if (!$stmt_block->execute()) {
            $response['error'] = "Ошибка выполнения запроса: " . $stmt_block->error;
        } else {
            // Удаляем старые записи из таблицы user_block для этого блока
            $sql_delete_user_block = "DELETE FROM user_block WHERE block_id=?";
            $stmt_delete_user_block = $conn->prepare($sql_delete_user_block);
            if (!$stmt_delete_user_block) {
                $response['error'] = "Ошибка подготовки запроса: " . $conn->error;
            } else {
                $stmt_delete_user_block->bind_param("i", $block_id);
                if (!$stmt_delete_user_block->execute()) {
                    $response['error'] = "Ошибка выполнения запроса: " . $stmt_delete_user_block->error;
                } else {
                    // Добавляем новые записи в таблицу user_block для этого блока
                    $sql_insert_user_block = "INSERT INTO user_block (user_id, block_id) VALUES (?, ?)";
                    $stmt_insert_user_block = $conn->prepare($sql_insert_user_block);
                    if (!$stmt_insert_user_block) {
                        $response['error'] = "Ошибка подготовки запроса: " . $conn->error;
                    } else {
                        foreach ($userIds as $user_id) {
                            $user_id = intval($user_id); // Преобразуем к целому числу
                            $stmt_insert_user_block->bind_param("ii", $user_id, $block_id);
                            if (!$stmt_insert_user_block->execute()) {
                                $response['error'] = "Ошибка выполнения запроса: " . $stmt_insert_user_block->error;
                                break; // Прерываем цикл в случае ошибки
                            }
                        }
                        $updated_user_ids_str = implode(',', $userIds);
                        $sql_update_block = "UPDATE block SET user_id=? WHERE id=?";
                        $stmt_update_block = $conn->prepare($sql_update_block);
                        if (!$stmt_update_block) {
                            $response['error'] = "Ошибка подготовки запроса: " . $conn->error;
                        } else {
                            $stmt_update_block->bind_param("si", $updated_user_ids_str, $block_id);
                            if (!$stmt_update_block->execute()) {
                                $response['error'] = "Ошибка выполнения запроса: " . $stmt_update_block->error;
                            } else {
                                $response['message'] = "Запись успешно обновлена";
                                $response['title'] = $title;
                                $response['color'] = $color;
                                $response['color1'] = $color1;
                            }
                            $stmt_update_block->close();
                        }
                    }
                }
                $stmt_delete_user_block->close();
            }
        }
    }
} else {
    $response['error'] = "Отсутствуют обязательные данные";
}

echo json_encode($response);

$conn->close();
?>
                        $stmt_insert_user_block->close();
                        $response['message'] = "Запись успешно обновлена";
                        $response['title'] = $title;
                        $response['color'] = $color;
                        $response['color1'] = $color1;
                    }
                }
                $stmt_delete_user_block->close();
            }
        }
        $stmt_block->close();
    }
} else {
    $response['error'] = "Отсутствуют обязательные данные";
}

echo json_encode($response);

$conn->close();
?>