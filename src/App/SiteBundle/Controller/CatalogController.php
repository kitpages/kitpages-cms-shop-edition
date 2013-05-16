<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Kitpages\CmsBundle\Controller\Context;
use Kitpages\CmsBundle\Entity\NavPublish;
use Kitpages\CmsBundle\Entity\Page;
use Kitpages\CmsBundle\Entity\PagePublish;
use Kitpages\CmsBundle\Controller\NavController;

class CatalogController extends NavController
{

    public function categoryAction($categoryName)
    {

    }


    public function widgetProductListAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $context = $this->get('kitpages.cms.controller.context');
        $pageList = array();
        if ($context->getViewMode() == Context::VIEW_MODE_EDIT || $context->getViewMode() == Context::VIEW_MODE_PREVIEW) {
            $page = $em->getRepository('KitpagesCmsBundle:Page')->findOneBySlug($slug);
            if ($page != null) {
                $pageChildren = $em->getRepository('KitpagesCmsBundle:Page')->childrenOfDepth($page, 1);
                $cmsFileManager = $cmsFileManager = $this->get('kitpages.cms.manager.file');
                $dataInheritanceList = $this->container->getParameter('kitpages_cms.page.data_inheritance_list');
                foreach($pageChildren as $page) {
                    $dataRoot = $em->getRepository('KitpagesCmsBundle:Page')->getDataWithInheritance($page, $dataInheritanceList);
                    $description = '';
                    if (isset($dataRoot['description'])){
                        $description = $dataRoot['description'];
                    }
                    $pageList[] = array(
                        'menuTitle' => $page->getMenuTitle(),
                        'description' => $description,
                        'url' => $this->getPageLink($page),
                        'mediaList' => $cmsFileManager->mediaList($dataRoot, false)
                    );
                }
            }

        }

        elseif ($context->getViewMode() == Context::VIEW_MODE_PROD) {
            // calculate page
            $cacheManager = $this->get('kitpages.simple_cache');

            $myThis = $this;
            $response = $cacheManager->get(
                'app-site-category-'.$context->getViewMode()."-$slug",
                function() use ($myThis, $em, $slug ) {
                    $em = $myThis->getDoctrine()->getManager();
                    $context = $myThis->get('kitpages.cms.controller.context');

                    $page = $em->getRepository('KitpagesCmsBundle:Page')->findOneBySlug($slug);

                    if ($page != null) {
                        $pageChildren = $em->getRepository('KitpagesCmsBundle:Page')->childrenOfDepth($page, 1);
                        $cmsFileManager = $cmsFileManager = $this->get('kitpages.cms.manager.file');
                        $dataInheritanceList = $this->container->getParameter('kitpages_cms.page.data_inheritance_list');
                        foreach($pageChildren as $page) {
                            $dataRoot = $em->getRepository('KitpagesCmsBundle:Page')->getDataWithInheritance($page, $dataInheritanceList);
                            $pageList[] = array(
                                'menuTitle' => $page->getMenuTitle(),
                                'url' => $this->getPageLink($page),
                                'mediaList' => $cmsFileManager->mediaList($dataRoot, false)
                            );
                        }
                    }
                    return $myThis->render(
                        'AppSiteBundle:Catalog:widget-product-list.html.twig',
                        array(
                            'pageList' => $pageList,
                            'kitCmsViewMode' => $context->getViewMode(),
                            'kitpages_target' => $_SERVER["REQUEST_URI"]
                        )
                    );
                }
            );
            return $response;
        }
        return $this->render(
            'AppSiteBundle:Catalog:widget-product-list.html.twig',
            array(
                'pageList' => $pageList,
                'kitCmsViewMode' => $context->getViewMode(),
                'kitpages_target' => $_SERVER["REQUEST_URI"]
            )
        );
    }

    public function getPageLink($page) {
        $url = '';
        if ($page->getPageType() == 'link' ) {
            $url = $page->getLinkUrl();
            if ($page->getIsLinkUrlFirstChild()) {
                $em = $this->getDoctrine()->getManager();
                $pageChildren = $em->getRepository('KitpagesCmsBundle:Page')->children($page, true);
                if (count($pageChildren) > 0 && $pageChildren['0'] instanceof Page) {
                    $url = $this->getPageLink($pageChildren['0']);
                }
            }
        }
        if ($page->getPageType() == 'edito' ) {
            if ($page->getForcedUrl()) {
                $url = $this->getRequest()->getBaseUrl().$page->getForcedUrl();
            } else {
                $url = $this->generateUrl(
                    'kitpages_cms_page_view_lang',
                    array(
                        'id' => $page->getId(),
                        'lang' => $page->getLanguage(),
                        'urlTitle' => $page->getUrlTitle()
                    )
                );
            }
        }
        return $url;

    }

}
