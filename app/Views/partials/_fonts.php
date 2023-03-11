<?php $font = getFontClient($activeFonts, 'site');
if(!empty($font)):
if($font->font_source=='local' && $font->has_local_file):
if($font->font_key == 'open-sans'):?>
<style>@font-face {font-family: 'Open Sans'; font-style: normal; font-weight: 400; font-display: swap; src: local(''), url('<?= base_url('assets/fonts/open-sans/open-sans-400.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/open-sans/open-sans-400.woff'); ?>') format('woff')}  @font-face {font-family: 'Open Sans'; font-style: normal; font-weight: 600; font-display: swap; src: local(''), url('<?= base_url('assets/fonts/open-sans/open-sans-600.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/open-sans/open-sans-600.woff'); ?>') format('woff')}  @font-face {font-family: 'Open Sans'; font-style: normal; font-weight: 700; font-display: swap; src: local(''), url('<?= base_url('assets/fonts/open-sans/open-sans-700.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/open-sans/open-sans-700.woff'); ?>') format('woff')}</style>
<?php elseif ($font->font_key == 'poppins'):?>
<style>@font-face {font-family: 'Poppins'; font-style: normal; font-weight: 400; font-display: swap; src: local(''), url('<?= base_url('assets/fonts/poppins/poppins-400.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/poppins/poppins-400.woff'); ?>') format('woff')}  @font-face {font-family: 'Poppins'; font-style: normal; font-weight: 600; font-display: swap; src: local(''), url('<?= base_url('assets/fonts/poppins/poppins-600.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/poppins/poppins-600.woff'); ?>') format('woff')}  @font-face {font-family: 'Poppins'; font-style: normal; font-weight: 700; font-display: swap; src: local(''), url('<?= base_url('assets/fonts/poppins/poppins-700.woff2'); ?>') format('woff2'), url('<?= base_url('assets/fonts/poppins/poppins-700.woff'); ?>') format('woff')}</style>
<?php endif;
else:echo $font->font_url;endif;
$fontFamilyArray = explode(':', $font->font_family ?? '');
$fontFamily = isset($fontFamilyArray[1]) ? trim($fontFamilyArray[1] ?? '') : ''; ?>
<style>:root {--vr-font-main:<?= $fontFamily; ?>;</style>
<?php endif;?>