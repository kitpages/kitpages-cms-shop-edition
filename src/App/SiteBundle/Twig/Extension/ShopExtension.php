<?php
namespace App\SiteBundle\Twig\Extension;

class ShopExtension extends \Twig_Extension
{

    public static function priceFormat($price) {
        //return '';
        return number_format($price, 2, ',', ' ').' €';
    }

//    public static function countryName($value, $locale)
//    {
//        $countryList = \Symfony\Component\Locale\Locale::getDisplayCountries($locale);
//        if (!in_array($value, $countryList)) {
//            return $countryList[$value];
//        } else {
//            return $value;
//        }
//    }

//    public static function fieldTranslationWithPrefix($field, $language)
//    {
//        return ProductForm::getFieldTranslationWithPrefix($language, $field);
//    }



    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'kit_site_shop_price' => new \Twig_Filter_Function('App\SiteBundle\Twig\Extension\ShopExtension::priceFormat'),
//            'kit_catalog_country' => new \Twig_Filter_Function('App\CatalogBundle\Twig\Extension\CatalogExtension::countryName'),
//            'kit_catalog_fieldtranslation' => new \Twig_Filter_Function('App\CatalogBundle\Twig\Extension\CatalogExtension::fieldTranslationWithPrefix'),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kit_site_shop';
    }
}
