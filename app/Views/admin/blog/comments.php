<div class="row" style="margin-bottom: 15px;">
    <div class="col-sm-12">
        <h3 style="font-size: 18px; font-weight: 600;"><?= trans('blog_comments'); ?></h3>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= $title; ?></h3>
        </div>
        <div class="right">
            <a href="<?= $topButtonURL; ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-bars"></i>&nbsp;&nbsp;<?= $topButtonText; ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped data_table" role="grid" aria-describedby="example1_info">
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
                        <?php $model = new \App\Models\BlogModel();
                        if (!empty($comments)):
                            foreach ($comments as $item):?>
                                <tr>
                                    <td style="text-align: center !important;"><input type="checkbox" name="checkbox-table" class="checkbox-table" value="<?= $item->id; ?>"></td>
                                    <td><?= esc($item->id); ?></td>
                                    <td><?= esc($item->name); ?></td>
                                    <td><?= esc($item->email); ?></td>
                                    <td class="break-word"><?= esc($item->comment); ?></td>
                                    <td>
                                        <?php $post = $model->getPostJoined($item->post_id);
                                        if (!empty($post)): ?>
                                            <a href="<?= generatePostUrl($post); ?>" target="_blank"><?= esc($post->title); ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item->ip_address); ?></td>
                                    <td><?= formatDate($item->created_at); ?></td>
                                    <td>
                                        <form action="<?= base_url('BlogController/approveCommentPost'); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= $item->id; ?>">
                                            <div class="dropdown">
                                                <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?= trans('select_option'); ?><span class="caret"></span></button>
                                                <ul class="dropdown-menu options-dropdown">
                                                    <?php if ($item->status != 1): ?>
                                                        <li><button type="submit"><i class="fa fa-check option-icon"></i><?= trans("approve"); ?></button></li>
                                                    <?php endif; ?>
                                                    <li><a href="javascript:void(0)" onclick="deleteItem('BlogController/deleteComment','<?= $item->id; ?>','<?= trans("confirm_comment", true); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a></li>
                                                </ul>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="pull-left">
                            <button class="btn btn-sm btn-danger btn-table-delete" onclick="deleteSelectedBlogComments('<?= trans("confirm_comments", true); ?>');"><?= trans('delete'); ?></button>
                            <?php if ($showApproveButton == true): ?>
                                <button class="btn btn-sm btn-success btn-table-delete" onclick="approveSelectedBlogComments();"><?= trans('approve'); ?></button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>