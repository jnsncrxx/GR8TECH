<?php
$content = file_get_contents('c:\\xampp\\htdocs\\GR8TECH\\resources\\views\\employees\\edit.blade.php');

$sections = [
    'personal' => '/\{\{-- ═══════════════════════════════════════════════\s*1\. PERSONAL INFORMATION.*?--\}\}.*?(?=\{\{-- ═══════════════════════════════════════════════)/s',
    'id' => '/\{\{-- ═══════════════════════════════════════════════\s*IDENTIFICATION.*?--\}\}.*?(?=\{\{-- ═══════════════════════════════════════════════)/s',
    'work' => '/\{\{-- ═══════════════════════════════════════════════\s*2\. WORK INFORMATION.*?--\}\}.*?(?=\{\{-- ═══════════════════════════════════════════════)/s',
    'details' => '/\{\{-- ═══════════════════════════════════════════════\s*3\. ADDITIONAL EMPLOYEE DETAILS.*?--\}\}.*?(?=\{\{-- ═══════════════════════════════════════════════)/s',
    'emergency' => '/\{\{-- ═══════════════════════════════════════════════\s*4\. IN CASE OF EMERGENCY.*?--\}\}.*?(?=\{\{-- ═══════════════════════════════════════════════)/s',
    'loans' => '/\{\{-- ═══════════════════════════════════════════════\s*5\. EMPLOYEE LOANS.*?--\}\}.*?(?=\{\{-- ═══════════════════════════════════════════════)/s',
    'banking' => '/\{\{-- ═══════════════════════════════════════════════\s*6\. BANKING & IDS.*?--\}\}.*?(?=        <div style="display:flex;justify-content:flex-end;gap:1rem;)/s',
];

$blocks = [];
$matches = [];

foreach ($sections as $key => $pattern) {
    if (preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE)) {
        $blocks[$key] = $match[0][0];
        $matches[$key] = $match[0];
    } else {
        echo "Missing: $key\n";
        exit(1);
    }
}

$left_col = '<div style="display:flex;flex-direction:column;gap:1.5rem;">'."\n".$blocks['id'].$blocks['personal'].$blocks['banking'].$blocks['loans'].'</div>';
$right_col = '<div style="display:flex;flex-direction:column;gap:1.5rem;">'."\n".$blocks['work'].$blocks['details'].$blocks['emergency'].'</div>';

$grid = '<div class="grid-2" style="align-items:start;">'."\n".$left_col."\n".$right_col."\n</div>\n\n";

$start_idx = $matches['personal'][1];
$end_idx = $matches['banking'][1] + strlen($matches['banking'][0]);

$new_content = substr($content, 0, $start_idx) . $grid . substr($content, $end_idx);

file_put_contents('c:\\xampp\\htdocs\\GR8TECH\\resources\\views\\employees\\edit.blade.php', $new_content);
echo "Done!\n";
