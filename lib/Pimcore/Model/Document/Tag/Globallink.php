<?php

namespace Pimcore\Model\Document\Tag;

use Pimcore\Model;
use Pimcore\Tool;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;

class Globallink extends Model\Document\Tag\Link
{

    /**
     * Return the type of the element
     *
     * @return string
     */
    public function getType()
    {
        return "globallink";
    }

    /**
     *
     */
    protected function updatePathFromInternal()
    {
        if ($this->data["internal"])
        {
            if ($this->data["internalType"] == "document")
            {
                if ($doc = Document::getById($this->data["internalId"]))
                {
                    if (!Document::doHideUnpublished() || $doc->isPublished())
                    {
                        $path = $doc->getFullPath();

                        if( \Zend_Registry::isRegistered('Website_Country'))

                        {
                            $currentCountry = \Zend_Registry::get('Website_Country');
                            $validLanguages = Tool::getValidLanguages();

                            //its global
                            $currentIsoCode = NULL;

                            if( $currentCountry instanceof \CoreShop\Model\Country) {

                                $currentIsoCode = strtolower( $currentCountry->getIsoCode() );
                            }
                            else if( is_string( $currentCountry ) )
                            {
                                $currentIsoCode = $currentCountry;
                            }

                            $urlPath = parse_url($path, PHP_URL_PATH);
                            $urlPathFragments = explode('/', ltrim($urlPath, '/'));

                            //first needs to be country
                            $pathCountry = isset($urlPathFragments[0]) ? $urlPathFragments[0] : NULL;

                            //second needs to be language.
                            $pathLanguage = isset($urlPathFragments[1]) ? $urlPathFragments[1] : NULL;

                            $isValidLanguage = in_array($pathLanguage, $validLanguages);

                            //if 2. fragment is invalid language and 1. fragment is valid language, 1. fragment is missing!
                            $shiftCountry = $isValidLanguage == FALSE && in_array($pathCountry, $validLanguages);

                            //country is missing. add it.
                            if ($shiftCountry)
                            {
                                $path = '/' . $currentIsoCode . $path;
                            }
                            //if country is set, but in wrong context, replace it!
                            else if( $pathCountry !== $currentIsoCode )
                            {
                                $path = '/' . $currentIsoCode . str_replace($pathCountry . '/', '', $path);
                            }

                        }

                        $this->data["path"] = $path;

                    } else
                    {
                        $this->data["path"] = "";
                    }
                }
            }
            else if ($this->data["internalType"] == "asset")
            {
                if ($asset = Asset::getById($this->data["internalId"]))
                {
                    $this->data["path"] = $asset->getFullPath();
                }

            }

        }

    }

}