<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppSubscriber implements EventSubscriberInterface
{
    protected $container;

    public function __construct(ContainerInterface $container) // this is @service_container
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            EasyAdminEvents::PRE_LIST => 'checkUserRights',
            EasyAdminEvents::PRE_EDIT => 'checkUserRights'
        );
    }

    /**
     * show an error if user is not superadmin and tries to manage restricted stuff
     *
     * @param GenericEvent $event event
     * @return null
     * @throws AccessDeniedException
     */
    public function checkUserRights(GenericEvent $event)
    {
        $entity = $this->container->get('request_stack')->getCurrentRequest()->query->get('entity');
        $action = $this->container->get('request_stack')->getCurrentRequest()->query->get('action');
        $user_id = $this->container->get('request_stack')->getCurrentRequest()->query->get('id');
        // if user management
        if ($entity == 'User') {
            // if not admin throw error
            if (!$this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
                throw new AccessDeniedException();
            }

        }


//        $user_id = $event->getAdmin()->getRequest()->attributes->all()['id'];
//        // we can get container from ->getAdmin()->getConfigurationPool()->getContainer()
//        $session_id = $event->getAdmin()->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser()->getId();
//        if ($user_id != $session_id) {
//            throw new AccessDeniedException();
//        }
    }

}