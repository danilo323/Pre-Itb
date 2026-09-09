<?php
if (!function_exists('is_visible')) require_once 'content_helper.php';
$mision_visible = is_visible('sobre_mision');
$vision_visible = is_visible('sobre_vision');

if (!$mision_visible && !$vision_visible) return;
?>
<!-- ============================================= -->
<!-- 2 y 3. MISIÓN Y VISIÓN                        -->
<!-- ============================================= -->
<section class="sobre-mv-section" id="mision-y-vision">
    <div class="sobre-mv__container">
        <?php if ($mision_visible) include __DIR__ . '/sobre-mision.php'; ?>
        <?php if ($vision_visible) include __DIR__ . '/sobre-vision.php'; ?>
    </div>
</section>
