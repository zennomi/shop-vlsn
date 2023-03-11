<?php namespace App\Models;

use CodeIgniter\Model;

class VariationModel extends BaseModel
{
    protected $builder;
    protected $builderVariationOptions;
    protected $builderImagesVariation;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('variations');
        $this->builderVariationOptions = $this->db->table('variation_options');
        $this->builderImagesVariation = $this->db->table('images_variation');
    }

    //add variation
    public function addVariation()
    {
        $productId = inputPost('product_id');
        $arrayNames = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'label' => inputPost('label_lang_' . $language->id)
            ];
            array_push($arrayNames, $item);
        }
        $arrayNames = serialize($arrayNames);
        $data = [
            'product_id' => $productId,
            'user_id' => user()->id,
            'parent_id' => inputPost('parent_id'),
            'label_names' => $arrayNames,
            'variation_type' => inputPost('variation_type'),
            'insert_type' => 'new',
            'option_display_type' => inputPost('option_display_type'),
            'show_images_on_slider' => inputPost('show_images_on_slider'),
            'use_different_price' => inputPost('use_different_price'),
            'is_visible' => inputPost('is_visible')
        ];
        if (empty($data['parent_id'])) {
            $data['parent_id'] = 0;
        }
        if (empty($data['show_images_on_slider'])) {
            $data['show_images_on_slider'] = 0;
        }
        if (empty($data['use_different_price'])) {
            $data['use_different_price'] = 0;
        }
        $this->builder->insert($data);
    }

    //edit variation
    public function editVariation($id)
    {
        $variation = $this->getVariation($id);
        if (!empty($variation)) {
            $arrayNames = array();
            foreach ($this->activeLanguages as $language) {
                $item = [
                    'lang_id' => $language->id,
                    'label' => inputPost('label_lang_' . $language->id)
                ];
                array_push($arrayNames, $item);
            }
            $arrayNames = serialize($arrayNames);
            $data = [
                'parent_id' => inputPost('parent_id'),
                'label_names' => $arrayNames,
                'variation_type' => inputPost('variation_type'),
                'option_display_type' => inputPost('option_display_type'),
                'show_images_on_slider' => inputPost('show_images_on_slider'),
                'use_different_price' => inputPost('use_different_price'),
                'is_visible' => inputPost('is_visible')
            ];
            if (empty($data['parent_id'])) {
                $data['parent_id'] = 0;
            }
            if (empty($data['show_images_on_slider'])) {
                $data['show_images_on_slider'] = 0;
            }
            if (empty($data['use_different_price'])) {
                $data['use_different_price'] = 0;
            }
            return $this->builder->where('id', clrNum($id))->update($data);
        }
        return false;
    }

    //get variation
    public function getVariation($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //select variation
    public function selectVariation($variationId, $productId)
    {
        $variation = $this->getVariation($variationId);
        $newVariationId = 0;
        if (!empty($variation)) {
            $data = [
                'product_id' => $productId,
                'user_id' => user()->id,
                'parent_id' => $variation->parent_id,
                'label_names' => $variation->label_names,
                'variation_type' => $variation->variation_type,
                'insert_type' => 'copy',
                'option_display_type' => $variation->option_display_type,
                'show_images_on_slider' => $variation->show_images_on_slider,
                'use_different_price' => $variation->use_different_price,
                'is_visible' => $variation->is_visible
            ];
            $this->builder->insert($data);
            $newVariationId = $this->db->insertID();
        }
        if (!empty($newVariationId)) {
            $options = $this->getVariationOptions($variationId);
            if (!empty($options)) {
                foreach ($options as $option) {
                    $data = [
                        'variation_id' => $newVariationId,
                        'parent_id' => $option->parent_id,
                        'option_names ' => $option->option_names,
                        'stock' => $option->stock,
                        'color' => $option->color,
                        'price' => 0,
                        'discount_rate' => 0,
                        'is_default' => $option->is_default,
                        'use_default_price' => 1,
                        'no_discount' => 1
                    ];
                    $this->builderVariationOptions->insert($data);
                }
            }
        }
    }

    //get variation by user id
    public function getVariationsByUserId($userId)
    {
        return $this->builder->where('user_id', clrNum($userId))->get()->getResult();
    }

    //get product variations
    public function getProductVariations($productId)
    {
        return $this->builder->where('product_id', clrNum($productId))->get()->getResult();
    }

    //is there variation uses different price
    public function isVariationsUseDifferentPrice($productId, $exceptId = null)
    {
        if (!empty($exceptId)) {
            $this->builder->where('id !=', clrNum($exceptId));
        }
        if (!empty($this->builder->where('product_id', clrNum($productId))->where('use_different_price = 1')->get()->getRow())) {
            return true;
        }
        return false;
    }

    //get product sub variation
    public function getProductSubVariation($parentId)
    {
        return $this->builder->where('parent_id', clrNum($parentId))->orderBy('id')->get()->getRow();
    }

    //get half width product variations
    public function getHalfWidthProductVariations($productId)
    {
        return $this->builder->where('product_id', clrNum($productId))->where("is_visible = 1 AND (variation_type = 'text' OR variation_type = 'number' OR variation_type = 'dropdown')")->orderBy('id')->get()->getResult();
    }

    //get full width product variations
    public function getFullWidthProductVariations($productId)
    {
        return $this->builder->where('product_id', clrNum($productId))->where("is_visible = 1 AND (variation_type = 'checkbox' OR variation_type = 'radio_button')")->orderBy('id')->get()->getResult();
    }

    //delete variation
    public function deleteVariation($id)
    {
        $variation = $this->getVariation($id);
        if (!empty($variation)) {
            if ($this->builder->where('id', $variation->id)->delete()) {
                $this->deleteVariationOptions($variation->id);
            }
        }
    }

    //add variation option
    public function addVariationOption($variationId)
    {
        $variation = $this->getVariation($variationId);
        if (!empty($variation)) {
            $arrayNames = array();
            foreach ($this->activeLanguages as $language) {
                $item = [
                    'lang_id' => $language->id,
                    'option_name' => inputPost('option_name_' . $language->id)
                ];
                array_push($arrayNames, $item);
            }
            $arrayNames = serialize($arrayNames);
            $data = [
                'variation_id' => $variationId,
                'parent_id' => inputPost('parent_id'),
                'option_names ' => $arrayNames,
                'stock' => inputPost('option_stock'),
                'color' => inputPost('option_color'),
                'price' => inputPost('option_price'),
                'discount_rate' => inputPost('option_discount_rate'),
                'is_default' => inputPost('is_default'),
                'use_default_price' => inputPost('use_default_price'),
                'no_discount' => inputPost('no_discount')
            ];
            if (!empty($data['price'])) {
                $data['price'] = getPrice($data['price'], 'database');
            } else {
                $data['price'] = 0;
            }
            if (empty($data['color'])) {
                $data['color'] = '';
            }
            if (empty($data['discount_rate'])) {
                $data['discount_rate'] = 0;
            }
            if ($data['discount_rate'] > 99) {
                $data['discount_rate'] = 99;
            }
            if (empty($data['use_default_price'])) {
                $data['use_default_price'] = 0;
            }
            if (empty($data['no_discount'])) {
                $data['no_discount'] = 0;
            }
            if (empty($data['parent_id'])) {
                $data['parent_id'] = 0;
            }
            if (empty($data['is_default'])) {
                $data['is_default'] = 0;
            } else {
                $data['price'] = 0;
                $data['discount_rate'] = 0;
            }
            if ($this->builderVariationOptions->insert($data)) {
                return $this->db->insertID();
            }
        }
        return false;
    }

    //edit variation option
    public function editVariationption($optionId)
    {
        $option = $this->getVariationOption($optionId);
        if (!empty($option)) {
            $variation = $this->getVariation($option->variation_id);
            if (!empty($variation)) {
                $arrayNames = array();
                foreach ($this->activeLanguages as $language) {
                    $item = [
                        'lang_id' => $language->id,
                        'option_name' => inputPost('option_name_' . $language->id)
                    ];
                    array_push($arrayNames, $item);
                }
                $arrayNames = serialize($arrayNames);
                $data = [
                    'parent_id' => inputPost('parent_id'),
                    'option_names ' => $arrayNames,
                    'stock' => inputPost('option_stock'),
                    'color' => inputPost('option_color'),
                    'price' => inputPost('option_price'),
                    'discount_rate' => inputPost('option_discount_rate'),
                    'is_default' => inputPost('is_default'),
                    'use_default_price' => inputPost('use_default_price'),
                    'no_discount' => inputPost('no_discount')
                ];
                if (!empty($data['price'])) {
                    $data['price'] = getPrice($data['price'], 'database');
                } else {
                    $data['price'] = 0;
                }
                if (empty($data['color'])) {
                    $data['color'] = '';
                }
                if (empty($data['discount_rate'])) {
                    $data['discount_rate'] = 0;
                }
                if ($data['discount_rate'] > 99) {
                    $data['discount_rate'] = 99;
                }
                if (empty($data['use_default_price'])) {
                    $data['use_default_price'] = 0;
                }
                if (empty($data['no_discount'])) {
                    $data['no_discount'] = 0;
                }
                if (empty($data['parent_id'])) {
                    $data['parent_id'] = 0;
                }
                if (empty($data['is_default'])) {
                    $data['is_default'] = 0;
                } else {
                    $data['price'] = 0;
                    $data['discount_rate'] = 0;
                }
                return $this->builderVariationOptions->where('id', clrNum($optionId))->update($data);
            }
        }
        return false;
    }

    //clear variation default option
    public function clearVariationDefaultOption($variationId, $optionId)
    {
        $option = $this->getVariationOption($optionId);
        if (!empty($option) && $option->is_default == 1) {
            return $this->builderVariationOptions->where('id != ', clrNum($optionId))->where('variation_id', clrNum($variationId))->update(['is_default' => 0]);
        }
    }

    //is variation option exist
    public function isVariationOptionExist($variationId, $optionId = null)
    {
        $arrayNames = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'option_name' => inputPost('option_name_' . $language->id)
            ];
            array_push($arrayNames, $item);
        }
        $arrayNames = serialize($arrayNames);
        if (!empty($optionId)) {
            $this->builderVariationOptions->where('id !=', clrNum($optionId));
        }
        $count = $this->builderVariationOptions->where('variation_id', clrNum($variationId))->where('option_names', $arrayNames)->countAllResults();
        if ($count > 0) {
            return true;
        }
        return false;
    }

    //get variation options
    public function getVariationOptions($variationId)
    {
        return $this->builderVariationOptions->where('variation_id', clrNum($variationId))->orderBy('id')->get()->getResult();
    }

    //get variation option
    public function getVariationOption($optionId)
    {
        return $this->builderVariationOptions->where('id', clrNum($optionId))->get()->getRow();
    }

    //get variation default option
    public function getVariationDefaultOption($variationId)
    {
        return $this->builderVariationOptions->where('variation_id', clrNum($variationId))->orderBy('is_default DESC, id')->get(1)->getRow();
    }

    //get variation sub options
    public function getVariationSubOptions($parentId)
    {
        return $this->builderVariationOptions->where('parent_id', clrNum($parentId))->orderBy('id')->get()->getResult();
    }

    //get variation option images
    public function getVariationOptionImages($optionId)
    {
        return $this->builderImagesVariation->where('variation_option_id', clrNum($optionId))->orderBy('is_main DESC')->get()->getResult();
    }

    //get variation option main image
    public function getVariationOptionMainImage($optionId)
    {
        return $this->builderImagesVariation->where('variation_option_id', clrNum($optionId))->orderBy('is_main DESC')->get(1)->getRow();
    }

    //upload variation image
    public function uploadVariationImage($productId, $variationOptionId)
    {
        $uploadModel = new UploadModel();
        $tempFile = $uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $data = [
                'product_id' => $productId,
                'variation_option_id' => $variationOptionId,
                'image_default' => $uploadModel->uploadProductDefaultImage($tempFile['path'], 'images'),
                'image_big' => $uploadModel->uploadProductBigImage($tempFile['path'], 'images'),
                'image_small' => $uploadModel->uploadProductVariationSmallImage($tempFile['path'], 'images'),
                'is_main' => 0,
                'storage' => 'local'
            ];
            $uploadModel->deleteTempFile($tempFile['path']);
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
            if ($this->builderImagesVariation->insert($data)) {
                echo json_encode(['image_id' => $this->db->insertID()]);
            }
        }
    }

    //upload variation images session
    public function uploadVariationImagesSession()
    {
        $uploadModel = new UploadModel();
        $tempFile = $uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $fileId = inputPost('file_id');
            $images = $this->getSessVariationImagesArray();
            if(empty($images)){
                $images = array();
            }
            $item = new \stdClass();
            $item->img_default = $uploadModel->uploadProductDefaultImage($tempFile['path'], 'temp');
            $item->img_big = $uploadModel->uploadProductBigImage($tempFile['path'], 'temp');
            $item->img_small = $uploadModel->uploadProductVariationSmallImage($tempFile['path'], 'temp');
            $item->file_id = $fileId;
            $item->is_main = 0;
            $item->file_time = time();
            array_push($images, $item);
            helperSetSession('mds_vr_images_array', $images);
            $uploadModel->deleteTempFile($tempFile['path']);
        }
    }

    //add variation images
    public function addVariationImages($productId, $variationOptionId)
    {
        $images = $this->getSessVariationImagesArray();
        if (!empty($images)) {
            foreach ($images as $image) {
                if (!empty($image)) {
                    $storage = 'local';
                    $uploadModel = new UploadModel();
                    $directory = $uploadModel->createUploadDirectory('images');
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
                        deleteFile("uploads/temp/" . $image->img_big);
                        copy(FCPATH . 'uploads/temp/' . $image->img_small, FCPATH . 'uploads/images/' . $directory . $image->img_small);
                        deleteFile("uploads/temp/" . $image->img_small);
                    }
                    //add to database
                    $data = [
                        'product_id' => $productId,
                        'variation_option_id' => $variationOptionId,
                        'image_default' => $directory . $image->img_default,
                        'image_big' => $directory . $image->img_big,
                        'image_small' => $directory . $image->img_small,
                        'is_main' => $image->is_main,
                        'storage' => $storage
                    ];
                    $this->builderImagesVariation->insert($data);
                }
            }
        }
        helperDeleteSession('mds_vr_images_array');
    }

    //get variation images array session
    public function getSessVariationImagesArray()
    {
        $images = array();
        $images = helperGetSession('mds_vr_images_array');
        if (!empty($images)) {
            usort($images, function ($a, $b) {
                if ($a->file_time == $b->file_time) return 0;
                return $a->file_time < $b->file_time ? 1 : -1;
            });
        }
        return $images;
    }

    //set variation image main session
    public function setSessVariationImageMain($fileId)
    {
        $images = $this->getSessVariationImagesArray();
        if (!empty($images)) {
            foreach ($images as $image) {
                if ($image->file_id == $fileId) {
                    $image->is_main = 1;
                } else {
                    $image->is_main = 0;
                }
            }
        }
        helperSetSession('mds_vr_images_array', $images);
    }

    //set variation image main
    public function setVariationImageMain($fileId, $variationOptionId)
    {
        $rows = $this->builderImagesVariation->where('variation_option_id', clrNum($variationOptionId))->get()->getResult();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if ($row->id == $fileId) {
                    $data['is_main'] = 1;
                } else {
                    $data['is_main'] = 0;
                }
                $this->builderImagesVariation->where('id', $row->id)->update($data);
            }
        }
    }

    //get variation image
    public function getVariationImage($id)
    {
        return $this->builderImagesVariation->where('id', clrNum($id))->get()->getRow();
    }

    //delete variation options
    public function deleteVariationOptions($variationId)
    {
        $options = $this->getVariationOptions($variationId);
        if (!empty($options)) {
            foreach ($options as $item) {
                $this->deleteVariationOption($item->id);
            }
        }
    }

    //delete variation option
    public function deleteVariationOption($optionId)
    {
        $option = $this->getVariationOption($optionId);
        if (!empty($option)) {
            if ($this->builderVariationOptions->where('id', $option->id)->delete()) {
                $images = $this->getVariationOptionImages($option->id);
                if (!empty($images)) {
                    foreach ($images as $image) {
                        $this->deleteVariationImage($image->id);
                    }
                }
            }
            return true;
        }
        return false;
    }

    //delete variation image session
    public function deleteVariationImageSession($fileId)
    {
        $images = $this->getSessVariationImagesArray();
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
        helperSetSession('mds_vr_images_array', $imagesNew);
    }

    //delete variation image
    public function deleteVariationImage($imageId)
    {
        $image = $this->getVariationImage($imageId);
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
            $this->builderImagesVariation->where('id', $image->id)->delete();
        }
    }

}