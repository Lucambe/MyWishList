<?php
namespace mywishlist\controllers;

class ErrorController {

    protected $view;

    public static $statuscode = [
        "HTTP_CONTINUE" => 100,
        "HTTP_SWITCHING_PROTOCOLS" => 101,
        "HTTP_PROCESSING" => 102,
        "HTTP_OK" => 200,
        "HTTP_CREATED" => 201,
        "HTTP_ACCEPTED" => 202,
        "HTTP_NONAUTHORITATIVE_INFORMATION" => 203,
        "HTTP_NO_CONTENT" => 204,
        "HTTP_RESET_CONTENT" => 205,
        "HTTP_PARTIAL_CONTENT" => 206,
        "HTTP_MULTI_STATUS" => 207,
        "HTTP_ALREADY_REPORTED" => 208,
        "HTTP_IM_USED" => 226,
        "HTTP_MULTIPLE_CHOICES" => 300,
        "HTTP_MOVED_PERMANENTLY" => 301,
        "HTTP_FOUND" => 302,
        "HTTP_SEE_OTHER" => 303,
        "HTTP_NOT_MODIFIED" => 304,
        "HTTP_USE_PROXY" => 305,
        "HTTP_UNUSED" => 306,
        "HTTP_TEMPORARY_REDIRECT" => 307,
        "HTTP_PERMANENT_REDIRECT" => 308,
        "HTTP_BAD_REQUEST" => 400,
        "HTTP_UNAUTHORIZED"  => 401,
        "HTTP_PAYMENT_REQUIRED" => 402,
        "HTTP_FORBIDDEN" => 403,
        "HTTP_NOT_FOUND" => 404,
        "HTTP_METHOD_NOT_ALLOWED" => 405,
        "HTTP_NOT_ACCEPTABLE" => 406,
        "HTTP_PROXY_AUTHENTICATION_REQUIRED" => 407,
        "HTTP_REQUEST_TIMEOUT" => 408,
        "HTTP_CONFLICT" => 409,
        "HTTP_GONE" => 410,
        "HTTP_LENGTH_REQUIRED" => 411,
        "HTTP_PRECONDITION_FAILED" => 412,
        "HTTP_REQUEST_ENTITY_TOO_LARGE" => 413,
        "HTTP_REQUEST_URI_TOO_LONG" => 414,
        "HTTP_UNSUPPORTED_MEDIA_TYPE" => 415,
        "HTTP_REQUESTED_RANGE_NOT_SATISFIABLE" => 416,
        "HTTP_EXPECTATION_FAILED" => 417,
        "HTTP_IM_A_TEAPOT" => 418,
        "HTTP_MISDIRECTED_REQUEST" => 421,
        "HTTP_UNPROCESSABLE_ENTITY" => 422,
        "HTTP_LOCKED" => 423,
        "HTTP_FAILED_DEPENDENCY" => 424,
        "HTTP_UPGRADE_REQUIRED" => 426,
        "HTTP_PRECONDITION_REQUIRED" => 428,
        "HTTP_TOO_MANY_REQUESTS" => 429,
        "HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE" => 431,
        "HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE" => 444,
        "HTTP_UNAVAILABLE_FOR_LEGAL_REASONS" => 451,
        "HTTP_CLIENT_CLOSED_REQUEST" => 499,
        "HTTP_INTERNAL_SERVER_ERROR" => 500,
        "HTTP_NOT_IMPLEMENTED" => 501,
        "HTTP_BAD_GATEWAY" => 502,
        "HTTP_SERVICE_UNAVAILABLE" => 503,
        "HTTP_GATEWAY_TIMEOUT" => 504,
        "HTTP_VERSION_NOT_SUPPORTED" => 505,
        "HTTP_VARIANT_ALSO_NEGOTIATES" => 506,
        "HTTP_INSUFFICIENT_STORAGE" => 507,
        "HTTP_LOOP_DETECTED" => 508,
        "HTTP_NOT_EXTENDED" => 510,
        "HTTP_NETWORK_AUTHENTICATION_REQUIRED" => 511,
        "HTTP_NETWORK_CONNECTION_TIMEOUT_ERROR" => 599
    ];

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function showError($request, $response, $args) {
        if(!in_array($args['n'], ErrorController::$statuscode)) {
            $args['n'] = 418;
        }

        $this->view->render($response, 'error.phtml', [
            "ERROR_CODE" => $args['n'],
            "ERROR_TEXT" => array_search($args['n'], ErrorController::$statuscode)
        ]);

        $response = $response->withStatus((int) $args['n']);
        return $response;
    }
}
