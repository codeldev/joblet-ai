<x-layouts.error
    :code="$exception->getStatusCode()"
    :title="getErrorResponseTitle($exception->getStatusCode())"
    :description="getErrorResponseText($exception->getStatusCode(), $exception->getMessage())"
/>
