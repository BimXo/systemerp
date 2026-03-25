<?php
require_once 'SalesModel.php';
require_once 'SalesController.php';

$model     = new ModelSprzedazy();
$kontroler = new KontrolerSprzedazy($model);
$wiadomosc = '';

// ── OBSŁUGA EKSPORTU (musi być przed jakimkolwiek HTML) ─────
if (isset($_GET['eksport'])) {
    $format           = $_GET['eksport'];          // 'html' lub 'pdf'
    $wszystkieSprzedaze = $kontroler->model->pobierzWszystkie();
    $najwyzszaTransakcja = $kontroler->pobierzNajwyzszaTransakcjaPrzychod();
    $najlepszyProdukt    = $kontroler->pobierzNajwyzszyPrzychodProdukt();
    $sumaLaczna          = array_sum(array_column($wszystkieSprzedaze, 3));
    $dataRaportu         = date('d.m.Y H:i');

    // Grupowanie po produkcie
    $wgProduktu = [];
    foreach ($wszystkieSprzedaze as $s) {
        $wgProduktu[$s[2]]['count'] = ($wgProduktu[$s[2]]['count'] ?? 0) + 1;
        $wgProduktu[$s[2]]['sum']   = ($wgProduktu[$s[2]]['sum']   ?? 0) + (float)$s[3];
    }
    arsort($wgProduktu);

    ob_start();
    ?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Raport Sprzedazy <?= date('Y-m-d') ?></title>
<link rel="stylesheet" href="../../css/main.css">
<link rel="stylesheet" href="../../css/sales.css">
</head>
<body>
<div class="raport-wrap">

  <div class="raport-naglowek">
    <div>
      <h1>Raport Sprzedaży</h1>
      <div style="font-size:13px;color:#666;margin-top:4px;">System ERP &mdash; Moduł Sprzedazy</div>
    </div>
    <div class="meta">
      Wygenerowano: <?= $dataRaportu ?><br>
      Liczba rekordow: <?= count($wszystkieSprzedaze) ?>
    </div>
  </div>

  <!-- Statystyki ogolne -->
  <div class="stats-row">
    <div class="stat-box">
      <div class="label">Transakcji</div>
      <div class="value"><?= count($wszystkieSprzedaze) ?></div>
      <div class="sub">lacznie</div>
    </div>
    <div class="stat-box">
      <div class="label">Laczny przychod</div>
      <div class="value"><?= number_format($sumaLaczna, 2, ',', ' ') ?></div>
      <div class="sub">PLN</div>
    </div>
    <div class="stat-box">
      <div class="label">Srednia transakcja</div>
      <div class="value"><?= count($wszystkieSprzedaze) ? number_format($sumaLaczna / count($wszystkieSprzedaze), 2, ',', ' ') : '0,00' ?></div>
      <div class="sub">PLN</div>
    </div>
    <?php if ($najlepszyProdukt): ?>
    <div class="stat-box">
      <div class="label">Top produkt</div>
      <div class="value" style="font-size:15px;"><?= htmlspecialchars($najlepszyProdukt['produkt']) ?></div>
      <div class="sub"><?= number_format($najlepszyProdukt['suma'], 2, ',', ' ') ?> PLN</div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Zestawienie po produktach -->
  <section>
    <h2>Zestawienie wedlug produktow</h2>
    <table>
      <thead>
        <tr><th>Produkt</th><th>Liczba sprzedazy</th><th>Laczny przychod (PLN)</th><th>Udzial %</th></tr>
      </thead>
      <tbody>
        <?php foreach ($wgProduktu as $prod => $dane): ?>
        <tr>
          <td><?= htmlspecialchars($prod) ?></td>
          <td><?= $dane['count'] ?></td>
          <td><?= number_format($dane['sum'], 2, ',', ' ') ?></td>
          <td><?= $sumaLaczna > 0 ? number_format($dane['sum'] / $sumaLaczna * 100, 1) . ' %' : '—' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <!-- Najwyzsza transakcja -->
  <?php if ($najwyzszaTransakcja): ?>
  <section>
    <h2>Transakcja z najwyzszym przychodem</h2>
    <table>
      <thead><tr><th>ID</th><th>ID Klienta</th><th>Produkt</th><th>Kwota (PLN)</th><th>Data</th></tr></thead>
      <tbody>
        <tr class="highlight">
          <td><?= htmlspecialchars($najwyzszaTransakcja[0]) ?></td>
          <td><?= htmlspecialchars($najwyzszaTransakcja[1]) ?></td>
          <td><?= htmlspecialchars($najwyzszaTransakcja[2]) ?></td>
          <td><?= number_format((float)$najwyzszaTransakcja[3], 2, ',', ' ') ?></td>
          <td><?= htmlspecialchars($najwyzszaTransakcja[4]) ?></td>
        </tr>
      </tbody>
    </table>
  </section>
  <?php endif; ?>

  <!-- Pelna historia -->
  <section>
    <h2>Pelna historia transakcji</h2>
    <?php if (empty($wszystkieSprzedaze)): ?>
      <p style="color:#888;">Brak transakcji w bazie.</p>
    <?php else: ?>
    <table>
      <thead>
        <tr><th>ID</th><th>ID Klienta</th><th>Produkt</th><th>Kwota (PLN)</th><th>Data</th></tr>
      </thead>
      <tbody>
        <?php foreach ($wszystkieSprzedaze as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s[0]) ?></td>
          <td><?= htmlspecialchars($s[1]) ?></td>
          <td><?= htmlspecialchars($s[2]) ?></td>
          <td><?= number_format((float)$s[3], 2, ',', ' ') ?></td>
          <td><?= htmlspecialchars($s[4]) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr style="font-weight:700;background:#f0f5fa;">
          <td colspan="3" style="text-align:right;">Suma lacznie:</td>
          <td><?= number_format($sumaLaczna, 2, ',', ' ') ?></td>
          <td></td>
        </tr>
      </tbody>
    </table>
    <?php endif; ?>
  </section>

  <?php if ($format === 'html'): ?>
  <div class="no-print" style="margin-top:24px;display:flex;gap:12px;">
    <button onclick="window.print()" style="padding:10px 20px;background:#1f3a7a;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Drukuj / Zapisz PDF</button>
    <a href="SalesView.php" style="padding:10px 20px;background:#e1e7ed;color:#2f3b50;border-radius:6px;text-decoration:none;font-size:14px;">Wróć do modułu</a>
  </div>
  <?php endif; ?>

  <div class="raport-stopka">
    System ERP &mdash; Raport wygenerowany automatycznie dnia <?= $dataRaportu ?>
  </div>

</div>
</body>
</html>
    <?php
    $html = ob_get_clean();

    if ($format === 'html') {
        // Pobierz jako plik HTML
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="raport_sprzedazy_' . date('Y-m-d') . '.html"');
        echo $html;
        exit;
    }

    if ($format === 'pdf') {
        // Wyswietl HTML z przyciskiem "Drukuj jako PDF"
        header('Content-Type: text/html; charset=utf-8');
        // Zmien przycisk na auto-print
        $html = str_replace(
            '<button onclick="window.print()"',
            '<button style="display:none" onclick="window.print()"',
            $html
        );
        $html = str_replace(
            '</body>',
            '<script>window.onload=function(){window.print();}</script></body>',
            $html
        );
        echo $html;
        exit;
    }
}

