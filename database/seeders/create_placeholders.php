<?php

$dir = __DIR__ . '/../../public/images/placeholders';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$icons = [
    'paper.svg' => ['📄', '#4F46E5', 'Giấy In & Photo'],
    'pen.svg' => ['✏️', '#059669', 'Bút Viết & Mực'],
    'folder.svg' => ['📁', '#D97706', 'Bìa & File Hồ Sơ'],
    'notebook.svg' => ['📓', '#2563EB', 'Sổ Tay & Giấy Note'],
    'stationery.svg' => ['✂️', '#DC2626', 'Dụng Cụ Văn Phòng'],
    'toner.svg' => ['🖨️', '#7C3AED', 'Hộp Mực Máy In'],
    'parts.svg' => ['⚙️', '#EA580C', 'Linh Kiện Máy In'],
    'default-product.svg' => ['📦', '#64748B', 'Sản Phẩm VPP'],
];

foreach ($icons as $filename => [$emoji, $bg, $text]) {
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">
  <rect width="200" height="200" rx="24" fill="{$bg}" fill-opacity="0.12"/>
  <rect x="2" y="2" width="196" height="196" rx="22" fill="none" stroke="{$bg}" stroke-width="2" stroke-opacity="0.3"/>
  <text x="50%" y="48%" font-size="52" text-anchor="middle" dominant-baseline="middle">{$emoji}</text>
  <text x="50%" y="78%" font-size="13" font-family="sans-serif" font-weight="bold" fill="{$bg}" text-anchor="middle">{$text}</text>
</svg>
SVG;
    file_put_contents("$dir/$filename", $svg);
    echo "Created: $filename\n";
}
