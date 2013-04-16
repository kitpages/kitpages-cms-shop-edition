<?php

namespace App\LanguageBundle\Service;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class LanguageManager {

    protected $languageList = array();
    protected $localeList = array();

    public function __construct($localeList)
    {
        $this->localeList = $localeList;
        $this->languageList = array_flip($localeList);
    }

    public function getLocaleList() {
        return $this->localeList;
    }

    public function getLanguageList() {
        return $this->languageList;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }
        $request = $event->getRequest();
        $session = $request->getSession();
        $attributes = $request->attributes;
        $lang = $attributes->get('lang', null);

        if(in_array($lang, $this->localeList)) {
            $request->setLocale($lang);
        }
        if (!in_array($request->getLocale(), $this->localeList)) {
            $request->setLocale($request->getPreferredLanguage(array_values($this->languageList)));
        }


        setlocale(LC_TIME, $this->localeList[$request->getLocale()]);
    }
}

?>
