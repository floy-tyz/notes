<?php

namespace App\Infrastructure\Http\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestTransformerListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($this->isAvailable($request)) {

            $this->transform($request);
        }
    }

    private function isAvailable(Request $request): bool
    {
        return 'json' === $request->getContentType() && $request->getContent();
    }

    private function transform(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() === JSON_ERROR_NONE) {

            if (!is_array($data)) {

                $data = [];
            }
            else {

                array_walk_recursive($data, function (&$value) {

                    if (is_string($value) && strlen($value) === 0) $value = null;
                });
            }

            $request->request->replace($data);
        }
    }
}