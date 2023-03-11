<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<rss version="2.0"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:admin="http://webns.net/mvcb/"
     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
<title><?= xml_convert($feedName); ?></title>
<link><?= $feedUrl; ?></link>
<description><?= convertToXmlCharacter(xml_convert($pageDescription)); ?></description>
<dc:language><?= $pageLanguage; ?></dc:language>
<dc:creator><?= $creatorEmail; ?></dc:creator>
<dc:rights><?= convertToXmlCharacter(xml_convert($baseSettings->copyright)); ?></dc:rights>
<?php foreach ($products as $product):
    $productDetails= getProductDetails($product->id, selectedLangId(),true); ?>
<item>
    <title><?= convertToXmlCharacter(xml_convert(getProductTitle($product))); ?></title>
    <link><?= generateProductUrl($product); ?></link>
    <guid><?= generateProductUrl($product); ?></guid>
    <description><![CDATA[<div class="price"><p>✔ <?= trans("price") . ': ' . priceFormatted($product->price,$product->currency). ' '; ?></p></div><div class="description"><?= !empty($productDetails) ? $productDetails->description : ''; ?></div>]]></description>
<?php $imagePath = '';
$fileSize = 12;
if (!empty($product) && !empty($product->image)) {
    $image = $product->image;
    if (!empty($image)) {
        $imageArray = explode("::", $image);
        if (!empty($imageArray[0]) && !empty($imageArray[1])) {
            if ($imageArray[0] == 'aws_s3') {
                $imagePath = getAWSBaseUrl() . 'uploads/images/' . $imageArray[1];
            } else {
                $imagePath = base_url() . '/uploads/images/' . $imageArray[1];
                $fileSize = @filesize(FCPATH . 'uploads/images/' . $imageArray[1]);
            }
        }
    }
}
if (!empty($imagePath)):
    $imagePath = str_replace( 'https://', 'http://', $imagePath); ?>
    <enclosure url="<?= convertToXmlCharacter($imagePath); ?>" length="<?= (isset($fileSize)) ? $fileSize : ''; ?>" type="image/jpeg"/>
<?php endif; ?>
    <pubDate><?= date('r', strtotime($product->created_at)); ?></pubDate>
    <dc:creator><?= convertToXmlCharacter($product->user_username); ?></dc:creator>
</item>
<?php endforeach; ?>
</channel>
</rss>
