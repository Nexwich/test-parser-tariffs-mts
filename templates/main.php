<?php
/**
 * @var array $tariffs
 * @var array $categories
 * @var array $sorts
 */
?>

<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Тарифы</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    .hide {
      display:none;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Тарифы</h1>

  <div class="row">
    <div class="col-auto">
      <form action="tariffs/parse" method="post" class="form-reload">
        <div class="my-3">
          <button class="btn btn-warning" type="submit">
            <span>Парсить</span>
            <i class="fa-solid fa-yin-yang fa-spin hide"></i>
          </button>
        </div>
      </form>
    </div>

    <?php if (!empty($categories) or !empty($tariffs)) { ?>
      <div class="col-auto">
        <form action="tariffs/clear" method="post" class="form-reload">
          <div class="my-3">
            <button class="btn btn-danger" type="submit">
              <span>Очистить данные</span>
              <i class="fa-solid fa-yin-yang fa-spin hide"></i>
            </button>
          </div>
        </form>
      </div>
    <?php } ?>
  </div>

  <form id="form" action="" method="get">
    <div class="my-4">
      <div class="row">
        <?php if (!empty($categories)) { ?>
          <div class="col-auto">
            <label for="input-category">Категории</label>

            <select class="form-select js-submit" name="search[categoryId]" id="input-category">
              <option value="">Все</option>

              <?php foreach ($categories as $category) { ?>
                <option
                  value="<?= $category['id'] ?>"

                  <?php if (!empty($_GET['search']['categoryId']) and $_GET['search']['categoryId']
                    == $category['id']) { ?>
                    selected="selected"
                  <?php } ?>
                >
                  <?= $category['title'] ?>
                </option>
              <?php } ?>
            </select>
          </div>
        <?php } ?>

        <?php if (!empty($sorts) and !empty($tariffs)) { ?>
          <div class="col-auto">
            <label for="input-sort">Сортировка</label>

            <select class="form-select js-submit" name="sort" id="input-sort">
              <?php foreach ($sorts as $sort) { ?>
                <option
                  value="<?= $sort['id'] ?>"

                  <?php if (!empty($_GET['sort']) and $_GET['sort'] == $sort['id']) { ?>
                    selected="selected"
                  <?php } ?>
                >
                  <?= $sort['title'] ?>
                </option>
              <?php } ?>
            </select>
          </div>
        <?php } ?>
      </div>
    </div>
  </form>

  <?php if (!empty($tariffs)) { ?>
    <section>
      <?php foreach ($tariffs as $tariff) { ?>
        <div class="tariff my-3">
          <h3 class="tariff--title"><?= $tariff['title'] ?></h3>

          <?php if (!empty($tariff['offers'])) { ?>
            <div class="tariff--offers">
              <?php foreach ($tariff['offers'] as $offer) { ?>
                <div class="offer my-2">
                  <div class="offer--title"><?= $offer['title'] ?></div>
                  <div class="offer--price"><?= $offer['price'] ?>&nbsp;₽/мес</div>
                </div>
              <?php } ?>
            </div>
          <?php }else { ?>
            <div class="tariff--offers">
              <div class="offer my-2">
                <div class="offer--title">Настраиваемый</div>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </section>
  <?php } ?>
</div>

<script src="../assets/js/app.min.js"></script>

</body>
</html>