// ── OBSŁUGA AKCJI POST ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = $_POST['akcja'] ?? '';

    if ($akcja === 'dodaj') {
        $id = $kontroler->model->utworz(
            trim($_POST['id_klienta']),
            trim($_POST['produkt']),
            $_POST['kwota'],
            $_POST['data']
        );
        $wiadomosc = "Transakcja #$id zostala dodana.";

    } elseif ($akcja === 'edytuj') {
        $ok = $kontroler->model->aktualizuj(
            $_POST['id'],
            trim($_POST['id_klienta']),
            trim($_POST['produkt']),
            $_POST['kwota'],
            $_POST['data']
        );
        $wiadomosc = $ok ? "Transakcja #{$_POST['id']} zaktualizowana." : "Nie znaleziono transakcji.";

    } elseif ($akcja === 'usun') {
        $ok = $kontroler->model->usun($_POST['id']);
        $wiadomosc = $ok ? "Transakcja #{$_POST['id']} usunieta." : "Nie znaleziono transakcji.";
    }
}

// ── DANE DO WIDOKU ───────────────────────────────────────────
$wszystkieSprzedaze  = $kontroler->model->pobierzWszystkie();
$najwyzszaTransakcja = $kontroler->pobierzNajwyzszaTransakcjaPrzychod();
$najlepszyProdukt    = $kontroler->pobierzNajwyzszyPrzychodProdukt();

