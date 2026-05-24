<?php

namespace App\Http\Middleware;

use App\Helpers\TranslationHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale(TranslationHelper::activeLocale());

        $response = $next($request);

        if ($this->shouldTranslateResponse($request, $response)) {
            $response->setContent(TranslationHelper::renderHtml($response->getContent()));
        }

        return $response;
    }

    protected function shouldTranslateResponse(Request $request, Response $response): bool
    {
        if ($request->is('admin/*', 'super-admin/*', 'staff/*', 'customer/*', 'receptionist/*')) {
            return false;
        }

        return method_exists($response, 'headers')
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            && method_exists($response, 'getContent')
            && method_exists($response, 'setContent');
    }
}
