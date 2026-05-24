@php
    $defaultMenuItems = collect([
        ['label' => 'Home', 'href' => route('home'), 'active' => Request::is('/')],
        ['label' => 'Rooms', 'href' => route('rooms'), 'active' => Request::is('rooms')],
        ['label' => 'Services', 'href' => route('services'), 'active' => Request::is('services')],
        ['label' => 'Gallery', 'href' => route('gallery'), 'active' => Request::is('gallery')],
        ['label' => 'FAQs', 'href' => route('faqs'), 'active' => Request::is('faqs')],
        ['label' => 'Blog', 'href' => route('blog.index'), 'active' => Request::is('blog*')],
        ['label' => 'About', 'href' => route('about'), 'active' => Request::is('about')],
        ['label' => 'Contact', 'href' => route('contact'), 'active' => Request::is('contact')],
    ]);

    $items = ($navMenuItems ?? collect())->isNotEmpty() ? $navMenuItems : $defaultMenuItems;
    $menuOffset = $menuOffset ?? 0;
    $menuLimit = $menuLimit ?? null;
    $includePluginLinks = $includePluginLinks ?? true;
    $items = $menuLimit === null ? $items->slice($menuOffset) : $items->slice($menuOffset, $menuLimit);
@endphp

@foreach($items as $item)
    @php
        if (is_array($item)) {
            $href = $item['href'];
            $label = $item['label'];
            $target = '_self';
            $active = $item['active'] ?? false;
        } else {
            $href = '#';
            if ($item->type === 'page' && $item->page) {
                $href = route('frontend.page', $item->page->slug);
            } elseif ($item->type === 'url') {
                $href = $item->url ?: '#';
            } elseif ($item->route_name && Route::has($item->route_name)) {
                $href = route($item->route_name);
            }
            $label = $item->label;
            $target = $item->target ?: '_self';
            $active = $item->type === 'url'
                ? Request::is(ltrim(parse_url($href, PHP_URL_PATH) ?: '', '/'))
                : url()->current() === url($href);
        }
    @endphp
    <a href="{{ $href }}" class="{{ $active ? 'active' : '' }}" target="{{ $target }}">{{ $label }}</a>
@endforeach

@if($includePluginLinks)
    @include('themes.partials.plugin-menu-links')
@endif
