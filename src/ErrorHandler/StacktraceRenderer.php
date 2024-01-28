<?php
namespace Apie\HtmlBuilders\ErrorHandler;

use Stringable;
use Throwable;

final class StacktraceRenderer implements Stringable
{
    private const DEFAULT_CDN = 'https://unpkg.com/apie-stacktrace@0.1.5/dist/apie-stacktrace/apie-stacktrace.esm.js';
    private const DEFAULT_STYLE_CDN = 'https://unpkg.com/apie-stacktrace@0.1.5/dist/apie-stacktrace/apie-stacktrace.css';

    public function __construct(
        private readonly Throwable $error,
        private readonly string $cdn = self::DEFAULT_CDN,
        private readonly string $styleCdn = self::DEFAULT_STYLE_CDN,
    ) {
    }

    public function __toString(): string
    {
        $loadCdnScript = '<script type="module" src="' . htmlentities($this->cdn) . '"></script>';
        $loadCdnScript .= '<link rel="stylesheet" href="' . htmlentities($this->styleCdn) . '" />';
        $wrapped = new WrappedError($this->error);
        $data = $wrapped->jsonSerialize();
        // json_encode is almost XSS free.
        // In old Firefox browsers it's possible to enter </script><div onload=" to have an XSS.
        $setters = "elm.exceptions = " . preg_replace('#</script#i', '&lt;/script', json_encode($data['exceptions'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . ';' . PHP_EOL;
        $templates = "";
        foreach ($data['files'] ?? [] as $fileName => $contents) {
            $templates .= sprintf(
                '<template type="apie/stacktrace-source" id="%s">%s</template>',
                htmlentities($fileName),
                htmlentities($contents),
            );
        }

        return sprintf(
            '%s
%s
<apie-stacktrace class="stacktrace-unhandled" php-version="%s"></apie-stacktrace>
<script>
(function(elm) {
    elm.classList.remove("stacktrace-unhandled");
    %s
}(document.querySelector("apie-stacktrace.stacktrace-unhandled")));
</script>',
            $templates,
            $loadCdnScript,
            PHP_VERSION,
            $setters
        );
    }
}
