<?php


use core\logger\Logger;
use php\http\{HttpServer, HttpServerRequest, HttpServerResponse};
use repository\account\AccountDBRepository;
use repository\card\CardDBRepository;
use routes\account\CreateAccount;
use routes\statics\StaticRoutes;
use repository\todo\{TodoDBRepository, ToDoMemoryRepository, ToDoRepository};
use routes\card\{CreateCard, DeleteCard, GetAllCard, GetByIdCard, UpdateCard};
use routes\todo\{CreateTodo, DeleteTodo, GetAllTodos, GetByIdTodos, GetToDoForCard, UpdateTodo};
use twig\{TwigEngine, TwigStreamLoader};

class App
{
    const APP_NAME = 'ToDo App';


    private $port = 80;

    /**
     * @var ToDoRepository
     */
    private $toDoRepository;


    private $cardRepository;

    private $accountRepository;

    /**
     * @var Routes
     */
    private $routes;
    /**
     * @var HttpServer
     */
    private $server;

    public function run()
    {
        $this->initRepositories();

        $this->routes = new Routes();

        $this->startServer();
    }

    private function startServer()
    {
        $this->server = new HttpServer($this->port);
        $this->server->setErrorHandler([$this, "errorHandler"]);
        $this->server->setRequestLogHandler([$this, "requestLogHandler"]);

        $this->routeRegister();

        Logger::info("Server started");
        Logger::info("Open in browser http://localhost" . (($this->port == 80) ? "" : $this->port));
        Logger::info("Press Ctrl+C to stop server");

        $this->server->runInBackground();

    }

    /**
     * @return void
     */
    public function routeRegister(): void
    {
        $this->routes->register(new StaticRoutes());

        // todos
        $this->routes->register(new GetAllTodos($this->toDoRepository));
        $this->routes->register(new GetByIdTodos($this->toDoRepository));
        $this->routes->register(new CreateTodo($this->toDoRepository));
        $this->routes->register(new UpdateTodo($this->toDoRepository));
        $this->routes->register(new DeleteTodo($this->toDoRepository));
        $this->routes->register(new GetToDoForCard($this->toDoRepository));

        // cards
        $this->routes->register(new CreateCard($this->cardRepository));
        $this->routes->register(new DeleteCard($this->cardRepository));
        $this->routes->register(new GetAllCard($this->cardRepository));
        $this->routes->register(new GetByIdCard($this->cardRepository));
        $this->routes->register(new UpdateCard($this->cardRepository));

        // Accounts
        $this->routes->register(new CreateAccount($this->accountRepository)); // todo test it
        /*$this->routes->register(new GetByUsernameAccount($this->accountRepository)); // todo test it
        $this->routes->register(new GetAllAccount($this->accountRepository)); // todo test it
        $this->routes->register(new UpdateAccount($this->accountRepository)); // todo test it*/


        $this->server->get('/', function (HttpServerRequest $request, HttpServerResponse $response) {
            $loader = new TwigStreamLoader();
            $twig = new TwigEngine($loader);
            $loader->setPrefix('res://view/');
            $loader->setSuffix('.twig');
            $response->body($twig->render('index', [
                'appName' => self::APP_NAME
            ]));
        });

        foreach ($this->routes->getAllRoutes() as $route) {
            $this->server->route($route->getMethod(), $route->getPath(), $route);
        }
    }

    /**
     * @return void
     */
    public function initRepositories(): void
    {
        $dbFile = "mysqli.db";

        $this->cardRepository = new CardDBRepository($dbFile);
        $this->toDoRepository = new TodoDBRepository($dbFile);
        $this->accountRepository = new AccountDBRepository($dbFile);
    }

    private function errorHandler(Throwable $err, HttpServerRequest $request, HttpServerResponse $response)
    {
        $msg = $err->getMessage();

        Logger::error(sprintf("'%s'; User: %s", $msg, $request->remoteUser()));

        $response->write(json_encode(['error' => $msg]));
    }

    private function requestLogHandler(HttpServerRequest $request)
    {
        echo "Request: " . $request->method() . " " . $request->path() . "\n";
    }
}