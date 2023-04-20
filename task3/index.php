<?php

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (!empty($_GET['save'])) {
    print('
      <p>
        Спасибо, результаты сохранены.
      </p>
      ');
  }
  include('form.php');
  exit();
}

$name = $_POST['name'];
$email = $_POST['email'];
$year = $_POST['year'];
$gender = $_POST['gender'];
$hand = $_POST['hand'];
if(isset($_POST["abilities"])) {
  $abilities = $_POST["abilities"];
  $filtred_abilities = array_filter($abilities, 
  function($value) {
    return($value == 1 || $value == 2 || $value == 3);
  }
  );
}
$biography = $_POST['biography'];
$checkboxContract = isset($_POST['checkboxContract']);

$errors = FALSE;

if (empty($name)) {
  print('
    <h1>
      Заполните имя.
    </h1>
  <br/>');
  $errors = TRUE;
}

if (empty($email)) {
  print('
    <h1>
      Заполните email.
    </h1>
  <br/>');
  $errors = TRUE;
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  print('
    <h1>
      Корректно* заполните email.
    </h1>
  <br/>');
  $errors = TRUE;
}

if (!is_numeric($year)) {
  print('
    <h1>
      Неправильный формат ввода года.
    </h1>
  <br/>');
  $errors = TRUE;
} else if ((2023 - $year) < 16) {
  print('
    <h1>
      Извините, вам должно быть 16 лет.
    </h1>
  <br/>');
  $errors = TRUE;
}

if ($gender != 'male' && $gender != 'female') {
  print('
    <h1>
      Выбран неизвестный пол.
    </h1>
  <br/>');
  $errors = TRUE;
}

if ($hand != 'right' && $hand != 'left') {
  print('
    <h1>
      Выбрана неизвестная рука.
    </h1>
  <br/>');
  $errors = TRUE;
}

if (empty($abilities)) {
  print('
    <h1>
      Выберите хотя бы одну сверхспособность.
    </h1>
  <br/>');
  $errors = TRUE;
} else if (count($filtred_abilities) != count($abilities)) {
  print('
    <h1>
      Выбрана неизвестная сверхспособность.
    </h1>
  <br/>');
  $errors = TRUE;
}

if (empty($biography)) {
  print('
    <h1>
      Расскажи о себе что-нибудь.
    </h1>
  <br/>');
  $errors = TRUE;
} else if (!preg_match('/^[\p{Cyrillic}\d\s,.!?-]+$/u', $biography)) {
  print('
    <h1>
      Недопустимый формат ввода биографии.
    </h1>
  <br/>');
  $errors = TRUE;
} 

if ($checkboxContract == '') {
  print('
    <h1>
      Ознакомьтесь с контрактом.
    </h1>
  <br/>');
  $errors = TRUE;
}

if ($errors) {
  exit();
}

$user = 'u52893';
$pass = '1125300';
$db = new PDO('mysql:host=localhost;dbname=u52893', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

try {
  $stmt = $db->prepare("INSERT INTO application (name, email, year, gender, hand, biography) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([$name, $email, $year, $gender, $hand, $biography]);
  $application_id = $db->lastInsertId();
  $stmt = $db->prepare("INSERT INTO abilities (application_id, superpower_id) VALUES (?, ?)");
  foreach ($abilities as $superpower_id) {
    $stmt->execute([$application_id, $superpower_id]);
  }
} catch (PDOException $e) {
  print('Error : ' . $e->getMessage());
  exit();
}
header('Location: ?save=1');
?>