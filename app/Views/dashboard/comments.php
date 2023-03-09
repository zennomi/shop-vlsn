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
                    <table class="table table-striped">
                        <thead>
                        <tr role="row">
                            <th scope="col"><?= trans("id"); ?></th>
                            <th scope="col"><?= trans("username"); ?></th>
                            <th scope="col"><?= trans("comment"); ?></th>
                            <th scope="col"><?= trans("product"); ?></th>
                            <th scope="col"><?= trans("date"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($comments)):
                            foreach ($comments as $comment):
                                $product = getActiveProduct($comment->product_id); ?>
                                <tr>
                                    <td style="width: 5%;"><?= $comment->id; ?></td>
                                    <td style="width: 10%;">
                                        <a href="<?= generateProfileUrl($comment->user_slug); ?>" class="link-black" target="_blank"><?= esc($comment->name); ?></a>
                                    </td>
                                    <td style="width: 40%;"><?= esc($comment->comment); ?></td>
                                    <td style="width: 30%;">
                                        <?php if (!empty($product)): ?>
                                            <a href="<?= generateProductUrl($product); ?>" class="link-black font-500" target="_blank"><?= getProductTitle($product); ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="white-space-nowrap" style="width: 15%"><?= formatDate($comment->created_at); ?></td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($comments)): ?>
                    <p class="text-center">
                        <?= trans("no_records_found"); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($comments)): ?>
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