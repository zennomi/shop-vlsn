<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\VariationModel;

class VariationController extends BaseController
{
    protected $variationModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->variationModel = new VariationModel();
        if (!isVendor()) {
            redirectToUrl(langBaseUrl());
        }
    }

    /**
     * Add Variation Post
     */
    public function addVariationPost()
    {
        $productId = inputPost('product_id');
        $this->variationModel->addVariation();
        $this->productVariationListHtmlContent($productId, '_response_variations');
    }

    /**
     * Edit Variation
     */
    public function editVariation()
    {
        $id = inputPost('id');
        $variation = $this->variationModel->getVariation($id);
        if ($this->checkVariationPermission($variation)) {
            $this->productVariationHtmlContent($id, '_response_variation_edit');
        }
    }

    /**
     * Edit Variation Post
     */
    public function editVariationPost()
    {
        $variationId = inputPost('variation_id');
        $variation = $this->variationModel->getVariation($variationId);
        if ($this->checkVariationPermission($variation)) {
            $productId = inputPost('product_id');
            $this->variationModel->editVariation($variationId);
            $this->productVariationListHtmlContent($productId, '_response_variations');
        }
    }

    /**
     * Delete Variation Post
     */
    public function deleteVariationPost()
    {
        $id = inputPost('id');
        $variation = $this->variationModel->getVariation($id);
        if ($this->checkVariationPermission($variation)) {
            $this->variationModel->deleteVariation($id);
            $this->productVariationListHtmlContent($variation->product_id, '_response_variations');
        }
    }

    /**
     * Add Variation Option
     */
    public function addVariationOption()
    {
        $variationId = inputPost('variation_id');
        $variation = $this->variationModel->getVariation($variationId);
        if ($this->checkVariationPermission($variation)) {
            $this->productVariationHtmlContent($variationId, '_add_option');
        }
    }

    /**
     * Add Variation Option Post
     */
    public function addVariationOptionPost()
    {
        $variationId = inputPost('variation_id');
        $variation = $this->variationModel->getVariation($variationId);
        if ($this->checkVariationPermission($variation)) {
            if ($this->variationModel->isVariationOptionExist($variationId)) {
                setErrorMessage(trans("msg_option_exists"));
            } else {
                $variationOptionId = $this->variationModel->addVariationOption($variationId);
                if ($variationOptionId) {
                    $this->variationModel->addVariationImages($variation->product_id, $variationOptionId);
                    //clear default option
                    $this->variationModel->clearVariationDefaultOption($variationId, $variationOptionId);
                    setSuccessMessage(trans("msg_added"));
                } else {
                    setErrorMessage(trans("msg_error"));
                }
            }
            $this->productVariationHtmlContent($variationId, '_add_option');
        }
    }

    /**
     * View Variation Options
     */
    public function viewVariationOptions()
    {
        $variationId = inputPost('variation_id');
        $this->productVariationOptionsHtmlContent($variationId, '_options');
    }

    /**
     * Edit Variation Option
     */
    public function editVariationOption()
    {
        $variationId = inputPost('variation_id');
        $parentVariationOptions = null;
        $variation = $this->variationModel->getVariation($variationId);
        if ($this->checkVariationPermission($variation)) {
            if (!empty($variation) && $variation->parent_id != 0) {
                $parentVariationOptions = $this->variationModel->getVariationOptions($variation->parent_id);
            }
            $optionId = inputPost('option_id');
            $vars = [
                'variation' => $variation,
                'variationOption' => $this->variationModel->getVariationOption($optionId),
                'variationOptionImages' => $this->variationModel->getVariationOptionImages($optionId),
                'parentVariationOptions' => $parentVariationOptions,
            ];
            $htmlContent = view('dashboard/product/variation/_edit_option', $vars);
            $data = [
                'result' => 1,
                'htmlContent' => $htmlContent,
            ];
            echo json_encode($data);
        }
    }

    /**
     * Edit Variation Option Post
     */
    public function editVariationOptionPost()
    {
        $variationId = inputPost('variation_id');
        $parentVariationOptions = null;
        $variation = $this->variationModel->getVariation($variationId);
        if ($this->checkVariationPermission($variation)) {
            $optionId = inputPost('option_id');
            //check option exists
            if ($this->variationModel->isVariationOptionExist($variationId, $optionId)) {
                setErrorMessage(trans("msg_option_exists"));
            } elseif ($this->variationModel->editVariationption($optionId)) {
                //clear default option
                $this->variationModel->clearVariationDefaultOption($variationId, $optionId);
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
            if (!empty($variation) && $variation->parent_id != 0) {
                $parentVariationOptions = $this->variationModel->getVariationOptions($variation->parent_id);
            }
            $vars = [
                'variation' => $variation,
                'variationOption' => $this->variationModel->getVariationOption($optionId),
                'variationOptionImages' => $this->variationModel->getVariationOptionImages($optionId),
                'parentVariationOptions' => $parentVariationOptions
            ];
            $htmlContent = view('dashboard/product/variation/_edit_option', $vars);
            $data = [
                'result' => 1,
                'htmlContent' => $htmlContent,
            ];
            echo json_encode($data);
            resetFlashData();
        }
    }

    /**
     * Delete Variation Option
     */
    public function deleteVariationOptionPost()
    {
        $variationId = inputPost('variation_id');
        $variation = $this->variationModel->getVariation($variationId);
        if ($this->checkVariationPermission($variation)) {
            $optionId = inputPost('option_id');
            $this->variationModel->deleteVariationOption($optionId);
            $this->productVariationOptionsHtmlContent($variationId, '_options');
        }
    }

    /**
     * Select Variation
     */
    public function selectVariationPost()
    {
        $variationId = inputPost('variation_id');
        $variation = $this->variationModel->getVariation($variationId);
        if ($this->checkVariationPermission($variation)) {
            $productId = inputPost('product_id');
            $this->variationModel->selectVariation($variationId, $productId);
            $this->productVariationListHtmlContent($productId, '_response_variations');
        }
    }

    /**
     * Upload Variation Image
     */
    public function uploadVariationImage()
    {
        $variationOptionId = inputPost('variation_option_id');
        $variationOption = $this->variationModel->getVariationOption($variationOptionId);
        if (!empty($variationOption)) {
            $variation = $this->variationModel->getVariation($variationOption->variation_id);
            if ($this->checkVariationPermission($variation)) {
                $this->variationModel->uploadVariationImage($variation->product_id, $variationOption->id);
            }
        }
    }

    /**
     * Upload Variation Image Session
     */
    public function uploadVariationImageSession()
    {
        $this->variationModel->uploadVariationImagesSession();
    }

    /**
     * Get Uploaded Variation Image Session
     */
    public function getSessUploadedVariationImage()
    {
        $fileId = inputPost('file_id');
        $images = $this->variationModel->getSessVariationImagesArray();
        if (!empty($images)) {
            foreach ($images as $image) {
                if ($image->file_id == $fileId) {
                    echo '<img src="' . base_url() . "/uploads/temp/" . $image->img_default . '" alt="">' .
                        '<a href="javascript:void(0)" class="btn-img-delete btn-delete-variation-image-session" data-file-id="' . $image->file_id . '"><i class="icon-close"></i></a>' .
                        '<a href="javascript:void(0)" class="btn btn-xs btn-secondary btn-is-image-main btn-set-variation-image-main-session" data-file-id="' . $image->file_id . '">' . trans("main") . '</a>';
                    break;
                }
            }
        }
    }

    /**
     * Set Main Image Session
     */
    public function setVariationImageMainSession()
    {
        $fileId = inputPost('file_id');
        $this->variationModel->setSessVariationImageMain($fileId);
    }

    /**
     * Set Main Image
     */
    public function setVariationImageMain()
    {
        $fileId = inputPost('file_id');
        $variationOptionId = inputPost('option_id');
        $this->variationModel->setVariationImageMain($fileId, $variationOptionId);
    }

    /**
     * Delete Variation Image Session
     */
    public function deleteVariationImageSessionPost()
    {
        $fileId = inputPost('file_id');
        $this->variationModel->deleteVariationImageSession($fileId);
    }

    /**
     * Delete Variation Image
     */
    public function deleteVariationImagePost()
    {
        $variationId = inputPost('variation_id');
        $variation = $this->variationModel->getVariation($variationId);
        if ($this->checkVariationPermission($variation)) {
            $imageId = inputPost('image_id');
            $this->variationModel->deleteVariationImage($imageId);
        }
    }

    /**
     * Get Uploaded Variation Image
     */
    public function getUploadedVariationImage()
    {
        $imageId = inputPost('image_id');
        $image = $this->variationModel->getVariationImage($imageId);
        if (!empty($image)) {
            $option = $this->variationModel->getVariationOption($image->variation_option_id);
            if (!empty($option)) {
                echo '<div id="uploaded_vr_img_' . $image->id . '"><img src="' . getVariationOptionImageUrl($image) . '" alt="">' .
                    '<a href="javascript:void(0)" class="btn-img-delete btn-delete-variation-image" data-variation-id="' . $option->variation_id . '" data-file-id="' . $image->id . '"><i class="icon-close"></i></a>' .
                    '<a href="javascript:void(0)" class="btn btn-xs btn-secondary btn-is-image-main btn-set-variation-image-main" data-file-id="' . $image->id . '" data-option-id="' . $image->variation_option_id . '">' . trans("main") . '</a></div>';
            }
        }
    }

    //check variation permission
    private function checkVariationPermission($variation)
    {
        if (authCheck() && !empty($variation)) {
            if ($variation->user_id == user()->id || hasPermission('products')) {
                return true;
            }
        }
        return false;
    }

    //product variation list html content
    private function productVariationListHtmlContent($productId, $view)
    {
        $vars = [
            'productVariations' => $this->variationModel->getProductVariations($productId)
        ];
        $htmlContent = view('dashboard/product/variation/' . $view, $vars);
        $data = [
            'result' => 1,
            'htmlContent' => $htmlContent,
        ];
        echo json_encode($data);
        resetFlashData();
    }

    //product variation html content
    private function productVariationHtmlContent($variationId, $view)
    {
        $variation = $this->variationModel->getVariation($variationId);
        $productVariations = null;
        $parentVariationOptions = null;
        $product = null;
        if (!empty($variation)) {
            $product = getProduct($variation->product_id);
            $productVariations = $this->variationModel->getProductVariations($variation->product_id);
            if ($variation->parent_id != 0) {
                $parentVariationOptions = $this->variationModel->getVariationOptions($variation->parent_id);
            }
        }
        $vars = [
            'variation' => $variation,
            'productVariations' => $productVariations,
            'parentVariationOptions' => $parentVariationOptions,
            'product' => $product
        ];
        $htmlContent = view('dashboard/product/variation/' . $view, $vars);
        echo json_encode(['result' => 1, 'htmlContent' => $htmlContent]);
        resetFlashData();
    }

    //product variation options html content
    private function productVariationOptionsHtmlContent($variationId, $view)
    {
        $variation = $this->variationModel->getVariation($variationId);
        if (!empty($variation)) {
            $vars = [
                'variation' => $variation,
                'variationOptions' => $this->variationModel->getVariationOptions($variationId),
                'product' => getProduct($variation->product_id)
            ];
            $htmlContent = view('dashboard/product/variation/' . $view, $vars);
            echo json_encode(['result' => 1, 'htmlContent' => $htmlContent]);
        } else {
            echo json_encode(['result' => 0, 'htmlContent' => '']);
        }
        resetFlashData();
    }

}
