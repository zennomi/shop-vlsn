<?php namespace App\Models;

use CodeIgniter\Model;

class FileModel extends BaseModel
{
    protected $uploadModel;
    protected $builderImages;
    protected $builderMedia;
    protected $builderBlogImages;
    protected $builderDigitalFiles;
    protected $builderFileManager;

    public function __construct()
    {
        parent::__construct();
        $this->uploadModel = new UploadModel();
        $this->builderImages = $this->db->table('images');
        $this->builderMedia = $this->db->table('media');
        $this->builderBlogImages = $this->db->table('blog_images');
        $this->builderDigitalFiles = $this->db->table('digital_files');
        $this->builderFileManager = $this->db->table('images_file_manager');
    }

    //upload image
    public function uploadImage()
    {
        $productId = inputPost('product_id');
        $tempFile = $this->uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $data = [
                'product_id' => $productId,
                'image_default' => $this->uploadModel->uploadProductDefaultImage($tempFile['path'], 'images'),
                'image_big' => $this->uploadModel->uploadProductBigImage($tempFile['path'], 'images'),
                'image_small' => $this->uploadModel->uploadProductSmallImage($tempFile['path'], 'images'),
                'is_main' => 0,
                'storage' => 'local'
            ];
            $this->uploadModel->deleteTempFile($tempFile['path']);
            //move to s3
            if ($this->storageSettings->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $data['storage'] = 'aws_s3';
                if (!empty($data['image_default'])) {
                    $awsModel->putProductObject($data['image_default'], FCPATH . 'uploads/images/' . $data['image_default']);
                    deleteFile('uploads/images/' . $data['image_default']);
                }
                if (!empty($data['image_big'])) {
                    $awsModel->putProductObject($data['image_big'], FCPATH . 'uploads/images/' . $data['image_big']);
                    deleteFile('uploads/images/' . $data['image_big']);
                }
                if (!empty($data['image_small'])) {
                    $awsModel->putProductObject($data['image_small'], FCPATH . 'uploads/images/' . $data['image_small']);
                    deleteFile('uploads/images/' . $data['image_small']);
                }
            }
            $this->db->reconnect();
            if ($this->builderImages->insert($data)) {
                echo json_encode(['image_id' => $this->db->insertID()]);
            }
        }
    }

    //upload image session
    public function uploadImageSession()
    {
        $tempFile = $this->uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $fileId = inputPost('file_id');
            $images = $this->getSessProductImagesArray();
            $item = new \stdClass();
            $item->img_default = $this->uploadModel->uploadProductDefaultImage($tempFile['path'], 'temp');
            $item->img_big = $this->uploadModel->uploadProductBigImage($tempFile['path'], 'temp');
            $item->img_small = $this->uploadModel->uploadProductSmallImage($tempFile['path'], 'temp');
            $item->file_id = $fileId;
            $item->is_main = 0;
            $item->file_time = time();
            array_push($images, $item);
            helperSetSession('mds_product_images', $images);
            $this->uploadModel->deleteTempFile($tempFile['path']);
        }
    }

    //add product images
    public function addProductImages($productId)
    {
        $images = $this->getSessProductImagesArray();
        if (!empty($images)) {
            foreach ($images as $image) {
                if (!empty($image)) {
                    $storage = 'local';
                    $directory = $this->uploadModel->createUploadDirectory('images');
                    if ($this->storageSettings->storage == 'aws_s3') {
                        $storage = 'aws_s3';
                        $awsModel = new AwsModel();
                        $awsModel->putProductObject($directory . $image->img_default, FCPATH . 'uploads/temp/' . $image->img_default);
                        deleteFile('uploads/temp/' . $image->img_default);
                        $awsModel->putProductObject($directory . $image->img_big, FCPATH . 'uploads/temp/' . $image->img_big);
                        deleteFile('uploads/temp/' . $image->img_big);
                        $awsModel->putProductObject($directory . $image->img_small, FCPATH . 'uploads/temp/' . $image->img_small);
                        deleteFile('uploads/temp/' . $image->img_small);
                    } else {
                        copy(FCPATH . 'uploads/temp/' . $image->img_default, FCPATH . 'uploads/images/' . $directory . $image->img_default);
                        deleteFile('uploads/temp/' . $image->img_default);
                        copy(FCPATH . 'uploads/temp/' . $image->img_big, FCPATH . 'uploads/images/' . $directory . $image->img_big);
                        deleteFile('uploads/temp/' . $image->img_big);
                        copy(FCPATH . 'uploads/temp/' . $image->img_small, FCPATH . 'uploads/images/' . $directory . $image->img_small);
                        deleteFile('uploads/temp/' . $image->img_small);
                    }
                    //add to database
                    $data = [
                        'product_id' => $productId,
                        'image_default' => $directory . $image->img_default,
                        'image_big' => $directory . $image->img_big,
                        'image_small' => $directory . $image->img_small,
                        'is_main' => $image->is_main,
                        'storage' => $storage
                    ];
                    $this->db->reconnect();
                    $this->builderImages->insert($data);
                }
            }
        }
        helperDeleteSession('mds_product_images');
    }

    //set image main session
    public function setSessImageMain($fileId)
    {
        $images = $this->getSessProductImagesArray();
        if (!empty($images)) {
            foreach ($images as $image) {
                if ($image->file_id == $fileId) {
                    $image->is_main = 1;
                } else {
                    $image->is_main = 0;
                }
            }
        }
        helperSetSession('mds_product_images', $images);
    }

    //set image main
    public function setImageMain($imageId, $productId)
    {
        $images = $this->getProductImages($productId);
        if (!empty($images)) {
            foreach ($images as $image) {
                if ($image->id == $imageId) {
                    $data['is_main'] = 1;
                } else {
                    $data['is_main'] = 0;
                }
                $this->builderImages->where('id', $image->id)->update($data);
            }
        }
    }

    //get product images array session
    public function getSessProductImagesArray()
    {
        $images = array();
        if (!empty(helperGetSession('mds_product_images'))) {
            $images = helperGetSession('mds_product_images');
        }
        if (!empty($images)) {
            usort($images, function ($a, $b) {
                if ($a->file_time == $b->file_time) return 0;
                return $a->file_time < $b->file_time ? 1 : -1;
            });
        }
        return $images;
    }

    //get product images
    public function getProductImages($productId)
    {
        return $this->builderImages->where('product_id', clrNum($productId))->orderBy('images.is_main DESC')->get()->getResult();
    }

    //get product image
    public function getImage($imageId)
    {
        return $this->builderImages->where('images.id', clrNum($imageId))->get()->getRow();
    }

    //get product main image
    public function getProductMainImage($productId)
    {
        return $this->builderImages->where('product_id', clrNum($productId))->orderBy('images.is_main DESC')->get()->getRow();
    }

    //delete image session
    public function deleteImageSession($fileId)
    {
        $images = $this->getSessProductImagesArray();
        $imagesNew = array();
        if (!empty($images)) {
            foreach ($images as $image) {
                if ($image->file_id == $fileId) {
                    deleteFile('uploads/temp/' . $image->img_default);
                    deleteFile('uploads/temp/' . $image->img_big);
                    deleteFile('uploads/temp/' . $image->img_small);
                } else {
                    $item = new \stdClass();
                    $item->img_default = $image->img_default;
                    $item->img_big = $image->img_big;
                    $item->img_small = $image->img_small;
                    $item->file_id = $image->file_id;
                    $item->is_main = $image->is_main;
                    $item->file_time = $image->file_time;
                    array_push($imagesNew, $item);
                }
            }
        }
        unset($images);
        helperSetSession('mds_product_images', $imagesNew);
    }

    //delete product image
    public function deleteProductImage($imageId)
    {
        $image = $this->getImage($imageId);
        if (!empty($image)) {
            if ($image->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteProductObject($image->image_default);
                $awsModel->deleteProductObject($image->image_big);
                $awsModel->deleteProductObject($image->image_small);
            } else {
                deleteFile('uploads/images/' . $image->image_default);
                deleteFile('uploads/images/' . $image->image_big);
                deleteFile('uploads/images/' . $image->image_small);
            }
            $this->builderImages->where('id', $image->id)->delete();
        }
    }

    //delete product images
    public function deleteProductImages($productId)
    {
        $images = $this->getProductImages($productId);
        if (!empty($images)) {
            foreach ($images as $image) {
                $this->deleteProductImage($image->id);
            }
        }
    }

    /*
     * --------------------------------------------------------------------
     * File Manager
     * --------------------------------------------------------------------
     */

    //upload image
    public function uploadFileManagerImage()
    {
        $tempFile = $this->uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $data = [
                'image_path' => $this->uploadModel->uploadFileManagerImage($tempFile['path']),
                'storage' => 'local',
                'user_id' => user()->id
            ];
            //move to s3
            if ($this->storageSettings->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $data['storage'] = 'aws_s3';
                //move images
                if (!empty($data['image_path'])) {
                    $awsModel->putFileManagerImageObject($data['image_path'], FCPATH . 'uploads/images-file-manager/' . $data['image_path']);
                    deleteFile('uploads/images-file-manager/' . $data["image_path"]);
                }
            }
            $this->db->reconnect();
            $this->builderFileManager->insert($data);
            $this->uploadModel->deleteTempFile($tempFile['path']);
        }
    }

    //get user file manager images
    public function getUserFileManagerImages($userId)
    {
        return $this->builderFileManager->where('user_id', clrNum($userId))->orderBy('id DESC')->get()->getResult();
    }

    //get file manager image
    public function getFileManagerImage($fileId)
    {
        return $this->builderFileManager->where('id', clrNum($fileId))->get()->getRow();
    }

    //delete file manager image
    public function deleteFileManagerImage($fileId, $userId)
    {
        $image = $this->getFileManagerImage($fileId);
        if (!empty($image) && $image->user_id == $userId) {
            if ($image->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteFileManagerImageObject($image->image_path);
            } else {
                deleteFile('uploads/images-file-manager/' . $image->image_path);
            }
            $this->builderFileManager->where('id', $image->id)->delete();
        }
    }

    /*
     * --------------------------------------------------------------------
     * Blog Images
     * --------------------------------------------------------------------
     */

    //upload image
    public function uploadBlogImage()
    {
        $tempFile = $this->uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $data = [
                'image_path' => $this->uploadModel->uploadBlogImage($tempFile['path'], 'big'),
                'image_path_thumb' => $this->uploadModel->uploadBlogImage($tempFile['path'], 'small'),
                'storage' => 'local',
                'user_id' => user()->id
            ];
            //move to s3
            if ($this->storageSettings->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $data['storage'] = 'aws_s3';
                //move images
                if (!empty($data['image_path'])) {
                    $awsModel->putBlogObject($data['image_path'], FCPATH . $data['image_path']);
                    deleteFile($data['image_path']);
                }
                if (!empty($data['image_path_thumb'])) {
                    $awsModel->putBlogObject($data['image_path_thumb'], FCPATH . $data['image_path_thumb']);
                    deleteFile($data['image_path_thumb']);
                }
            }
            $this->db->reconnect();
            $this->builderBlogImages->insert($data);
            $this->uploadModel->deleteTempFile($tempFile['path']);
        }
    }

    //get blog images
    public function getBlogImages($limit)
    {
        return $this->builderBlogImages->orderBy('id DESC')->get(clrNum($limit))->getResult();
    }

    //load more blog images
    public function loadMoreBlogImages($min, $limit)
    {
        return $this->builderBlogImages->where('id < ', clrNum($min))->orderBy('id DESC')->get(clrNum($limit))->getResult();
    }

    //get blog image
    public function getBlogImage($id)
    {
        return $this->builderBlogImages->where('id', clrNum($id))->get()->getRow();
    }

    //delete blog image
    public function deleteBlogImage($id)
    {
        $image = $this->getBlogImage($id);
        if (!empty($image)) {
            if ($image->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteBlogObject($image->image_path);
                $awsModel->deleteBlogObject($image->image_path_thumb);
            } else {
                deleteFile($image->image_path);
                deleteFile($image->image_path_thumb);
            }
            $this->builderBlogImages->where('id', $image->id)->delete();
        }
    }

    /*
     * --------------------------------------------------------------------
     * Digital Files
     * --------------------------------------------------------------------
     */

    //upload digital files
    public function uploadDigitalFile($productId)
    {
        if (isset($_FILES['file'])) {
            if (empty($_FILES['file']['name'])) {
                exit();
            }
        }
        $product = getProduct($productId);
        if (!empty($product)) {
            $file = $this->uploadModel->uploadDigitalFile('file');
            if (!empty($file) && !empty($file['name'])) {
                $data = [
                    'product_id' => $productId,
                    'user_id' => user()->id,
                    'file_name' => $file['name'],
                    'storage' => 'local',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->reconnect();
                $this->builderDigitalFiles->insert($data);
            }
        }
    }

    //get product digital file
    public function getProductDigitalFile($productId)
    {
        return $this->builderDigitalFiles->where('product_id', clrNum($productId))->get()->getRow();
    }

    //get digital file
    public function getDigitalFile($id)
    {
        return $this->builderDigitalFiles->where('id', clrNum($id))->get()->getRow();
    }

    //create license key file
    public function createLicenseKeyFile($sale)
    {
        $path = FCPATH . 'uploads/temp/license_certificate.txt';
        @unlink($path);
        $seller = getUser($sale->seller_id);
        $buyer = getUser($sale->buyer_id);
        $product = $this->db->table('products')->where('id', clrNum($sale->product_id))->get()->getRow();
        $productDetails = getProductDetails(clrNum($sale->product_id), selectedLangId());
        $text = "\n" . strtoupper($this->generalSettings->application_name ?? '') . ' ' . strtoupper(trans("license_certificate")) . "\n==============================================\n\n";
        if (!empty($productDetails)) {
            $text .= trans("product") . ":\n";
            $text .= $productDetails->title . "\n\n";
        }
        if (!empty($product)) {
            $text .= trans("product_url") . ":\n";
            $text .= generateProductUrl($product) . "\n\n";
        }
        if (!empty($seller)) {
            $text .= trans("seller") . ":\n";
            $text .= getUsername($seller) . "\n\n";
        }
        if (!empty($buyer)) {
            $text .= trans("buyer") . ":\n";
            $text .= getUsername($buyer) . "\n\n";
        }
        $text .= trans("purchase_code") . ":\n";
        $text .= $sale->purchase_code . "\n\n";
        if (!empty($sale->license_key)) {
            $text .= trans("license_key") . ":\n";
            $text .= $sale->license_key . "\n\n";
        }
        $handle = fopen($path, "w");
        fwrite($handle, $text);
        fclose($handle);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($path));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit();
    }

    //delete digital file
    public function deleteDigitalFile($fileId)
    {
        $digitalFile = $this->getDigitalFile($fileId);
        if (!empty($digitalFile)) {
            if (($digitalFile->user_id == user()->id) || hasPermission('products')) {
                deleteFile('uploads/digital-files/' . $digitalFile->file_name);
                return $this->builderDigitalFiles->where('id', $digitalFile->id)->delete();
            }
        }
    }

    /*
     * --------------------------------------------------------------------
     * Videos
     * --------------------------------------------------------------------
     */

    //upload video
    public function uploadVideo($productId)
    {
        $fileName = '';
        $storage = 'local';
        if ($this->storageSettings->storage == 'aws_s3') {
            $awsModel = new AwsModel();
            $storage = 'aws_s3';
            $file = $this->createFileName('video_');
            if (!empty($file['name']) && !empty($file['tempName'])) {
                $fileName = $file['name'];
                $awsModel->putVideoObject($file['name'], $file['tempName']);
            }
        } else {
            $file = $this->uploadModel->uploadVideo('file');
            if (!empty($file) && !empty($file['name'])) {
                $fileName = $file['name'];
            }
        }
        if (!empty($fileName)) {
            $data = [
                'product_id' => $productId,
                'media_type' => 'video',
                'file_name' => $fileName,
                'storage' => $storage
            ];
            $this->db->reconnect();
            $this->builderMedia->insert($data);
        }
    }

    //get product video
    public function getProductVideo($productId)
    {
        return $this->builderMedia->where('product_id', clrNum($productId))->where('media_type', 'video')->get()->getRow();
    }

    //delete video
    public function deleteVideo($productId)
    {
        $video = $this->getProductVideo($productId);
        if (!empty($video)) {
            if ($video->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteVideoObject($video->file_name);
            } else {
                deleteFile('uploads/videos/' . $video->file_name);
            }
            return $this->builderMedia->where('id', $video->id)->delete();
        }
    }

    /*
     * --------------------------------------------------------------------
     * Audios
     * --------------------------------------------------------------------
     */

    //upload audio
    public function uploadAudio($productId)
    {
        $fileName = '';
        $storage = 'local';
        if ($this->storageSettings->storage == 'aws_s3') {
            $awsModel = new AwsModel();
            $storage = 'aws_s3';
            $file = $this->createFileName('audio_');
            if (!empty($file['name']) && !empty($file['tempName'])) {
                $fileName = $file['name'];
                $awsModel->putAudioObject($file['name'], $file['tempName']);
            }
        } else {
            $file = $this->uploadModel->uploadAudio('file');
            if (!empty($file) && !empty($file['name'])) {
                $fileName = $file['name'];
            }
        }
        if (!empty($fileName)) {
            $data = [
                'product_id' => $productId,
                'media_type' => 'audio',
                'file_name' => $fileName,
                'storage' => $storage
            ];
            $this->db->reconnect();
            $this->builderMedia->insert($data);
        }
    }

    //get product audio
    public function getProductAudio($productId)
    {
        return $this->builderMedia->where('product_id', clrNum($productId))->where('media_type', 'audio')->get()->getRow();
    }

    //delete audio
    public function deleteAudio($productId)
    {
        $audio = $this->getProductAudio($productId);
        if (!empty($audio)) {
            if ($audio->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                $awsModel->deleteAudioObject($audio->file_name);
            } else {
                deleteFile('uploads/audios/' . $audio->file_name);
            }
            return $this->builderMedia->where('id', $audio->id)->delete();
        }
    }

    /*
     * --------------------------------------------------------------------
     * Support Attachments
     * --------------------------------------------------------------------
     */

    //upload attachment
    public function uploadAttachment($ticketType)
    {
        $tempFile = $this->uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $data = new \stdClass();
            $data->fileId = uniqid();
            $data->name = !empty($tempFile['orjName']) ? $tempFile['orjName'] : 'file';
            $data->tempPath = $tempFile['path'];
            $data->ticketType = $ticketType;
            $filesSession = array();
            $ticketAttachments = helperGetSession('ticket_attachments');
            if (!empty($ticketAttachments)) {
                $filesSession = $ticketAttachments;
            }
            array_push($filesSession, $data);
            helperSetSession('ticket_attachments', $filesSession);
            return true;
        }
    }

    //delete attachment
    public function deleteAttachment($id)
    {
        $filesSessionNew = array();
        $ticketAttachments = helperGetSession('ticket_attachments');
        if (!empty($ticketAttachments)) {
            foreach ($ticketAttachments as $item) {
                if ($item->fileId == $id) {
                    @unlink($item->tempPath);
                } else {
                    array_push($filesSessionNew, $item);
                }
            }
        }
        helperSetSession('ticket_attachments', $filesSessionNew);
    }

    //create file name
    public function createFileName($prefix)
    {
        if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            return ['name' => $prefix . generateToken() . '.' . $ext, 'tempName' => $_FILES['file']['tmp_name']];
        }
        return null;
    }
}
