<?php if (!empty($adSpace)):
    $adCodes = null;
    foreach ($adSpaces as $item) {
        if ($item->ad_space == $adSpace) {
            $adCodes = $item;
        }
    }
    if (!empty($adCodes)):
        $adCodeDesktop = trim($adCodes->ad_code_desktop ?? '');
        $adCodeMobile = trim($adCodes->ad_code_mobile ?? '');
        if ($adCodeDesktop != ''): ?>
            <div class="container">
                <div class="row">
                    <div class="bn-container bn-container-ds<?= isset($class) ? ' ' . $class : ''; ?>">
                        <div class="bn-inner bn-ds-<?= $adCodes->id; ?>">
                            <?= $adCodeDesktop; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
        if ($adCodeMobile != ''): ?>
            <div class="container">
                <div class="row">
                    <div class="bn-container bn-container-mb<?= isset($class) ? ' ' . $class : ''; ?>">
                        <div class="bn-inner bn-mb-<?= $adCodes->id; ?>">
                            <?= $adCodeMobile; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
    endif;
endif; ?>
