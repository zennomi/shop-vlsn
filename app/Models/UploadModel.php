<?php namespace App\Models;

require_once APPPATH . 'ThirdParty/intervention-image/vendor/autoload.php';

use CodeIgniter\Model;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;

class UploadModel extends BaseModel
{
    protected $imgQuality;

    public function __construct()
    {
        parent::__construct();
        $this->imgQuality = 85;
    }

    //upload file
    private function upload($inputName, $directory, $namePrefix, $allowedExtensions = null, $keepOrjName = false)
    {
        if ($allowedExtensions != null && is_array($allowedExtensions) && !empty($allowedExtensions[0])) {
            if (!$this->checkAllowedFileTypes($inputName, $allowedExtensions)) {
                return null;
            }
        }
        $file = $this->request->getFile($inputName);
        if (!empty($file) && !empty($file->getName())) {
            $orjName = $file->getName();
            $name = pathinfo($orjName, PATHINFO_FILENAME);
            $ext = pathinfo($orjName, PATHINFO_EXTENSION);
            $name = strSlug($name);
            if (empty($name)) {
                $name = generateToken();
            }
            $uniqueName = $namePrefix . generateToken() . '.' . $ext;
            if ($keepOrjName == true) {
                $fullName = $name . '.' . $ext;
                if (file_exists(FCPATH . $directory . '/' . $fullName)) {
                    $fullName = $name . '-' . uniqid() . '.' . $ext;
                }
                $uniqueName = $fullName;
            }
            $path = $directory . $uniqueName;
            if (!$file->hasMoved()) {
                if ($file->move(FCPATH . $directory, $uniqueName)) {
                    return ['name' => $uniqueName, 'orjName' => $orjName, 'path' => $path, 'ext' => $ext];
                }
            }
        }
        return null;
    }

