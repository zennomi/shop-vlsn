<?php $session = session();
if ($session->getFlashdata('product_details_success')): ?>
    <div class="row-custom m-b-15">
        <div class="product-details-message success-message">
            <p>
                <i class="icon-check"></i>
                <?= $session->getFlashdata('product_details_success'); ?>
            </p>
        </div>
    </div>
<?php elseif ($session->getFlashdata('product_details_error')): ?>
    <div class="row-custom m-b-15">
        <div class="product-details-message error-message">
            <p>
                <i class="icon-times"></i>
                <?= $session->getFlashdata('product_details_error'); ?>
            </p>
        </div>
    </div>
<?php endif; ?>