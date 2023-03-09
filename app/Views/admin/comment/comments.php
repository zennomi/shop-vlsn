<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('product_comments'); ?></h3>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= $title; ?></h3>
        </div>
        <div class="right">
            <a href="<?= $topButtonUrl; ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-bars"></i>&nbsp;&nbsp;<?= $topButtonText; ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid" aria-describedby="example1_info">
                        <thead>
                        <tr role="row">
                            <th width="20" class="table-no-sort" style="text-align: center !important;"><input type="checkbox" class="checkbox-table" id="checkAll"></th>
                            <th width="20"><?= trans('id'); ?></th>
                            <th><?= trans('name'); ?></th>
                            <th><?= trans('email'); ?></th>
                            <th><?= trans('comment'); ?></th>
                            <th style="min-width: 20%"><?= trans('url'); ?></th>
                            <th><?= trans('ip_address'); ?></th>
                            <th style="min-width: 10%"><?= trans('date'); ?></th>
                            <th class="max-width-120"><?= trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($comments)):
                            foreach ($comments as $item): ?>
                                <tr>
                                    <td style="text-align: center !important;"><input type="checkbox" name="checkbox-table" class="checkbox-table" value="<?= $item->id; ?>"></td>
                                    <td><?= esc($item->id); ?></td>
                                    <td><?= esc($item->name); ?></td>
                                    <td><?= esc($item->email); ?></td>
                                    <td class="break-word"><?= esc($item->comment); ?></td>
                                    <td>
                                        <?php $product = getProduct($item->product_id);
                                        if (!empty($product)): ?>
                                            <a href="<?= generateProductUrl($product); ?>" target="_blank"><?= getProductTitle($product); ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item->ip_address); ?></td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <form action="<?= base_url('ProductController/approveCommentPost'); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $item->id; ?>">
                                            <div class="dropdown">
                                                <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?><span class="caret"></span></button>
                                                <ul class="dropdown-menu options-dropdown">
                                                    <?php if ($item->status != 1): ?>
                                                        <li>
                                                            <button type="submit"><i class="fa fa-check option-icon"></i><?= trans("approve"); ?></button>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li><a href="javascript:void(0)" onclick="deleteItem('ProductController/deleteCommentPost','<?= $item->id; ?>','<?= trans("confirm_comment", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a></li>
                                                </ul>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($comments)): ?>
                        <p class="text-center"><?= trans("no_records_found"); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="pull-left">
                            <button class="btn btn-sm btn-danger btn-table-delete" onclick="deleteSelectedComments('<?= trans("confirm_comments", true); ?>');"><?= trans('delete'); ?></button>
                            <?php if ($showApproveButton == true): ?>
                                <button class="btn btn-sm btn-success btn-table-delete" onclick="approveSelectedComments();"><?= trans('approve'); ?></button>
                            <?php endif; ?>
                        </div>
                        <div class="pull-right">
                            <?= view('partials/_pagination'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>