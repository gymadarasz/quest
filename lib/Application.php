<?php

namespace Madlib;

class Application
{
    protected Config $config;
    protected Request $request;
    protected Factory $factory;
    protected Session $session;
    protected Redirect $redirect;
    protected Code $code;
    protected Message $message;

    public function __construct(
        Config $config,
        Request $request,
        Factory $factory,
        Session $session,
        Redirect $redirect,
        Code $code,
        Message $message
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->factory = $factory;
        $this->session = $session;
        $this->redirect = $redirect;
        $this->code = $code;
        $this->message = $message;
    }

    public function process(): void
    {
        try {
            $method = $this->request->getMethod();

            $session_csrf = $this->session->get('csrf') ?? '';
            $request_csrf = $this->request->get('csrf') ?? '';
            if ($method == 'POST' && $session_csrf && $request_csrf && $session_csrf != $request_csrf) {
                throw new Exception('CSRF mismatch');
            }
            $this->session->set('csrf', $this->code->generate(40));

            $route = $this->request->getRoute();
            $area = 'public';
            if ($this->session->get('user_id')) {
                if ($this->session->get('user_admin')) {
                    if (isset($this->config::ROUTES['private'][$method][$route])) {
                        $area = 'private';
                    } elseif (isset($this->config::ROUTES['protected'][$method][$route])) {
                        $area = 'protected';
                    }
                } elseif (!$this->session->get('user_admin')) {
                    if (isset($this->config::ROUTES['protected'][$method][$route])) {
                        $area = 'protected';
                    }
                }
            }
            
            if ($area === 'protected' && !$this->session->get('user_subscribed') && !in_array($route, [
                'home', 
                'logout',
                'api/paypal-payment/create-payment',
                'api/paypal-payment/execute-payment',
            ], true)) {
                $this->redirect->go('home');
                return;
            }

            $class = $this->config::ROUTES[$area][$method][$route][0] ?? null;
            $func = $this->config::ROUTES[$area][$method][$route][1] ?? null;
            if (!$class || !$func) {
                switch ($area) {
                    case 'public':
                        if (
                            $this->config::ROUTES['protected'][$method][$route][0] ?? null &&
                            $this->config::ROUTES['protected'][$method][$route][1] ?? null
                        ) {
                            $this->session->set('redirect', $this->request->getServer('REQUEST_URI'));
                            $this->redirect->go('login');
                            return;
                        }
                        break;
                    case 'protected':
                        if (
                            $this->config::ROUTES['public'][$method][$route][0] ?? null &&
                            $this->config::ROUTES['public'][$method][$route][1] ?? null
                        ) {
                            $this->session->destroy();
                            $this->redirect->go($this->request->getServer('REQUEST_URI'));
                            return;
                        }
                        break;
                    default:
                        throw new Exception('Invalid area');
                }
                throw new Exception("Invalid route: [$method:$route]");
            }
            $this->factory->getInstance($class)->$func(...$this->config::ROUTES[$area][$method][$route][2] ?? []);
        } catch (MysqlException $e) {
            $this->message->error("Database error");
            $message = $e->getMessage();
            $debug = $message . " - " . $e->getAsString();
            trigger_error($debug);
            $this->message->debug($debug);
            $this->redirect->go('error-page');
        } catch (Exception $e) {
            $this->message->error($e->getMessage());
            $debug = $e->getAsString();
            trigger_error($debug);
            $this->message->debug($debug);
            $this->redirect->go('error-page');
        }
    }
}
