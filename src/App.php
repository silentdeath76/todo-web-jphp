<?php


use core\auth\Auth;
use core\logger\Logger;
use php\http\{HttpServer, HttpServerRequest, HttpServerResponse};
use repository\account\AccountDBRepository;
use repository\card\CardDBRepository;
use repository\session\SessionRepository;
use routes\account\CreateAccount;
use routes\account\LoginAccount;
use routes\account\LogoutAccount;
use routes\account\MeAccount;
use routes\statics\StaticRoutes;
use repository\task\{TaskDBRepository, TaskMemoryRepository, TaskRepository};
use routes\card\{CreateCard, DeleteCard, GetAllCard, GetByIdCard, UpdateCard};
use routes\task\{CreateTask, DeleteTask, GetAllTask, GetByIdTask, GetTaskForCard, UpdateTask};
use twig\{TwigEngine, TwigStreamLoader};

class App
{
    const APP_NAME = 'Task App';
    const PORT = 80;


    /**
     * @var TaskRepository
     */
    private $taskRepository;


    private $cardRepository;

    private $accountRepository;

    private $sessionRepository;

    /**
     * @var Routes
     */
    private $routes;
    /**
     * @var HttpServer
     */
    private $server;

    /**
     * @var Auth
     */
    private $auth;

    public function run()
    {
        $this->initRepositories();

        $this->routes = new Routes();

        $this->startServer();
    }

    private function startServer()
    {
        $this->server = new HttpServer(self::PORT);
        $this->server->setErrorHandler([$this, "errorHandler"]);
        $this->server->setRequestLogHandler([$this, "requestLogHandler"]);

        $this->routeRegister();

        Logger::info("Server started");
        Logger::info("Open in browser http://localhost" . ((self::PORT == 80) ? "" : ":" . self::PORT));
        Logger::info("Press Ctrl+C to stop server");

        $this->server->runInBackground();

    }

    /**
     * @return void
     */
    public function routeRegister(): void
    {
        $this->routes->register(new StaticRoutes());

        // accounts
        $this->routes->register(new CreateAccount($this->accountRepository));
        $this->routes->register(new LoginAccount($this->auth));
        $this->routes->register(new LogoutAccount($this->auth));
        $this->routes->register(new MeAccount($this->auth));

        // cards
        $this->routes->register(new CreateCard($this->cardRepository, $this->auth));
        $this->routes->register(new DeleteCard($this->cardRepository, $this->auth));
        $this->routes->register(new GetAllCard($this->cardRepository, $this->auth));   // ← строка 87
        $this->routes->register(new GetByIdCard($this->cardRepository, $this->auth));
        $this->routes->register(new UpdateCard($this->cardRepository, $this->auth));

        // tasks
        $this->routes->register(new GetAllTask($this->taskRepository, $this->auth));
        $this->routes->register(new GetByIdTask($this->taskRepository, $this->auth));
        $this->routes->register(new CreateTask($this->taskRepository, $this->auth));
        $this->routes->register(new UpdateTask($this->taskRepository, $this->auth));
        $this->routes->register(new DeleteTask($this->taskRepository, $this->auth));
        $this->routes->register(new GetTaskForCard($this->taskRepository, $this->auth));

        // ... twig и '/' без изменений

        $loader = new TwigStreamLoader();
        $twig = new TwigEngine($loader);
        $loader->setPrefix('res://view/');
        $loader->setSuffix('.twig');

        $auth = $this->auth;

        $this->server->get('/', function (HttpServerRequest $request, HttpServerResponse $response) use ($twig, $auth) {
            $user = $auth->user($request);


            $response->body($twig->render('index', [
                'appName'    => self::APP_NAME,
                'isLoggedIn' => $user !== null,
                'username'   => $user ? $user->username : '',
                'initial'    => $user ? strtoupper(substr($user->username, 0, 1)) : '',
            ]));
        });

        foreach ($this->routes->getAllRoutes() as $route) {
            // Logger::info("Registering: " . $route->getMethod() . " " . $route->getPath() . " (" . get_class($route) . ")");

            $method = strtoupper($route->getMethod());
            $path   = $route->getPath();

            switch ($method) {
                case 'GET':
                    $this->server->get($path, $route);
                    break;
                case 'POST':
                    $this->server->post($path, $route);
                    break;
                case 'PUT':
                    $this->server->put($path, $route);
                    break;
                case 'DELETE':
                    $this->server->delete($path, $route);
                    break;
                default:
                    $this->server->route($method, $path, $route);
            }
        }
    }

    /**
     * @return void
     */
    public function initRepositories(): void
    {
        $dbFile = "mysqli.db";

        $this->cardRepository    = new CardDBRepository($dbFile);
        $this->taskRepository    = new TaskDBRepository($dbFile);
        $this->accountRepository = new AccountDBRepository($dbFile);
        $this->sessionRepository = new SessionRepository($dbFile);

        $this->auth = new Auth($this->accountRepository, $this->sessionRepository);
    }

    private function errorHandler(Throwable $err, HttpServerRequest $request, HttpServerResponse $response)
    {
        Logger::error(sprintf("'%s'; User: %s", $err->getMessage(), $request->remoteUser()));

        $response
            ->status(500)
            ->write(json_encode(['error' => $err->getMessage()]));
    }

    private function requestLogHandler(HttpServerRequest $request)
    {
        Logger::info(sprintf("Request: %s %s", $request->method(), $request->path()));
    }
}