<?php
require_once __DIR__ . '/header.php';


$uploadDir = __DIR__ . '/images/';
$uploadRel = 'images/';

$allowedExt = ['jpg','jpeg','png','webp'];

// Хелпер загрузки (возвращает относительный путь или null)
function handle_upload($fieldName, $uploadDir, $uploadRel, $allowedExt) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $tmp  = $_FILES[$fieldName]['tmp_name'];
    $name = $_FILES[$fieldName]['name'];
    $size = $_FILES[$fieldName]['size'];

    // Проверка расширения
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return null; // можно вывести alert об ошибке
    }

    $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($name, PATHINFO_FILENAME));
    $newName  = $safeBase . '_' . uniqid() . '.' . $ext;

    // Переносим
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
    if (move_uploaded_file($tmp, $uploadDir . $newName)) {
        return $uploadRel . $newName;
    }
    return null;
}

/* -------------------- CREATE -------------------- */
if (isset($_GET["aktion"]) && $_GET["aktion"] === "1") {
    $name   = trim($_POST["name"] ?? "");
    $price  = $_POST["price"] ?? null;
    $amount = $_POST["amount"] ?? null;
    $comment= trim($_POST["comment"] ?? "");

    // загрузка файла (необязательна)
    $relPath = handle_upload('bild', $uploadDir, $uploadRel, $allowedExt);

    $db->execute(
        "INSERT INTO produkte (ProduktName, ProduktBild, ProduktPreis, ProduktMenge, ProduktKommentar)
         VALUES (?, ?, ?, ?, ?)",
        [$name, $relPath, $price, $amount, $comment]
    );

    header('Location: produkte_admin.php');
    exit;
}

/* -------------------- UPDATE / DELETE -------------------- */
if (isset($_GET["aktion"]) && $_GET["aktion"] === "2") {
    $rowId = (int)($_POST["zeileID"] ?? 0);

    if (!empty($_POST["entfernen"]) && $_POST["entfernen"] === "ja") {
        // Удаляем только запись. Файл НЕ удаляем (IMG-KEEP).
        $db->execute("DELETE FROM produkte WHERE ProduktID = ?", [$rowId]);
        header('Location: produkte_admin.php'); exit;
    } else {
        $name    = trim($_POST["name"] ?? "");
        $price   = $_POST["price"] ?? null;
        $amount  = $_POST["amount"] ?? null;
        $comment = trim($_POST["comment"] ?? "");
        $oldImg  = trim($_POST["old_image"] ?? ""); // старый путь

        // Если загружен новый файл — используем его; иначе оставляем старый
        $newImg = handle_upload('bild', $uploadDir, $uploadRel, $allowedExt);
        $finalImg = $newImg ?: $oldImg;

        $db->execute(
            "UPDATE produkte SET ProduktName=?, ProduktBild=?, ProduktPreis=?, ProduktMenge=?, ProduktKommentar=?
             WHERE ProduktID=?",
            [$name, $finalImg, $price, $amount, $comment, $rowId]
        );
        header('Location: produkte_admin.php'); exit;
    }
}

/* -------------------- READ -------------------- */
$produkte = $db->select("SELECT * FROM produkte ORDER BY ProduktID DESC");
?>
<h1 class="mb-4">Produkte bearbeiten</h1>

<!-- Кнопка показать/скрыть форму добавления -->
<div class="mb-3">
  <button class="btn btn-success" onclick="document.getElementById('addBox').classList.toggle('d-none');">
    + Neues Produkt
  </button>
  <a class="btn btn-link" href="index2.php">Zurück zur Produktliste</a>
</div>

<!-- Форма добавления товара -->
<div id="addBox" class="card p-3 mb-4 d-none">
  <form action="produkte_admin.php?aktion=1" method="post" enctype="multipart/form-data" autocomplete="off">
    <div class="row g-2">
      <div class="col-md-4">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Preis (€)</label>
        <input class="form-control" type="number" step="0.01" name="price" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Menge</label>
        <input class="form-control" type="number" name="amount" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Bild (jpg, png, webp)</label>
        <input class="form-control" type="file" name="bild" accept=".jpg,.jpeg,.png,.webp">
      </div>
    </div>
    <div class="mt-2">
      <label class="form-label">Kommentar</label>
      <textarea class="form-control" name="comment" rows="3"></textarea>
    </div>
    <button class="btn btn-primary mt-3">Speichern</button>
  </form>
</div>

<!-- UPDATE / DELETE FORM (оборачивает таблицу) -->
<form id="editForm" action="produkte_admin.php?aktion=2" method="post" enctype="multipart/form-data">
  <input type="hidden" name="zeileID" value="">
  <input type="hidden" name="entfernen" value="">
  <input type="hidden" name="old_image" value="">

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:70px;">ID</th>
          <th style="width:90px;">Bild</th>
          <th>Name</th>
          <th style="width:140px;">Preis</th>
          <th style="width:120px;">Menge</th>
          <th style="width:320px;">Kommentar</th>
          <th style="width:200px;">Aktionen</th>
        </tr>
      </thead>
      <tbody>
      <?php $pos = 1; foreach ($produkte as $p): ?>
        <tr>
          <td id="id-<?= $pos ?>"><?= (int)$p['ProduktID'] ?></td>

          <!-- миниатюра 60x60 -->
          <td id="imgcell-<?= $pos ?>">
            <?php $img = $p['ProduktBild'] ?: 'https://via.placeholder.com/60?text=Bild'; ?>
            <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
          </td>

          <td id="name-<?= $pos ?>"><?= htmlspecialchars($p['ProduktName'] ?? '') ?></td>
          <td id="price-<?= $pos ?>"><?= htmlspecialchars($p['ProduktPreis'] ?? '') ?></td>
          <td id="amount-<?= $pos ?>"><?= htmlspecialchars($p['ProduktMenge'] ?? '') ?></td>
          <td id="comment-<?= $pos ?>" style="white-space:pre-wrap;"><?= htmlspecialchars($p['ProduktKommentar'] ?? '') ?></td>

          <td id="ed-<?= $pos ?>">
            <button type="button" class="btn btn-sm btn-primary" onclick="ProdEdit(<?= $pos ?>)">Bearbeiten</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="ProdRemove(<?= $pos ?>)">Löschen</button>
          </td>
        </tr>
      <?php $pos++; endforeach; ?>
      </tbody>
    </table>
  </div>
</form>

<?php require_once __DIR__ . '/footer.php'; ?>
<script src="produkte_admin.js"></script>
