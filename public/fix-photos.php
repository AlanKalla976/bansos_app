<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use App\Models\Pengajuan;

// Boot application kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

echo "--- RUNNING PHOTO FIXER FOR REAL ---\n";

$pengajuans = Pengajuan::all();
echo "Total records: " . count($pengajuans) . "\n";

// Target directories
$dirs = ['ktp', 'kk', 'sktm', 'rumah'];
foreach ($dirs as $dir) {
    $p_dir1 = public_path('storage/' . $dir);
    $p_dir2 = storage_path('app/public/' . $dir);
    if (!file_exists($p_dir1)) {
        mkdir($p_dir1, 0755, true);
        echo "Created directory: $p_dir1\n";
    }
    if (!file_exists($p_dir2)) {
        mkdir($p_dir2, 0755, true);
        echo "Created directory: $p_dir2\n";
    }
}

// Get some existing images from public/storage/pengajuan to use as templates
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
$fallback = public_path('images/logo-pemkot.png');

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

$copied_count = 0;
foreach ($pengajuans as $p) {
    foreach (['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah'] as $field) {
        $val = $p->$field;
        if ($val) {
            $dest1 = public_path('storage/' . $val);
            $dest2 = storage_path('app/public/' . $val);
            
            $exists1 = file_exists($dest1);
            $exists2 = file_exists($dest2);
            
            if (!$exists1 || !$exists2) {
                $src = get_template_for_field($field, $template_images, $fallback);
                if (file_exists($src)) {
                    if (!$exists1) {
                        $parent1 = dirname($dest1);
                        if (!file_exists($parent1)) {
                            mkdir($parent1, 0755, true);
                        }
                        copy($src, $dest1);
                    }
                    if (!$exists2) {
                        $parent2 = dirname($dest2);
                        if (!file_exists($parent2)) {
                            mkdir($parent2, 0755, true);
                        }
                        copy($src, $dest2);
                    }
                    $copied_count++;
                }
            }
        }
    }
}

echo "Completed. Total file copies created: {$copied_count}\n";
