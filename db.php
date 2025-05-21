<?php
$servername = "localhost:4306";
$username = "root";
$password = "exide*0103";
$dbname = "mydb";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$jewelleryList = [];
$result = $conn->query("SELECT * FROM j");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jewelleryList[] = $row;
    }
}
$apiKey = "6de8f2b79c40f56a7e6a75ce5cb0111c";
$apiUrl = "https://api.metalpriceapi.com/v1/latest?base=INR&currencies=XAU&api_key=$apiKey";

$live_gold_rate = null;
$warning = null;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (isset($data['rates']['XAU']) && $data['rates']['XAU'] > 0) {
    $price_per_ounce = 1 / $data['rates']['XAU'];
    $live_gold_rate = $price_per_ounce / 31.1035;
} else {
    $warning = "Could not fetch live gold rate.";
}
$selectedJewellery = null;
$totalAmount = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["jewellery_id"])) {
    $id = intval($_POST["jewellery_id"]);
    foreach ($jewelleryList as $item) {
        if ($item["id"] == $id) {
            $selectedJewellery = $item;
            break;
        }
    }

    if ($selectedJewellery && $live_gold_rate !== null) {
        $weight = $selectedJewellery["gold_weight"];
        $wastage = $selectedJewellery["wastage_percent"];
        $making = $selectedJewellery["making_charge_percent"];
        $tax = $selectedJewellery["tax_percent"];

        $adjusted_weight = $weight + ($weight * $wastage / 100);
        $gold_cost = $adjusted_weight * $live_gold_rate;
        $making_amt = $gold_cost * $making / 100;
        $subtotal = $gold_cost + $making_amt;
        $tax_amt = $subtotal * $tax / 100;
        $totalAmount = $subtotal + $tax_amt;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jewellery Price Calculator</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom right, #f7f5f0, #fff7e6);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url("img.jpg") repeat center center fixed;
            background-size: cover;
        }

        .container {
            max-width: 900px;
            margin: 60px auto;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #b28c39;
            margin-bottom: 30px;
        }

        form {
            text-align: center;
            margin-bottom: 30px;
        }

        select {
            padding: 12px 20px;
            width: 60%;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            margin-top: 15px;
            padding: 12px 25px;
            background-color: #b28c39;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #a07c2d;
        }

        .details {
            text-align: center;
        }

        .details img {
            max-width: 300px;
            margin-top: 20px;
            border-radius: 12px;
        }

        ul {
            text-align: left;
            display: inline-block;
            margin-top: 20px;
            font-size: 16px;
            color: #444;
        }

        .total {
            margin-top: 20px;
            font-size: 22px;
            color: #d4af37;
            font-weight: bold;
        }

        .warning {
            text-align: center;
            color: red;
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            select {
                width: 90%;
            }
            .details img {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Jewellery Price Calculator</h2>

        <?php if ($warning): ?>
            <div class="warning"><?= htmlspecialchars($warning) ?></div>
        <?php endif; ?>

        <form method="POST">
            <select name="jewellery_id" required>
                <option value="">-- Select Jewellery --</option>
                <?php foreach ($jewelleryList as $j): ?>
                    <option value="<?= $j["id"] ?>" <?= ($selectedJewellery && $selectedJewellery["id"] == $j["id"]) ? "selected" : "" ?>>
                        <?= htmlspecialchars($j["jewellery_name"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>
            <button type="submit">Calculate Price</button>
        </form>

        <?php if ($selectedJewellery && $live_gold_rate !== null): ?>
            <div class="details">
                <h3><?= htmlspecialchars($selectedJewellery["jewellery_name"]) ?></h3>
                <img src="<?= htmlspecialchars($selectedJewellery["image_url"]) ?>?v=<?= time(); ?>" alt="Jewellery">
                <ul>
                    <li><strong>Gold Weight:</strong> <?= $selectedJewellery["gold_weight"] ?> g</li>
                    <li><strong>Wastage:</strong> <?= $selectedJewellery["wastage_percent"] ?>%</li>
                    <li><strong>Making Charges:</strong> <?= $selectedJewellery["making_charge_percent"] ?>%</li>
                    <li><strong>Tax:</strong> <?= $selectedJewellery["tax_percent"] ?>%</li>
                    <li><strong>Live Gold Rate:</strong> ₹<?= number_format($live_gold_rate, 2) ?> / gram</li>
                </ul>
                <div class="total">Total Price: ₹<?= number_format($totalAmount, 2) ?></div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
