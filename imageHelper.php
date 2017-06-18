<?php

class ImageAdd extends AdminImportController
{

        public function insertImageInPrestashop($id_product, $url, $name_photo)
        {
                $shops = Shop::getShops(true, null, true);
                $image = new ImageCore();
                $image->id_product = $id_product;
                $image->position = Image::getHighestPosition($id_product) + 1;
                $image->cover = true;
                $tmp = explode(".", $name_photo);
                $name_photo_product = "";
                $name_for_legend = "";
                if (count($tmp) == 1) {
                        $name_photo_product = trim($url) . $name_photo . ".jpg";
                        $name_for_legend = $name_photo . ".jpg";
                } else {
                        $name_photo_product = trim($url) . $name_photo;
                        $name_for_legend = $name_photo;
                }
                $image->legend = array('1' => trim($name_for_legend));
                if ($image->validateFields(false, true) === true && $image->validateFieldsLang(false, true) === true && $image->add()) {
                        $image->associateTo($shops);
                        if (!$this->copyImg($id_product, $image->id, $name_photo_product, 'products')) {
                                $image->delete();
                        }
                }
                return $image->id;
        }
}

?>
