<div class="reviews-container">
    <div class="row">
        <div class="col-12">
            <div class="review-total">
                <?php if (!empty($reviews)):
                    echo view('partials/_review_stars', ['rating' => $product->rating]);
                endif; ?>
                <label class="label-review"><?= trans("reviews"); ?>&nbsp;(<?= $reviewCount; ?>)</label>
                <?php $btnAddReview = false;
                if (authCheck() && $product->user_id != user()->id) {
                    if ($product->listing_type == 'ordinary_listing') {
                        $btnAddReview = true;
                    } else {
                        if ($product->is_free_product) {
                            $btnAddReview = true;
                        } else {
                            if (checkUserBoughtProduct(user()->id, $product->id)) {
                                $btnAddReview = true;
                            }
                        }
                    }
                } ?>
                <?php if ($btnAddReview): ?>
                    <button type="button" data-product-id="<?= $product->id; ?>" class="btn btn-sm btn-custom display-flex align-items-center m-l-15" data-toggle="modal" data-target="#rateProductModal" onclick="$('#review_product_id').val(<?= $product->id; ?>);">
                        <?= trans("add_review"); ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php if (empty($reviews)): ?>
                <p class="no-comments-found"><?= trans("no_reviews_found"); ?></p>
            <?php else: ?>
                <ul class="list-unstyled list-reviews">
                    <?php foreach ($reviews as $review):
                        $reviewUser = getUser($review->user_id);
                        if (!empty($reviewUser)):?>
                            <li class="media">
                                <a href="<?= generateProfileUrl($reviewUser->slug); ?>">
                                    <img src="<?= getUserAvatar($reviewUser); ?>" alt="<?= esc(getUsername($reviewUser)); ?>">
                                </a>
                                <div class="media-body">
                                    <div class="row-custom">
                                        <?= view('partials/_review_stars', ['rating' => $review->rating]); ?>
                                    </div>
                                    <div class="row-custom">
                                        <a href="<?= generateProfileUrl($reviewUser->slug); ?>">
                                            <h5 class="username"><?= esc(getUsername($reviewUser)); ?></h5>
                                        </a>
                                    </div>
                                    <div class="row-custom">
                                        <div class="review">
                                            <?= esc($review->review); ?>
                                        </div>
                                    </div>
                                    <div class="row-custom">
                                        <span class="date"><?= timeAgo($review->created_at); ?></span>
                                    </div>
                                </div>
                                <?php if (authCheck() && user()->id == $product->user_id): ?>
                                    <a href="javascript:void(0)" class="text-muted link-abuse-report" data-toggle="modal" data-target="#reportReviewModal" onclick="$('#report_review_id').val('<?= $review->id; ?>');">
                                        <?= trans("report"); ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endif;
                    endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (authCheck() && user()->id == $product->user_id): ?>
    <div class="modal fade" id="reportReviewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-custom">
                <form id="form_report_review" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= trans("report_review"); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true"><i class="icon-close"></i> </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div id="response_form_report_review" class="col-12"></div>
                            <div class="col-12">
                                <input type="hidden" id="report_review_id" name="id" value="">
                                <div class="form-group m-0">
                                    <label class="control-label"><?= trans("description"); ?></label>
                                    <textarea name="description" class="form-control form-textarea" placeholder="<?= trans("abuse_report_exp"); ?>" minlength="5" maxlength="10000" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif;
echo view('partials/_modal_rate_product'); ?>