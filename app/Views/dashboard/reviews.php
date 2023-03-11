<div class="row">
    <div class="col-sm-12">
        <?= view('dashboard/includes/_messages'); ?>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= esc($title); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th scope="col"><?= trans("id"); ?></th>
                            <th scope="col"><?= trans("user"); ?></th>
                            <th scope="col"><?= trans('review'); ?></th>
                            <th scope="col"><?= trans("product"); ?></th>
                            <th scope="col"><?= trans("date"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($reviews)):
                            foreach ($reviews as $review):
                                $product = getActiveProduct($review->product_id); ?>
                                <tr>
                                    <td style="width: 5%;"><?= $review->id; ?></td>
                                    <td style="width: 10%;">
                                        <a href="<?= generateProfileUrl($review->user_slug); ?>" class="link-black" target="_blank"><?= esc($review->user_username); ?></a>
                                    </td>
                                    <td class="break-word">
                                        <div class="pull-left" style="width: 100%;">
                                            <?= view('admin/includes/_review_stars', ['review' => $review->rating]); ?>
                                        </div>
                                        <p class="pull-left"><?= esc($review->review); ?></p>
                                    </td>
                                    <td style="width: 30%;">
                                        <?php if (!empty($product)): ?>
                                            <a href="<?= generateProductUrl($product); ?>" class="link-black font-500" target="_blank"><?= getProductTitle($product); ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="white-space-nowrap" style="width: 15%"><?= formatDate($review->created_at); ?></td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($reviews)): ?>
                    <p class="text-center">
                        <?= trans("no_records_found"); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($reviews)): ?>
                    <div class="number-of-entries">
                        <span><?= trans("number_of_entries"); ?>:</span>&nbsp;&nbsp;<strong><?= $numRows; ?></strong>
                    </div>
                <?php endif; ?>
                <div class="table-pagination">
                    <?= view('partials/_pagination'); ?>
                </div>
            </div>
        </div>
    </div>
</div>