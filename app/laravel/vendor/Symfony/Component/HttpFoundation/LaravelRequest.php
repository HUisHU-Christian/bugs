<?php namespace Symfony\Component\HttpFoundation;

class LaravelRequest extends Request {

    /**
     * Creates a new request with values from PHP's super globals.
     *
     * @return Request A new request
     *
     * @api
     */
    static public function createFromGlobals() {
        $request = new static($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
			$test1 = $request->server->get('CONTENT_TYPE') ?? 'rien';
			$test2 = $request->server->get('HTTP_CONTENT_TYPE') ?? 'rien';
        if ((0 === strpos($test1, 'application/x-www-form-urlencoded')
    		|| (0 === strpos($test2, 'application/x-www-form-urlencoded')))
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return $request;
    }

    /**
     * Get the root URL of the application.
     *
     * @return string
     */
    public function getRootUrl() {
        return $this->getScheme().'://'.$this->getHttpHost().$this->getBasePath();
    }

}