$edytowana = null;
if (isset($_GET['edytuj'])) {
    $edytowana = $kontroler->model->pobierzPoId((int)$_GET['edytuj']);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bezpieczny ERP – Modul Sprzedazy</title>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/sales.css">
</head>
<body>
<div class="container">
    <h1>Bezpieczny ERP – Modul Sprzedazy</h1>

    <?php if ($wiadomosc): ?>
        <div class="message"><?= htmlspecialchars($wiadomosc) ?></div>
    <?php endif; ?>

    <!-- ── 1. FORMULARZ DODAWANIA / EDYCJI ─────────────────── -->
    <div class="section">
        <h2><?= $edytowana ? 'Edytuj transakcje #' . htmlspecialchars($edytowana[0]) : '1. Dodaj transakcje' ?></h2>
        <form method="POST">
            <input type="hidden" name="akcja" value="<?= $edytowana ? 'edytuj' : 'dodaj' ?>">
            <?php if ($edytowana): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($edytowana[0]) ?>">
            <?php endif; ?>

            <input type="text"   name="id_klienta" placeholder="ID Klienta" required
                   value="<?= htmlspecialchars($edytowana[1] ?? '') ?>">
            <input type="text"   name="produkt"    placeholder="Produkt"    required
                   value="<?= htmlspecialchars($edytowana[2] ?? '') ?>">
            <input type="number" name="kwota"      placeholder="Kwota"      step="0.01" required
                   value="<?= htmlspecialchars($edytowana[3] ?? '') ?>">
            <input type="date"   name="data"       required
                   value="<?= htmlspecialchars($edytowana[4] ?? '') ?>">

            <button type="submit"><?= $edytowana ? 'Zapisz zmiany' : 'Zapisz transakcje' ?></button>
            <?php if ($edytowana): ?>
                <a href="SalesView.php">Anuluj</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- ── 2. STATYSTYKI MIĘDZY DATAMI ─────────────────────── -->
    <div class="section">
        <h2>2. Statystyki okresowe</h2>
        <form method="GET">
            <input type="date" name="poczatek" value="<?= htmlspecialchars($_GET['poczatek'] ?? '') ?>" required>
            <input type="date" name="koniec"   value="<?= htmlspecialchars($_GET['koniec']   ?? '') ?>" required>
            <button type="submit">Oblicz</button>
        </form>
        <?php if (isset($_GET['poczatek'], $_GET['koniec'])): ?>
            <?php $wynik = $kontroler->pobierzStatystykiMiedzyDatami($_GET['poczatek'], $_GET['koniec']); ?>
            <p class="stats">
                Liczba transakcji: <strong><?= $wynik['count'] ?></strong> &nbsp;|&nbsp;
                Suma przychodow: <strong><?= number_format($wynik['sum'], 2, ',', ' ') ?> PLN</strong>
            </p>
        <?php endif; ?>
    </div>

    <!-- ── 3. NAJWYŻSZA TRANSAKCJA ─────────────────────────── -->
    <div class="section">
        <h2>3. Transakcja z najwiekszym przychodem</h2>
        <?php if ($najwyzszaTransakcja): ?>
            <p class="stats">
                ID: <strong><?= htmlspecialchars($najwyzszaTransakcja[0]) ?></strong> &nbsp;|&nbsp;
                Produkt: <strong><?= htmlspecialchars($najwyzszaTransakcja[2]) ?></strong> &nbsp;|&nbsp;
                Kwota: <strong><?= number_format((float)$najwyzszaTransakcja[3], 2, ',', ' ') ?> PLN</strong> &nbsp;|&nbsp;
                Data: <strong><?= htmlspecialchars($najwyzszaTransakcja[4]) ?></strong>
            </p>
        <?php else: ?>
            <p>Brak danych.</p>
        <?php endif; ?>
    </div>

    <!-- ── 4. NAJLEPSZY PRODUKT ────────────────────────────── -->
    <div class="section">
        <h2>4. Produkt z najwiekszym lacznym przychodem</h2>
        <?php if ($najlepszyProdukt): ?>
            <p class="stats">
                Produkt: <strong><?= htmlspecialchars($najlepszyProdukt['produkt']) ?></strong> &nbsp;|&nbsp;
                Laczny przychod: <strong><?= number_format($najlepszyProdukt['suma'], 2, ',', ' ') ?> PLN</strong>
            </p>
        <?php else: ?>
            <p>Brak danych.</p>
        <?php endif; ?>
    </div>

    <!-- ── 5. HISTORIA TRANSAKCJI ──────────────────────────── -->
    <div class="section">
        <h2>5. Historia transakcji</h2>
        <?php if (empty($wszystkieSprzedaze)): ?>
            <p>Brak transakcji w bazie.</p>
        <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>ID Klienta</th>
                <th>Produkt</th>
                <th>Kwota (PLN)</th>
                <th>Data</th>
                <th>Akcje</th>
            </tr>
            <?php foreach ($wszystkieSprzedaze as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s[0]) ?></td>
                <td><?= htmlspecialchars($s[1]) ?></td>
                <td><?= htmlspecialchars($s[2]) ?></td>
                <td><?= number_format((float)$s[3], 2, ',', ' ') ?></td>
                <td><?= htmlspecialchars($s[4]) ?></td>
                <td class="akcje">
                    <a href="?edytuj=<?= (int)$s[0] ?>">Edytuj</a>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="akcja" value="usun">
                        <input type="hidden" name="id"    value="<?= (int)$s[0] ?>">
                        <button type="submit">Usun</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <!-- ── 6. EKSPORT RAPORTU ──────────────────────────────── -->
    <div class="section">
        <h2>6. Eksport raportu</h2>
        <p>Wygeneruj pelny raport sprzedazy ze wszystkimi statystykami.</p>
        <div class="eksport-przyciski">
            <a href="?eksport=html" class="btn-eksport btn-eksport-html">Pobierz raport HTML</a>
            <a href="?eksport=pdf"  class="btn-eksport btn-eksport-pdf">Drukuj jako PDF</a>
        </div>
    </div>

    <div class="powrot">
        <a href="../../index.php">Powrot do menu</a>
    </div>
</div>
</body>
</html>
