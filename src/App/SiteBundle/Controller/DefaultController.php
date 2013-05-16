<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kitpages\CmsBundle\Model\Paginator;
use App\SiteBundle\Form\ContactType;

class DefaultController extends Controller
{

    public function indexAction()
    {
        $currentLanguage = $this->get('request')->getLocale();
        return $this->redirect('/'.$currentLanguage);
    }


    /**
     * Displays a form to create a new Contact entity.
     *
     */
    public function contactAction()
    {

        $em = $this->getDoctrine()->getManager();
        $pageRepository = $em->getRepository('KitpagesCmsBundle:Page');
        $page = $pageRepository->findOneBySlug(
            $this->get('request')->getLocale().
                '-contact-form'
        );

        $form   = $this->createForm(new ContactType());

        return $this->render(
            'AppSiteBundle:Default:contact.html.twig',
            array(
                'form' => $form->createView(),
                'kitCmsPage' => $page,
                'kitMeta' => array(
                    'title' => 'Contact',
                    'description' => 'Contact',
                )
            )
        );
    }

    /**
     * Creates a new Contact entity.
     *
     */
    public function contactSendAction()
    {
        $request = $this->getRequest();
        $form    = $this->createForm(new ContactType());
        $form->bind($request);

        if ($form->isValid()) {
            $data = $form->getData();

            // mailer
            $mailer = $this->container->get('mailer');
            $message =
                "Contact from: ".$data['senderName'].
                    " (".$data['senderEmail'].")\n".
                    "Envoyer par le formulaire de contact\n".
                    "===============================================\n".
                    $data['message'];
            $mail = \Swift_Message::newInstance()
                ->setSubject('[Contact] ' . $data['subject'])
                ->setFrom($this->container->getParameter('email_contact'))
                ->setTo($this->container->getParameter('email_contact'))
                ->setBody($message);
            $mailer->send($mail);


            $this->get('session')->setFlash('notice', 'Your contact request was successfully sent');
            return $this->redirect($this->generateUrl('contact_form', array('lang'=>$this->get('request')->getLocale() )));

        }
        $this->get('session')->setFlash('notice', 'Erreur, Veuillez nous contactez par email.');
        return $this->redirect($this->generateUrl('contact_form', array('lang'=>$this->get('request')->getLocale() )));
    }

    public function newsAction($kitCmsBlockSlug)
    {
        $em = $this->getDoctrine()->getManager();
        $block = $em->getRepository('KitpagesCmsBundle:Block')->findOneBy(array('slug' => $kitCmsBlockSlug));
        $data = $block->getData();
        return $this->render(
            'AppSiteBundle:Default:news.html.twig',
            array(
                'kitCmsBlockSlug' => $kitCmsBlockSlug,
                'kitMeta' => array(
                    'title' => $data['root']['title'],
                    'description' => $data['root']['shortContent']
                )
            )
        );
    }

    public function newsListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pageRepository = $em->getRepository('KitpagesCmsBundle:Page');
        $page = $pageRepository->findOneBySlug(
            $this->get('request')->getLocale().
            '-news'
        );

        $paginator = new Paginator();
        $paginator->setCurrentPage( $this->get('request')->query->get('news_page', 1) );
        $paginator->setItemCountPerPage( 3 );
        $paginator->setUrlTemplate(
            $this->generateUrl(
                "news",
                array(
                    'news_page' => '_PAGE_',
                    'lang' => $this->get('request')->getLocale()
                )
            )
        );

        return $this->render(
            'AppSiteBundle:Default:news-list.html.twig',
            array(
                'paginator' => $paginator,
                'kitCmsPage' => $page,
                'kitMeta' => array(
                    'title' => $this->get('translator')->trans('Drumming news !'),
                    'description' => $this->get('translator')->trans('Latest drumming news !')
                )
            )
        );
    }


}
