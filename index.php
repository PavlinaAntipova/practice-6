<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Practice 6. PHP</title>
    <style>
        input,
        select {
            display: block;
            width: 120px;
        }

        label {
            display: block;
        }

        .result {
            color: green;
        }
    </style>
</head>

<body>
    <header>
        <h1>Practice 6. PHP</h1>
        <p>Автор: Pavlina Antipova</p>
        <p>Варіант: 6</p>
    </header>
    <main>
        <section>
            <h2>Task 1</h2>
            <p>Число А лежить в межах від 20 до 30. Індексний масив повинен заповнюватись рандомними значеннями, доки не отримає значення А. Значення масиву повинні бути в межах від 10 до 60.
            </p>
            <p>Після заповнення масиву необхідно усі значення масиву заокруглити до найближчого числа кратного 10.</p>
            <p>Статистичні дані, що необхідно отримати:</p>
            <ul>
                <li>кількість елементів масиву, значення яких більше А;</li>
                <li>максимальне значення;</li>
                <li>сума усіх значень, котрі мають індекс, на який А ділиться без остачі.</li>
            </ul>
            <form method="get">
                <label>Уведіть мінімальне значення добутку елементів масиву:
                    <input type="number" min='20' max='30' step='1' name="numberA">
                    <button type="submit">Увести</button>
                </label>
            </form>
            <?php
            define("MIN_ARRAY_VALUE", 10);
            define("MAX_ARRAY_VALUE", 60);
            define("ROUND_CONDITION", 10);

            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if (!empty($_GET["numberA"])) {
                    $A = $_GET["numberA"];
                    $arr = fillArr($A);

                    echo "<p>Число А -  $A</p>";
                    echo "<p>Утворенний масив до редагування: <br />";
                    renderArray($arr);
                    echo "</p>";
                    echo "<p>Відредагованний масив: <br />";
                    renderArray(changeArray($arr));
                    echo "</p>";
                    echo "<p>Статистичні дані про значення у масиві:</p>";

                    echo renderAssociativeArray(createAssociativeArray(calculateAmountValues($arr, $A), findMaxValue($arr), sumValues($arr, $A)));
                }
            }
            ?>

        </section>
        <section>
            <h2>Task 2</h2>
            <p>Виведіть на сторінку кількість днів, що вже пройшли у поточному році.</p>

            <?php
            echo "<p>Поточна дата - ";
            echo date('d.m.Y');
            echo "</p>";
            echo "<p>З початку року пройшло ";
            echo getCurrentDays();
            echo " днів </p>";

            ?>

        </section>
        <section>
            <h2>Task 3</h2>
            <p>Конвертер одиниць об’єму. Доступні для конвертування одиниці: мілілітри, літри, м3.
            </p>
            <?php
            define("MEASURES", array("ml", "l", 'm3'));
            define("ML_TO_OTHERS", array("ml" => 1, "l" => 0.001, 'm3' => 0.000001));
            define("L_TO_OTHERS", array("ml" => 1000, "l" => 1, 'm3' => 0.001));
            define("M3_TO_OTHERS", array("ml" => 1000000, "l" => 1000, 'm3' => 1));

            $validationErrors = [];
            ?>
            <form method="post" novalidate>
                <label>
                    Amount
                    <input type="number" name="amount">
                </label>
                <br />
                <label>
                    From
                    <select name="start" id="start">
                        <?php
                        foreach (MEASURES as $value) {
                            echo "<option value=$value>$value</option>";
                        }
                        ?>
                    </select>
                </label>
                <br />
                <label>
                    To
                    <select name="end" id="end">
                        <?php
                        foreach (MEASURES as $value) {
                            echo "<option value=$value>$value</option>";
                        }
                        ?>
                    </select>
                </label>
                <br />
                <button type="submit">Convert</button>
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $amount = $_POST["amount"];
                $fromValue = $_POST["start"];
                $toValue = $_POST["end"];
                $validationErrors['from'] = validateMeasures($fromValue);
                $validationErrors['to'] = validateMeasures($toValue);
                $validationErrors['amount'] = validateAmount($amount);


                if (containsError($validationErrors)['status']) {
                    echo "<p>";
                    echo containsError($validationErrors)['text'];
                    echo "</p>";
                } else {
                    $result = convert($amount, $fromValue, $toValue);
                    echo "<p class='result'>";
                    echo formatNumber($amount);
                    echo " $fromValue = ";
                    echo formatNumber($result);
                    echo " $toValue</p>";
                }
            }





            ?>
        </section>
    </main>
    <?php
    function fillArr($number)
    {
        $arr = [];
        do {
            $randomNumber = random_int(MIN_ARRAY_VALUE, MAX_ARRAY_VALUE);
            $arr[] = $randomNumber;
        } while ($randomNumber != $number);

        return $arr;
    }

    function roundNumber($number)
    {
        return ROUND_CONDITION * round($number / ROUND_CONDITION);
    }

    function changeArray($arr)
    {
        $newArray = [];

        for ($i = 0; $i < count($arr); $i++) {
            $newArray[] = roundNumber($arr[$i]);
        }

        return $newArray;
    }

    function renderArray($arr)
    {
        echo "arr = [";
        for ($i = 0; $i < count($arr); $i++) {
            echo $arr[$i];
            if ($i < count($arr) - 1) {
                echo ", ";
            }
        }
        echo "]";
    }

    function createAssociativeArray($amount, $maxValue, $sum)
    {
        $associativeArray = [];
        $associativeArray["amount"] = $amount;
        $associativeArray["max_value"] = $maxValue;
        $associativeArray["sum"] = $sum;

        return $associativeArray;
    }

    function renderAssociativeArray($arr)
    {
        foreach ($arr as $x => $x_value) {
            echo $x . "=" . $x_value;
            echo "<br>";
        }
    }


    function calculateAmountValues($arr, $inputNumber)
    {
        $filtredArray = [];
        for ($i = 0; $i < count($arr); $i++) {
            if (greaterThanNumber($arr[$i], $inputNumber)) {
                $filtredArray[] = $arr[$i];
            }
        }
        return count($filtredArray);
    }

    function greaterThanNumber($arrayNumber, $inputNumber)
    {
        return $arrayNumber > $inputNumber;
    }

    function findMaxValue($arr)
    {
        return max($arr);
    }

    function sumValues($arr, $inputNumber)
    {
        $count = 0;
        $number = (int)$inputNumber;

        for ($i = 0; $i < count($arr); $i++) {
            if (is_int($number / $i)) {
                $count = $count + $arr[$i];
            }
        }
        return $count;
    }

    function getCurrentDays()
    {
        $today = time();
        $startDate = mktime(0, 0, 0, 1, 1, 2022);
        return floor(($today - $startDate) / 86400);
    }

    function validateMeasures($measure)
    {
        if (empty($measure)) {
            return 'Measure value is required';
        }

        if (in_array($measure, MEASURES)) {
            return null;
        } else {
            return 'Measure value should be one of the provided values';
        }
    }

    function validateAmount($amount)
    {
        if (empty($amount)) {
            return 'Amount value is required';
        }

        if (!floatval($amount)) {
            return 'Amount value should be a number';
        }

        if ($amount < 0) {
            return 'Amount value should be a positive number';
        }

        return null;
    }

    function containsError($arr)
    {
        foreach ($arr as $error => $error_value) {
            if ($error_value != null) {
                return array('status' => true, 'text' => $error_value);
            }
        }
        return array('status' => false, 'text' => null);
    }

    function convert($amount, $start, $end)
    {
        if ($start == $end) {
            return $amount;
        }

        if ($start == MEASURES[0]) {
            return $amount * ML_TO_OTHERS[$end];
        }

        if ($start == MEASURES[1]) {
            return $amount * L_TO_OTHERS[$end];
        }

        if ($start == MEASURES[2]) {
            return $amount * M3_TO_OTHERS[$end];
        }
    }

    function formatNumber($number)
    {
        return number_format($number, 2, '.', ' ');
    }

    ?>
</body>

</html>