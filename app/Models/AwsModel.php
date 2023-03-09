<?php namespace App\Models;

require APPPATH . 'ThirdParty/aws-sdk/vendor/autoload.php';

use CodeIgniter\Model;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class AwsModel extends BaseModel
{
    protected $awsKey;
    protected $awsSecret;
    protected $bucket;
    protected $region;
    protected $s3;

    public function __construct()
    {
        parent::__construct();
        $this->awsKey = $this->storageSettings->aws_key;
        $this->awsSecret = $this->storageSettings->aws_secret;
        $this->bucket = $this->storageSettings->aws_bucket;
        $this->region = $this->storageSettings->aws_region;

        $credentials = new \Aws\Credentials\Credentials($this->awsKey, $this->awsSecret);
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => $credentials
        ]);
    }

    //put product object
    public function putProductObject($fileName, $tempPath)
    {
        $key = 'uploads/images/' . $fileName;
        $this->putObject($key, $tempPath);
    }

    //put file manager image object
    public function putFileManagerImageObject($fileName, $tempPath)
    {
        $key = 'uploads/images-file-manager/' . $fileName;
        $this->putObject($key, $tempPath);
    }

    //put video object
    public function putVideoObject($fileName, $tempPath)
    {
        $key = 'uploads/videos/' . $fileName;
        $this->putObject($key, $tempPath);
    }

    //put audio object
    public function putAudioObject($fileName, $tempPath)
    {
        $key = 'uploads/audios/' . $fileName;
        $this->putObject($key, $tempPath);
    }

    //put blog object
    public function putBlogObject($key, $tempPath)
    {
        $this->putObject($key, $tempPath);
    }

    //put category object
    public function putCategoryObject($key, $tempPath)
    {
        $this->putObject($key, $tempPath);
    }

    //delete product object
    public function deleteProductObject($fileName)
    {
        $key = 'uploads/images/' . $fileName;
        $this->deleteObject($key);
    }

    //delete file manager image object
    public function deleteFileManagerImageObject($fileName)
    {
        $key = 'uploads/images-file-manager/' . $fileName;
        $this->deleteObject($key);
    }

    //delete video object
    public function deleteVideoObject($fileName)
    {
        $key = 'uploads/videos/' . $fileName;
        $this->deleteObject($key);
    }

    //delete audio object
    public function deleteAudioObject($fileName)
    {
        $key = 'uploads/audios/' . $fileName;
        $this->deleteObject($key);
    }

    //delete blog object
    public function deleteBlogObject($key)
    {
        $this->deleteObject($key);
    }

    //delete category object
    public function deleteCategoryObject($key)
    {
        $this->deleteObject($key);
    }

    //put support object
    public function putSupportObject($key, $tempPath)
    {
        $this->putObject($key, $tempPath);
    }

    //put object
    public function putObject($key, $tempPath)
    {
        if (file_exists($tempPath)) {
            try {
                $file = fopen($tempPath, 'r');
                $this->s3->putObject([
                    'Bucket' => $this->bucket,
                    'Key' => $key,
                    'Body' => $file,
                    'ACL' => 'public-read'
                ]);
                fclose($file);
                return true;
            } catch (S3Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

    //delete object
    public function deleteObject($key)
    {
        if (!empty($key)) {
            try {
                $this->s3->deleteObject([
                    'Bucket' => $this->bucket,
                    'Key' => $key
                ]);
            } catch (S3Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

}
