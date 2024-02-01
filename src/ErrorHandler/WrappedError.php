<?php
namespace Apie\HtmlBuilders\ErrorHandler;

use Apie\Core\Exceptions\HttpStatusCodeException;
use Apie\TypeConverter\Exceptions\GetMultipleChainedExceptionInterface;
use JsonSerializable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

final class WrappedError implements JsonSerializable
{
    public function __construct(public readonly Throwable $wrappedError)
    {
    }

    public function getStatusCode(): int
    {
        if ($this->wrappedError instanceof HttpStatusCodeException || $this->wrappedError instanceof HttpException) {
            return $this->wrappedError->getStatusCode();
        }

        return 500;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $trace = $this->getTrace();
        $data = [
            [
                'message' => $this->wrappedError->getMessage(),
                'code' => $this->wrappedError->getCode(),
                'class' => get_class($this->wrappedError),
                'trace' => $trace,
            ]
        ];
        $files = $trace->getFiles();
        if ($this->wrappedError instanceof GetMultipleChainedExceptionInterface) {
            foreach ($this->wrappedError->getChainedExceptions() as $exception) {
                $next = new WrappedError($exception);
                $json = $next->jsonSerialize();
                if (isset($json['exceptions'])) {
                    $data = [...$data, ...$json['exceptions']];
                }
                $files = array_merge($files, $json['files']);
            }
        } else if ($this->wrappedError->getPrevious()) {
            $previous = new WrappedError($this->wrappedError->getPrevious());
            $json = $previous->jsonSerialize();
            if (isset($json['exceptions'])) {
                $data = [...$data, ...$json['exceptions']];
            }
            $files = array_merge($files, $json['files']);
        }
        return [
            'exceptions' => $data,
            'files' => $files,
        ];
    }

    public function getTrace(): WrappedErrorTraceList
    {
        $stacktrace = $this->wrappedError->getTrace();
        $list = [];
        $list[] = WrappedErrorTrace::fromNative([
            'file' => $this->wrappedError->getFile(),
            'line' => $this->wrappedError->getLine(),
        ]);
        foreach ($stacktrace as $stacktraceItem) {
            $list[]= WrappedErrorTrace::fromNative($stacktraceItem);
        }
        return new WrappedErrorTraceList($list);
    }
}
