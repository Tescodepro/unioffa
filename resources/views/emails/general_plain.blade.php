{{ $content['title'] ?? '' }}

{{ $content['body'] ?? '' }}

@if(!empty($content['footer']))
------------------
{{ $content['footer'] }}
@endif