    //upload temp file
    public function uploadTempFile($inputName, $isImage = false)
    {
        $allowedExtensions = array();
        if ($isImage) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        }
        return $this->upload($inputName, 'uploads/temp/', 'temp_', $allowedExtensions);
    }

    //product default image upload
    public function uploadProductDefaultImage($tempPath, $folder)
    {
        $newName = 'img_x500_' . generateToken() . $this->getExt($tempPath);
        $newPath = 'uploads/' . $folder . '/' . $newName;
        if ($folder == 'images') {
            $directory = $this->createUploadDirectory('images');
            $newName = $directory . $newName;
            $newPath = 'uploads/images/' . $newName;
        }
        $img = Image::make($tempPath)->orientate();
        $img->resize(null, 500, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(FCPATH . $newPath, $this->imgQuality);
        $this->addWatermark(FCPATH . $newPath, 'product', 'default');
        return $newName;
    }

    //product big image upload
    public function uploadProductBigImage($tempPath, $folder)
    {
        $newName = 'img_1920x_' . generateToken() . $this->getExt($tempPath);
        $newPath = 'uploads/' . $folder . '/' . $newName;
        if ($folder == 'images') {
            $directory = $this->createUploadDirectory('images');
            $newName = $directory . $newName;
            $newPath = 'uploads/images/' . $newName;
        }
        $img = Image::make($tempPath)->orientate();
        $img->resize(1920, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(FCPATH . $newPath, $this->imgQuality);
        $this->addWatermark(FCPATH . $newPath, 'product', 'big');
        return $newName;
    }

    //product small image upload
    public function uploadProductSmallImage($tempPath, $folder)
    {
        $newName = 'img_x300_' . generateToken() . $this->getExt($tempPath);
        $newPath = 'uploads/' . $folder . '/' . $newName;
        if ($folder == 'images') {
            $directory = $this->createUploadDirectory('images');
            $newName = $directory . $newName;
            $newPath = 'uploads/images/' . $newName;
        }
        $img = Image::make($tempPath)->orientate();
        $img->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(FCPATH . $newPath, $this->imgQuality);
        $this->addWatermark(FCPATH . $newPath, 'product', 'small', true);
        return $newName;
    }

    //product variation small image upload
    public function uploadProductVariationSmallImage($tempPath, $folder)
    {
        $newName = 'img_x200_' . generateToken() . $this->getExt($tempPath);
        $newPath = 'uploads/' . $folder . '/' . $newName;
        if ($folder == 'images') {
            $directory = $this->createUploadDirectory('images');
            $newName = $directory . $newName;
            $newPath = 'uploads/images/' . $newName;
        }
        $img = Image::make($tempPath)->orientate();
        $img->fit(200, 200)->save(FCPATH . $newPath, $this->imgQuality);
        return $newName;
    }

    //file manager image upload
    public function uploadFileManagerImage($tempPath)
    {
        $directory = $this->createUploadDirectory('images-file-manager');
        $newName = generateToken() . $this->getExt($tempPath);
        $newPath = 'uploads/images-file-manager/' . $directory . $newName;
        $img = Image::make($tempPath)->orientate();
        $img->resize(1280, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(FCPATH . $newPath, $this->imgQuality);
        $this->addWatermark(FCPATH . $newPath, 'product', 'big');
        return $directory . $newName;
    }

    //blog image upload
    public function uploadBlogImage($tempPath, $size)
    {
        $prefix = $size == 'small' ? 'img_thumb_' : 'img_';
        $newPath = 'uploads/blog/' . $this->createUploadDirectory('blog') . $prefix . generateToken() . $this->getExt($tempPath);
        $img = Image::make($tempPath)->orientate();
        if ($size == 'small') {
            $img->fit(500, 332)->save(FCPATH . $newPath, $this->imgQuality);
            $this->addWatermark(FCPATH . $newPath, 'blog', 'small', true);
        }else{
            $img->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(FCPATH . $newPath, $this->imgQuality);
            $this->addWatermark(FCPATH . $newPath, 'blog', 'big');
        }
        return $newPath;
    }

    //category image upload
    public function uploadCategoryImage($tempPath)
    {
        $newPath = 'uploads/category/category_' . generateToken() . $this->getExt($tempPath);
        $img = Image::make($tempPath)->orientate();
        $img->fit(420, 420)->save(FCPATH . $newPath, $this->imgQuality);
        return $newPath;
    }

    //upload slider image
    public function uploadSliderImage($tempPath, $isMobile)
    {
        $newPath = 'uploads/slider/slider_' . generateToken() . $this->getExt($tempPath);
        $img = Image::make($tempPath)->orientate();
        if ($isMobile) {
            $img->fit(768, 500)->save(FCPATH . $newPath, $this->imgQuality);
        } else {
            $img->fit(1920, 600)->save(FCPATH . $newPath, $this->imgQuality);
        }
        return $newPath;
    }

    //upload avatar
    public function uploadAvatar($tempPath)
    {
        $newPath = 'uploads/profile/avatar_' . generateToken() . $this->getExt($tempPath);
        $img = Image::make($tempPath)->orientate();
        $img->fit(240, 240)->save(FCPATH . $newPath, $this->imgQuality);
        return $newPath;
    }

    //upload cover image
    public function uploadCoverImage($tempPath)
    {
        $newPath = 'uploads/profile/cover_' . generateToken() . $this->getExt($tempPath);
        $img = Image::make($tempPath)->orientate();
        $img->fit(1920, 400)->save(FCPATH . $newPath, $this->imgQuality);
        return $newPath;
    }

    //upload newsletter image
    public function uploadNewsletterImage($tempPath)
    {
        $newPath = 'uploads/blocks/img_' . generateToken() . $this->getExt($tempPath);
        $img = Image::make($tempPath)->orientate();
        $img->fit(640, 640)->save(FCPATH . $newPath, $this->imgQuality);
        return $newPath;
    }

    //vendor document upload
    public function uploadVendorDocuments()
    {
        $arrayFiles = array();
        if (!empty($_FILES['file'])) {
            for ($i = 0; $i < countItems($_FILES['file']['name']); $i++) {
                if ($_FILES['file']['size'][$i] <= 5242880) {
                    $name = $_FILES['file']['name'][$i];
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $path = 'uploads/support/file_' . generateToken() . '.' . $ext;
                    if (move_uploaded_file($_FILES['file']['tmp_name'][$i], FCPATH . $path)) {
                        $item = [
                            'name' => basename($name),
                            'path' => $path
                        ];
                        array_push($arrayFiles, $item);
                    }
                }
            }
        }
        return $arrayFiles;
    }

    //logo upload
    public function uploadLogo($inputName)
    {
        return $this->upload($inputName, 'uploads/logo/', 'logo_', ['jpg', 'jpeg', 'png', 'gif', 'svg']);
    }

    //favicon upload
    public function uploadFavicon($inputName)
    {
        return $this->upload($inputName, 'uploads/logo/', 'favicon_', ['jpg', 'jpeg', 'png', 'gif']);
    }

    //ad upload
    public function uploadAd($inputName)
    {
        return $this->upload($inputName, 'uploads/blocks/', 'block_', ['jpg', 'jpeg', 'png', 'gif']);
    }

    //ad upload
    public function uploadReceipt($inputName)
    {
        return $this->upload($inputName, 'uploads/receipts/', 'receipt_');
    }

    //logo upload
    public function uploadFlag($tempPath)
    {
        $newPath = 'uploads/blocks/flag_' . uniqid() . $this->getExt($tempPath);
        $img = Image::make($tempPath)->orientate();
        $img->resize(null, 100, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(FCPATH . $newPath);
        return $newPath;
    }

    //digital file upload
    public function uploadDigitalFile($inputName)
    {
        return $this->upload($inputName, 'uploads/digital-files/', 'digital-file-');
    }

    //video upload
    public function uploadVideo($inputName)
    {
        return $this->upload($inputName, 'uploads/videos/', 'video_', ['mp4', 'MP4', 'webm', 'WEBM']);
    }

    //audio upload
    public function uploadAudio($inputName)
    {
        return $this->upload($inputName, 'uploads/audios/', 'audio_', ['mp3', 'MP3', 'wav', 'WAV']);
    }

    //download temp image
    function downloadTempImage($url, $ext, $fileName = 'temp')
    {
        $pathJPG = FCPATH . 'uploads/temp/' . $fileName . '.jpg';
        $pathGIF = FCPATH . 'uploads/temp/' . $fileName . '.gif';
        if (file_exists($pathJPG)) {
            @unlink($pathJPG);
        }
        if (file_exists($pathGIF)) {
            @unlink($pathGIF);
        }
        $path = $pathJPG;
        if ($ext == 'gif') {
            $path = $pathGIF;
        }
        $context = stream_context_create(array(
            'http' => array(
                'header' => array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201')
            )
        ));
        if (copy($url, $path, $context)) {
            return $path;
        }
        return false;
    }

    //check allowed file types
    public function checkAllowedFileTypes($fileName, $allowedTypes)
    {
        if (!isset($_FILES[$fileName])) {
            return false;
        }
        if (empty($_FILES[$fileName]['name'])) {
            return false;
        }

        $ext = pathinfo($_FILES[$fileName]['name'], PATHINFO_EXTENSION);
        if (!empty($ext)) {
            $ext = strtolower($ext);
        }
        $extArray = array();
        if (!empty($allowedTypes) && is_array($allowedTypes)) {
            foreach ($allowedTypes as $item) {
                if (!empty($item)) {
                    $item = trim($item, '"');
                }
                if (!empty($item)) {
                    $item = trim($item, "'");
                }
                array_push($extArray, $item);
            }
        }
        if (!empty($extArray) && in_array($ext, $extArray)) {
            return true;
        }
        return false;
    }

    //add watermark
    public function addWatermark($path, $type, $size, $isThumb = false)
    {
        try {
            $image = \Config\Services::image()->withFile($path);
            $addWatermark = false;
            if ($type == 'product' && $this->generalSettings->watermark_product_images == 1) {
                $addWatermark = true;
            } elseif ($type == 'blog' && $this->generalSettings->watermark_blog_images == 1) {
                $addWatermark = true;
            }
            if ($isThumb && $this->generalSettings->watermark_thumbnail_images != 1) {
                $addWatermark = false;
            }
            $fontSize = $this->generalSettings->watermark_font_size;
            $hAlign = $this->generalSettings->watermark_hor_alignment;
            $vAlign = $this->generalSettings->watermark_vrt_alignment;
            $hOffset = 15;
            $vOffset = 0;
            if ($hAlign == 'center') {
                $hOffset = 0;
            }
            if ($vAlign == 'top') {
                $vOffset = 15;
            }
            if ($size == 'big') {
                $fontSize = round($fontSize * 2);
            } elseif ($size == 'small') {
                $fontSize = round($fontSize * 0.72);
            }
            if ($addWatermark) {
                $image->text(esc($this->generalSettings->watermark_text), [
                    'color' => '#fff',
                    'opacity' => 0.5,
                    'withShadow' => false,
                    'hAlign' => $hAlign,
                    'vAlign' => $vAlign,
                    'hOffset' => $hOffset,
                    'vOffset' => $vOffset,
                    'fontSize' => $fontSize,
                    'fontPath' => FCPATH . 'assets/fonts/open-sans/OpenSans-Bold.ttf'
                ])->save($path);
            }
        } catch (CodeIgniter\Images\Exceptions\ImageException $e) {
        }
    }

    //get file extension
    public function getExt($path, $dot = true)
    {
        if (!empty($path)) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if (!$dot) {
                return $ext;
            }
            return '.' . $ext;
        }
        return '';
    }

    //create upload directory
    public function createUploadDirectory($folder)
    {
        $directory = date('Ym');
        $directoryPath = FCPATH . 'uploads/' . $folder . '/' . $directory . '/';
        if (!is_dir($directoryPath)) {
            @mkdir($directoryPath, 0755, true);
        }
        if (!file_exists($directoryPath . "index.html")) {
            @copy(FCPATH . "uploads/index.html", $directoryPath . "index.html");
        }
        return $directory . '/';
    }

    //delete temp file
    public function deleteTempFile($path)
    {
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
