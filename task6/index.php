<?php

include('basic_auth.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT application_id, name, email, year, gender, hand, biography FROM application");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $messages = array();

    $errors = array();
    $errors['error_id'] = empty($_COOKIE['error_id']) ? '' : $_COOKIE['error_id'];
    $errors['name'] = !empty($_COOKIE['name_error']);
    $errors['email1'] = !empty($_COOKIE['email_error1']);
    $errors['email2'] = !empty($_COOKIE['email_error2']);
    $errors['year1'] = !empty($_COOKIE['year_error1']);
    $errors['year2'] = !empty($_COOKIE['year_error2']);
    $errors['gender1'] = !empty($_COOKIE['gender_error1']);
    $errors['gender2'] = !empty($_COOKIE['gender_error2']);
    $errors['hand1'] = !empty($_COOKIE['hand_error1']);
    $errors['hand2'] = !empty($_COOKIE['hand_error2']);
    $errors['abilities1'] = !empty($_COOKIE['abilities_error1']);
    $errors['abilities2'] = !empty($_COOKIE['abilities_error2']);
    $errors['biography1'] = !empty($_COOKIE['biography_error1']);
    $errors['biography2'] = !empty($_COOKIE['biography_error2']);
  
    if (!empty($errors['name'])) {
        setcookie('name_error', '', 100000);
        $messages['name'] = '<p class="msg">Не заполнено поле имени</p>';
    }
    if (!empty($errors['email1'])) {
        setcookie('email_error1', '', 100000);
        $messages['email1'] = '<p class="msg">Не заполнено поле email</p>';
    } else if (!empty($errors['email2'])) {
        setcookie('email_error2', '', 100000);
        $messages['email2'] = '<p class="msg">Некорректно заполнено поле email</p>';
    }
    if (!empty($errors['year1'])) {
        setcookie('year_error1', '', 100000);
        $messages['year1'] = '<p class="msg">Неправильный формат ввода года</p>';
    } else if (!empty($errors['year2'])) {
        setcookie('year_error2', '', 100000);
        $messages['year2'] = '<p class="msg">Выбран возраст менее 14 лет</p>';
    }
    if (!empty($errors['gender1'])) {
        setcookie('gender_error1', '', 100000);
        $messages['gender1'] = '<p class="msg">Не выбран пол</p>';
    }
    if (!empty($errors['gender2'])) {
        setcookie('gender_error2', '', 100000);
        $messages['gender2'] = '<p class="msg">Выбран неизвестный пол</p>';
    }
    if (!empty($errors['hand1'])) {
        setcookie('hand_error1', '', 100000);
        $messages['hand1'] = '<p class="msg">Не выбрана рука</p>';
    }
    if (!empty($errors['hand2'])) {
        setcookie('hand_error2', '', 100000);
        $messages['hand2'] = '<p class="msg">Выбрана неизвестная рука</p>';
    }
    if (!empty($errors['abilities1'])) {
        setcookie('abilities_error1', '', 100000);
        $messages['abilities1'] = '<p class="msg">Не выбрана ни одна сверхспособность</p>';
    } else if (!empty($errors['abilities2'])) {
        setcookie('abilities_error2', '', 100000);
        $messages['abilities2'] = '<p class="msg">Выбрана неизвестная сверхспособность</p>';
    }
    if (!empty($errors['biography1'])) {
        setcookie('biography_error1', '', 100000);
        $messages['biography1'] = '<p class="msg">Не заполнено поле биографии</p>';
    } else if (!empty($errors['biography2'])) {
        setcookie('biography_error2', '', 100000);
        $messages['biography2'] = '<p class="msg">Недопустимый формат ввода биографии</p>';
    }
    include('dbshow.php');
    exit();
} else {
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)$/', $key, $matches)) {
            $app_id = $matches[1];
            setcookie('clear', $app_id, time() + 24 * 60 * 60);
            $stmt = $db->prepare("DELETE FROM application WHERE application_id = ?");
            $stmt->execute([$app_id]);
            $stmt = $db->prepare("DELETE FROM abilities WHERE application_id = ?");
            $stmt->execute([$app_id]);
            $stmt = $db->prepare("DELETE FROM users WHERE application_id = ?");
            $stmt->execute([$app_id]);
        }
        if (preg_match('/^save(\d+)$/', $key, $matches)) {
            $app_id = $matches[1];
            $dates = array();
            $dates['name'] = $_POST['name' . $app_id];
            $dates['email'] = $_POST['email' . $app_id];
            $dates['year'] = $_POST['year' . $app_id];
            $dates['gender'] = $_POST['gender' . $app_id];
            $dates['hand'] = $_POST['hand' . $app_id];
            $abilities = $_POST['abilities' . $app_id];
            $filtred_abilities = array_filter($abilities, function($value) {return($value == 1 || $value == 2 || $value == 3);});
            $dates['biography'] = $_POST['biography' . $app_id];
        
            $name = $dates['name'];
            $email = $dates['email'];
            $year = $dates['year'];
            $gender = $dates['gender'];
            $hand = $dates['hand'];
            $biography = $dates['biography'];
        
            if (empty($name)) {
                setcookie('name_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            }
            if (empty($email)) {
                setcookie('email_error1', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                setcookie('email_error2', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } 
            if (!is_numeric($year)) {
                setcookie('year_error1', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } else if ((2023 - $year) < 14) {
                setcookie('year_error2', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            }
            if (empty($gender)) {
                setcookie('gender_error1', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } else if ($gender != 'male' && $gender != 'female') {
                setcookie('gender_error2', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } 
            if (empty($hand)) {
                setcookie('hand_error1', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } else if ($hand != 'right' && $hand != 'left') {
                setcookie('hand_error2', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            }
            if (empty($abilities)) {
                setcookie('abilities_error1', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
              } else if (count($filtred_abilities) != count($abilities)) {
                setcookie('abilities_error2', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
              }
            if (empty($biography)) {
                setcookie('biography_error1', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } else if (!preg_match('/^[\p{Cyrillic}\d\s,.!?-]+$/u', $biography)) {
                setcookie('biography_error2', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            } 
        
            if ($errors) {
                setcookie('error_id', $app_id, time() + 24 * 60 * 60);
                header('Location: index.php');
                exit();
            } else {
                setcookie('name_error', '', 100000);
                setcookie('email_error1', '', 100000);
                setcookie('email_error2', '', 100000);
                setcookie('year_error1', '', 100000);
                setcookie('year_error2', '', 100000);
                setcookie('gender_error1', '', 100000);
                setcookie('gender_error2', '', 100000);
                setcookie('hand_error1', '', 100000);
                setcookie('hand_error2', '', 100000);
                setcookie('abilities_error1', '', 100000);
                setcookie('abilities_error2', '', 100000);
                setcookie('biography_error1', '', 100000);
                setcookie('biography_error2', '', 100000);
                setcookie('error_id', '', 100000);
            }
            $stmt = $db->prepare("SELECT name, email, year, gender, hand, biography FROM application WHERE application_id = ?");
            $stmt->execute([$app_id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT superpower_id FROM abilities WHERE application_id = ?");
            $stmt->execute([$app_id]);
            $old_abilities = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (array_diff($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE application SET name = ?, email = ?, year = ?, gender = ?, hand = ?, biography = ? WHERE application_id = ?");
                $stmt->execute([$dates['name'], $dates['email'], $dates['year'], $dates['gender'], $dates['hand'], $dates['biography'], $app_id]);
            }
            if (array_diff($abilities, $old_abilities) || count($abilities) != count($old_abilities)) {
                $stmt = $db->prepare("DELETE FROM abilities WHERE application_id = ?");
                $stmt->execute([$app_id]);
                $stmt = $db->prepare("INSERT INTO abilities (application_id, superpower_id) VALUES (?, ?)");
                foreach ($abilities as $superpower_id) {
                    $stmt->execute([$app_id, $superpower_id]);
                }
            }
        }
    }
    header('Location: index.php');
}