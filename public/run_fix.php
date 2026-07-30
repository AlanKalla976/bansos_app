<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use App\Models\Pengajuan;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

echo "--- RUNNING PHOTO FIXER VIA RUN_FIX DEBUG ---\n";

$pengajuans = Pengajuan::all();
echo "Total records: " . count($pengajuans) . "\n";

$template_images = [];
$pengajuan_dir = public_path('storage/pengajuan');
if (file_exists($pengajuan_dir)) {
    $files = scandir($pengajuan_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file($pengajuan_dir . '/' . $file)) {
            $template_images[] = $pengajuan_dir . '/' . $file;
        }
    }
}
echo "Found " . count($template_images) . " template images.\n";
if (!empty($template_images)) {
    echo "First template: " . $template_images[0] . "\n";
}

$fallback = public_path('images/logo-pemkot.png');
echo "Fallback path: " . $fallback . " (exists: " . (file_exists($fallback) ? "YES" : "NO") . ")\n";

function get_template_for_field($field, $templates, $fallback) {
    if (empty($templates)) {
        return $fallback;
    }
    switch ($field) {
        case 'foto_ktp':
            return $templates[0] ?? $fallback;
        case 'foto_kk':
            return $templates[1] ?? ($templates[0] ?? $fallback);
        case 'foto_sktm':
            return $templates[2] ?? ($templates[0] ?? $fallback);
        case 'foto_rumah':
            return $templates[3] ?? ($templates[0] ?? $fallback);
        default:
            return $fallback;
    }
}

// Let's test the first Pengajuan record that has ktp/ktp_5.jpg
$p = Pengajuan::find(5);
if ($p) {
    echo "Testing ID 5:\n";
    foreach (['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah'] as $field) {
        $val = $p->$field;
        echo "  $field = $val\n";
        if ($val) {
            $dest1 = public_path('storage/' . $val);
            $dest2 = storage_path('app/public/' . $val);
            $exists1 = file_exists($dest1) ? "YES" : "NO";
            $exists2 = file_exists($dest2) ? "YES" : "NO";
            echo "    dest1: $dest1 (exists: $exists1)\n";
            echo "    dest2: $dest2 (exists: $exists2)\n";
            
            $src = get_template_for_field($field, $template_images, $fallback);
            echo "    src: $src (exists: " . (file_exists($src) ? "YES" : "NO") . ")\n";
        }
    }
}
?>
