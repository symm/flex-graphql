<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Controller\GraphQLController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GraphQLPlaygroundSubscriber implements EventSubscriberInterface
{

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request    = $event->getRequest();
        if ($controller[0] instanceof GraphQLController && $this->shouldLoad($request)) {
            $event->setController(function () use ($controller) {
                $content = file_get_contents(__DIR__ . '/../Resources/playground.html');
                return new Response($content);
            });
        }
    }

    private function shouldLoad(Request $request)
    {
        return $request->getMethod() === 'GET' && $request->query->count() === 0;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}