<div id="wrapper">
    <div class="container">
        <div class="auth-container">
            <div class="auth-box">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title text-success"><?= trans("msg_register_success"); ?></h1>
                        <p class="text-center" style="font-size: 15px;">
                            <?= trans("msg_send_confirmation_email"); ?>
                        </p>
                        <div class="form-group m-t-15">
                            <button type="submit" class="btn btn-custom btn-block" onclick="sendActivationEmail('<?= esc($user->token); ?>', 'register');"><?= trans("resend_activation_email"); ?></button>
                        </div>
                        <div id="confirmation-result-register"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>