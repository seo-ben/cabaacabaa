<?php
$dir = new RecursiveDirectoryIterator('c:\composer\cabaacabaa\resources\views\admin');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$replacements = [
    '/\brounded-\[3rem\]\b/' => 'rounded-xl',
    '/\brounded-\[2\.5rem\]\b/' => 'rounded-xl',
    '/\brounded-\[2rem\]\b/' => 'rounded-xl',
    '/\brounded-3xl\b/' => 'rounded-xl',
    '/\brounded-2xl\b/' => 'rounded-lg',
    '/\bp-8\b/' => 'p-5',
    '/\bp-10\b/' => 'p-6',
    '/\bp-6\b/' => 'p-4',
    '/\bpx-8\b/' => 'px-5',
    '/\bpy-8\b/' => 'py-5',
    '/\bpx-6\b/' => 'px-4',
    '/\bpy-6\b/' => 'py-4',
    '/\bgap-8\b/' => 'gap-5',
    '/\bgap-6\b/' => 'gap-4',
    '/\bmb-10\b/' => 'mb-6',
    '/\bmb-8\b/' => 'mb-5',
    '/\bmt-10\b/' => 'mt-6',
    '/\bmt-8\b/' => 'mt-5',
    '/\bw-16 h-16\b/' => 'w-12 h-12',
    '/\bw-20 h-20\b/' => 'w-14 h-14',
    '/\bw-24 h-24\b/' => 'w-16 h-16',
    '/\btext-5xl\b/' => 'text-3xl',
    '/\btext-4xl\b/' => 'text-2xl',
    '/\btext-3xl\b/' => 'text-xl',
];

$updatedCount = 0;

foreach($files as $file) {
    if (strpos($file[0], 'dashboard.blade.php') !== false) continue;
    
    $content = file_get_contents($file[0]);
    $newContent = preg_replace(array_keys($replacements), array_values($replacements), $content);
    
    if($content !== $newContent) {
        file_put_contents($file[0], $newContent);
        echo "Updated: " . $file[0] . "\n";
        $updatedCount++;
    }
}

echo "Total updated files: " . $updatedCount . "\n";
