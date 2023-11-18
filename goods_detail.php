<?php
require "config.php";

$goods_id = isset($_GET['goods_id']) ? $_GET['goods_id'] : 1;

$sql = "SELECT * FROM goods_detail WHERE goods_id = $goods_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $row['product_name'] ?> - 商品详情页</title>
        <link rel="stylesheet" href="./css/goods_detail.css">
    </head>

    <body>

        <header>
            <h1><?= $row['product_name'] ?></h1>
            <p><?= $row['description'] ?></p>
        </header>

        <section class="product-images">
            <div class="image-container">
                <img src="<?= $row['image1'] ?>" alt="商品图片1">
            </div>
            <div class="image-container">
                <img src="<?= $row['image2'] ?>" alt="商品图片2">
            </div>
            <div class="image-container">
                <img src="<?= $row['image3'] ?>" alt="商品图片3">
            </div>
        </section>

        <section class="description">
            <h2>商品详细介绍</h2>
            <p><?= $row['description'] ?></p>
            <div class="additional-info">
                <h3>更多信息</h3>
                <p><?= $row['additional_info'] ?></p>
            </div>
        </section>

        <section class="related-products">
            <h2>相关商品</h2>
            <div class="related-product">
                <h3><?= $row['related_product1_name'] ?></h3>
                <p><?= $row['related_product1_description'] ?></p>
                <img src="<?= $row['related_product1_image'] ?>" alt="<?= $row['related_product1_name'] ?>">
            </div>
            <div class="related-product">
                <h3><?= $row['related_product2_name'] ?></h3>
                <p><?= $row['related_product2_description'] ?></p>
                <img src="<?= $row['related_product2_image'] ?>" alt="<?= $row['related_product2_name'] ?>">
            </div>
        </section>

        <section class="product-specs">
            <h2>产品规格</h2>
            <ul>
                <?php
                // Assuming product_specs is a comma-separated list in the database
                $specs = explode(',', $row['product_specs']);
                foreach ($specs as $spec) {
                    echo '<li>' . trim($spec) . '</li>';
                }
                ?>
            </ul>
        </section>

        <section class="shipping-info">
            <h2>配送信息</h2>
            <p><?= $row['shipping_info'] ?></p>
        </section>

        <section class="special-offers">
            <h2>特别优惠</h2>
            <p><?= $row['special_offers'] ?></p>
        </section>

        <footer>
            <button id="go-to-top">返回顶部</button>
            <div class="social-media">
                <p>分享：</p>
                <a href="#" class="social-icon">Facebook</a>
                <a href="#" class="social-icon">Twitter</a>
                <a href="#" class="social-icon">Instagram</a>
            </div>
        </footer>

    </body>

    </html>
<?php
} else {
    echo "未找到该商品的详情信息。";
}

$conn->close();
?>