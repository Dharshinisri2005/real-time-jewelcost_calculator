<?php
$jewelleryList = json_decode(file_get_contents("jewellery_data.json"), true);
$apiKey = "6de8f2b79c40f56a7e6a75ce5cb0111c";
$apiUrl = "https://api.metalpriceapi.com/v1/latest?base=INR&currencies=XAU&api_key=" . $apiKey;

$live_gold_rate = null;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (isset($data['rates']['XAU']) && $data['rates']['XAU'] > 0) {
    $price_per_ounce = 1 / $data['rates']['XAU']; 
    $live_gold_rate = $price_per_ounce / 31.1035; 
}
$selectedJewellery = null;
$totalAmount = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["jewellery_id"])) {
    $id = intval($_POST["jewellery_id"]);
    foreach ($jewelleryList as $item) {
        if ($item["id"] == $id) {
            $selectedJewellery = $item;
            break;
        }
    }

    if ($selectedJewellery && $live_gold_rate) {
        $gold_weight = $selectedJewellery["gold_weight"];
        $wastage = $selectedJewellery["wastage_percent"];
        $gold_rate = $live_gold_rate;
        $making_charge_percent = $selectedJewellery["making_charge_percent"];
        $tax_percent = $selectedJewellery["tax_percent"];

        $adjusted_weight = $gold_weight + ($gold_weight * $wastage / 100);
        $gold_cost = $adjusted_weight * $gold_rate;
        $making_charge = $gold_cost * $making_charge_percent / 100;
        $subtotal = $gold_cost + $making_charge;
        $tax = $subtotal * $tax_percent / 100;
        $totalAmount = $subtotal + $tax;
    }
}
?>
<html>
<head>
    <title>Jewellery Price Calculator</title>
    <style>
        body {
            font-family: Arial;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url("img.jpg") repeat center center fixed;
            background-size: cover;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 50px;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #d4af37;
        }
        form {
            margin-bottom: 30px;
            text-align: center;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            margin: 10px;
            width: 60%;
        }
        button {
            background-color: #d4af37;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: rgb(201, 158, 16);
        }
        .details {
            text-align: center;
        }
        .details img {
            max-width: 300px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        ul {
            list-style-type: none;
            padding: 0;
            text-align: left;
            display: inline-block;
            margin-top: 20px;
        }
        li {
            margin-bottom: 10px;
            font-size: 16px;
        }
        .total {
            font-weight: bold;
            color: rgb(201, 158, 16);
            font-size: 20px;
            margin-top: 10px;
        }
        .live-rate {
            text-align: center;
            font-size: 16px;
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Jewellery Price Calculator</h2>

    <?php if ($live_gold_rate): ?>
        <div class="live-rate">
            💰 Live Gold Rate (INR/gram): ₹<?php echo number_format($live_gold_rate, 2); ?>
        </div>
    <?php else: ?>
        <div class="live-rate" style="color: red;">
            ❌ Failed to fetch live gold rate. Please try again later.
        </div>
    <?php endif; ?>

    <form method="POST">
        <select name="jewellery_id" required>
            <option value="">-- Select Jewellery --</option>
            <?php foreach ($jewelleryList as $item): ?>
                <option value="<?php echo $item["id"]; ?>" <?php if ($selectedJewellery && $selectedJewellery["id"] == $item["id"]) echo "selected"; ?>>
                    <?php echo htmlspecialchars($item["jewellery_name"]); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <button type="submit">Calculate Price</button>
    </form>

    <?php if ($selectedJewellery): ?>
        <div class="details">
            <h3><?php echo htmlspecialchars($selectedJewellery["jewellery_name"]); ?></h3>
            <img src="<?php echo htmlspecialchars($selectedJewellery['image_url']) . '?v=' . time(); ?>"
                 alt="Image of <?php echo htmlspecialchars($selectedJewellery['jewellery_name']); ?>">
            <ul>
                <li>Gold Weight: <?php echo $gold_weight; ?> g</li>
                <li>Gold Rate: ₹<?php echo number_format($gold_rate, 2); ?> / gram</li>
                <li>Making Charge: <?php echo $making_charge_percent; ?>%</li>
                <li>Wastage: <?php echo $wastage; ?>%</li>
                <li>Tax: <?php echo $tax_percent; ?>%</li>
            </ul>
            <div class="total">Total Amount: ₹<?php echo number_format($totalAmount, 2); ?></div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
