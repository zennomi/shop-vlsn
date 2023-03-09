<?= view("profile/_cover_image"); ?>
<div id="wrapper">
    <div class="container">
        <?php if (empty($user->cover_image)): ?>
            <div class="row">
                <div class="col-12">
                    <nav class="nav-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= trans("followers"); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <div class="profile-page-top">
                    <?= view('profile/_profile_user_info'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= view('profile/_tabs'); ?>
            </div>
            <div class="col-12">
                <div class="profile-tab-content">
                    <div id="user-review-result" class="user-reviews">
                        <div class="reviews-container">
                            <div class="col-12">
                                <div class="review-total">
                                    <label class="label-review"><?= trans("reviews"); ?>&nbsp;(<?= $userRating->count; ?>)</label>
                                    <?php if (!empty($reviews)):
                                        echo view('partials/_review_stars', ['rating' => $userRating->rating]);
                                    endif; ?>
                                </div>
                                <?php if (empty($reviews)): ?>
                                    <p class="no-comments-found"><?= trans("no_reviews_found"); ?></p>
                                <?php else: ?>
                                    <ul class="list-unstyled list-reviews">
                                        <?php foreach ($reviews as $review): ?>
                                            <li class="media">
                                                <a href="<?= generateProfileUrl($review->user_slug); ?>">
                                                    <img src="<?= getUserAvatarById($review->user_id); ?>" alt="<?= getUsernameByUserId($review->user_id); ?>">
                                                </a>
                                                <div class="media-body">
                                                    <?php $reviewProduct = getActiveProduct($review->product_id);
                                                    if (!empty($reviewProduct)):?>
                                                        <div class="row-custom m-b-10">
                                                            <a href="<?= generateProductUrlBySlug($reviewProduct->slug); ?>"><strong><?= trans("product"); ?>:&nbsp;</strong><?= getProductTitle($reviewProduct); ?></a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="row-custom">
                                                        <?= view('partials/_review_stars', ['rating' => $review->rating]); ?>
                                                    </div>
                                                    <div class="row-custom">
                                                        <a href="<?= generateProfileUrl($review->user_slug); ?>">
                                                            <h5 class="username"><?= getUsernameByUserId($review->user_id); ?></h5>
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
                                                <?php if (authCheck() && user()->id == $user->id): ?>
                                                    <a href="javascript:void(0)" class="text-muted link-abuse-report" data-toggle="modal" data-target="#reportReviewModal" onclick="$('#report_review_id').val('<?= $review->id; ?>');">
                                                        <?= trans("report"); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 m-t-15">
                                <div class="float-right">
                                    <?= view('partials/_pagination'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-custom">
                        <?= view('partials/_ad_spaces', ['adSpace' => 'profile', 'class' => 'm-t-30']); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php if (authCheck() && user()->id == $user->id): ?>
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
                                    <label><?= trans("description"); ?></label>
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
<?php endif; ?>

