<div class="row">
    <div class="col-12">
        <form id="form_add_blog_comment">
            <input type="hidden" name="post_id" value="<?= $post->id; ?>">
            <?php if (!authCheck()): ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" name="name" id="comment_name" class="form-control form-input" placeholder="<?= trans("name"); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <input type="email" name="email" id="comment_email" class="form-control form-input" placeholder="<?= trans("email_address"); ?>">
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <textarea name="comment" id="comment_text" class="form-control form-input form-textarea" placeholder="<?= trans("comment"); ?>"></textarea>
            </div>
            <?php if(!authCheck()): ?>
            <div class="form-group">
                <?php reCaptcha('generate'); ?>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
            </div>
        </form>
    </div>
    <div class="col-12">
        <div id="message-comment-result" class="message-comment-result"></div>
    </div>
    <div class="col-12">
        <div id="comment-result">
            <?= view('blog/_blog_comments', ['commentPostId' => $post->id]); ?>
        </div>
    </div>
</div>