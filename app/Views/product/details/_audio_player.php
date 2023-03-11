<link rel="stylesheet" href="<?= base_url('assets/vendor/amplitudejs/app.min.css'); ?>"/>
<div class="product-audio-preview">
    <div id="single-song-player">
        <img data-amplitude-song-info="cover_art_url"/>
        <div class="bottom-container">
            <progress class="amplitude-song-played-progress" id="song-played-progress"></progress>
            <div class="time-container"><span class="current-time"><span class="amplitude-current-minutes"></span>:<span class="amplitude-current-seconds"></span></span>
                <span class="duration"><span class="amplitude-duration-minutes"></span>:<span class="amplitude-duration-seconds"></span></span>
            </div>
        </div>
    </div>
    <div class="player-control-container">
        <button class="btn btn-secondary btn-backward" onclick="skipBackward();"><i class="icon-backward m-r-5"></i><?= trans("backward"); ?></button>
        <button class="btn btn-secondary btn-play-pause amplitude-play-pause"><i class="icon-play m-r-5"></i><?= trans("play"); ?>&nbsp;/&nbsp;<i class="icon-pause m-r-5"></i><?= trans("pause"); ?></button>
        <button class="btn btn-secondary btn-forward" onclick="skipForward();"><i class="icon-forward m-r-5"></i><?= trans("forward"); ?></button>
    </div>
</div>
<script src="<?= base_url('assets/vendor/amplitudejs/amplitude.min.js'); ?>"></script>
<script>
    Amplitude.init({
        "songs": [
            {
                "name": "",
                "artist": "",
                "album": "",
                "url": "<?= getProductAudioUrl($audio); ?>",
                "cover_art_url": "<?= getProductMainImage($product->id, 'image_big'); ?>"
            }
        ]
    });
    document.getElementById('song-played-progress').addEventListener('click', function (e) {
        var offset = this.getBoundingClientRect();
        var x = e.pageX - offset.left;
        Amplitude.setSongPlayedPercentage((parseFloat(x) / parseFloat(this.offsetWidth)) * 100);
    });
    function skipBackward() {
        var progress = $('#song-played-progress').val();
        var progress = progress * 100;
        var newProgress = progress - 5;
        Amplitude.setSongPlayedPercentage(newProgress);
    }
    function skipForward() {
        var progress = $('#song-played-progress').val();
        var progress = progress * 100;
        var newProgress = progress + 5;
        Amplitude.setSongPlayedPercentage(newProgress);
    }
</script>

