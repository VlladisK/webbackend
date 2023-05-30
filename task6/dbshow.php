<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <title>Task 6</title>
    <link rel="stylesheet" href="db.css">
</head>
<body>
<?php
    echo '<div class="msgbox">'; 
        if (!empty($messages)) {
            foreach ($messages as $message) {
                print($message);
            }
        }
    echo '</div>';
    $stmt = $db->prepare("SELECT count(application_id) from abilities where superpower_id = 1;");
    $stmt->execute();
    $god = $stmt->fetchColumn();
    $stmt = $db->prepare("SELECT count(application_id) from abilities where superpower_id = 2;");
    $stmt->execute();
    $steni = $stmt->fetchColumn();
    $stmt = $db->prepare("SELECT count(application_id) from abilities where superpower_id = 3;");
    $stmt->execute();
    $levit = $stmt->fetchColumn();
    echo "бессмертие: "; echo (empty($god) ? '0' : $god) . "</br>";
    echo "прохождение сквозь стены: "; echo (empty($steni) ? '0' : $steni) . "</br>";
    echo "левитация: "; echo (empty($levit) ? '0' : $levit) . "</br>";
?>
    <form action="" method="POST">
        <table>
            <caption>Данные формы</caption>
            <tr> 
                <th>id</th>
                <th>Имя</th>
                <th>email</th>
                <th>Год</th>
                <th>Пол</th>
                <th>Преобладающая рука</th>
                <th>Суперсила</th>
                <th>Биография</th>
            </tr>
            <?php
                foreach ($values as $value) {
                    echo    '<tr>
                            <td style="font-weight: 700;">'; print($value['application_id']); echo '</td>
                            <td>
                                <input class="input" name="name'.$value['application_id'].'" value="'; print($value['name']); echo '">
                            </td>
                            <td>
                                <input class="input" name="email'.$value['application_id'].'" value="'; print($value['email']); echo '">
                            </td>
                            <td>
                                <select name="year'.$value['application_id'].'">';
                                    for ($i = 2023; $i >= 1922; $i--) {
                                        if ($i == $value['year']) {
                                            printf('<option selected value="%d">%d год</option>', $i, $i);
                                        } else {
                                            printf('<option value="%d">%d год</option>', $i, $i);
                                        }
                                    }
                    echo        '</select>
                            </td>
                            <td> 
                                <div class="column-item">
                                    <input type="radio" id="radioMale'.$value['application_id'].'" name="gender'.$value['application_id'].'" value="male" '; if ($value['gender'] == 'male') echo 'checked'; echo '>
                                    <label for="radioMale'.$value['application_id'].'">Мужчина</label>
                                </div>
                                <div class="column-item">
                                    <input type="radio" id="radioFemale'.$value['application_id'].'" name="gender'.$value['application_id'].'" value="female" '; if ($value['gender'] == 'female') echo 'checked'; echo '>
                                    <label for="radioFemale'.$value['application_id'].'">Женщина</label>
                                </div>
                            </td>
                            <td>
                                <div class="column-item">
                                    <input type="radio" id="radioRight'.$value['application_id'].'" name="hand'.$value['application_id'].'" value="right" '; if ($value['hand'] == 'right') echo 'checked'; echo '>
                                    <label for="radioRight'.$value['application_id'].'">Правша</label>
                                </div>
                                <div class="column-item">
                                    <input type="radio" id="radioLeft'.$value['application_id'].'" name="hand'.$value['application_id'].'" value="left" '; if ($value['hand'] == 'left') echo 'checked'; echo '>
                                    <label for="radioLeft'.$value['application_id'].'">Левша</label>
                                </div>
                            </td>';
                    $stmt = $db->prepare("SELECT superpower_id FROM abilities WHERE application_id = ?");
                    $stmt->execute([$value['application_id']]);
                    $abilities = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    echo    '<td class="abilities">
                                <div class="column-item">
                                    <input type="checkbox" id="god'.$value['application_id'].'" name="abilities'.$value['application_id'].'[]" value="1"' . (in_array(1, $abilities) ? ' checked' : '') . '>
                                    <label for="god'.$value['application_id'].'">бессмертие</label>
                                </div>
                                <div class="column-item">
                                    <input type="checkbox" id="noclip'.$value['application_id'].'" name="abilities'.$value['application_id'].'[]" value="2"' . (in_array(2, $abilities) ? ' checked' : '') . '>
                                    <label for="noclip'.$value['application_id'].'">прохождение сквозь стены</label>
                                </div>
                                <div class="column-item">
                                    <input type="checkbox" id="levitation'.$value['application_id'].'" name="abilities'.$value['application_id'].'[]" value="3"' . (in_array(3, $abilities) ? ' checked' : '') . '>
                                    <label for="levitation'.$value['application_id'].'">левитация</label>
                                </div>
                            </td>
                            <td>
                                <textarea name="biography'.$value['application_id'].'" id="" cols="30" rows="4" maxlength="128">'; print $value['biography']; echo '</textarea>
                            </td>
                            <td>
                                <div class="column-item">
                                    <input name="save'.$value['application_id'].'" type="submit" value="save'.$value['application_id'].'"/>
                                </div>
                                <div class="column-item">
                                    <input name="clear'.$value['application_id'].'" type="submit" value="clear'.$value['application_id'].'"/>
                                </div>
                            </td>
                        </tr>'; 
                }
            ?>
        </table>
    </form>
</body>
</html>