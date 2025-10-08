<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use App\Connection;
// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

$pdo = Connection::getPDO();
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');
$pdo->exec('TRUNCATE TABLE post_category');
$pdo->exec('TRUNCATE TABLE category');
$pdo->exec('TRUNCATE TABLE post');
$pdo->exec('TRUNCATE TABLE user');
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

$posts = [];
$categories = [];

for ($i = 0; $i < 50; $i++) {
    $name = $faker->sentence();     // titre court
    $slug = $faker->slug;           // slug "url friendly"
    $date = $faker->dateTimeBetween('-1 year', 'now')
        ->format('Y-m-d H:i:s');
    $content = $faker->paragraphs(3, true); // 3 paragraphes

    $pdo->exec("INSERT INTO post 
        (name, slug, created_at, content) 
        VALUES ('$name', '$slug', '$date', '$content')");
    $posts[] = $pdo->lastInsertId();
}
for ($i = 0; $i < 10; $i++) {
    $name = $faker->word();     // un mot
    $slug = $faker->slug;       // slug "url friendly"

    $pdo->exec("INSERT INTO category 
        (name, slug) 
        VALUES ('$name', '$slug')");
    $categories[] = $pdo->lastInsertId();
}

foreach ($posts as $post) {
    $randomCategories = $faker->randomElements($categories, rand(0, count($categories)));
    foreach ($randomCategories as $category) {
        $pdo->exec("INSERT INTO post_category 
            (post_id, category_id) 
            VALUES ($post, $category)");
    }
}

$password = password_hash('asmin', PASSWORD_BCRYPT);
$pdo->exec("INSERT INTO user 
    (username, password) 
    VALUES ('admin', '$password')");
