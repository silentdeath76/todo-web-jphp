<?php

namespace routes\account;

use core\auth\PasswordHasher;
use entities\account\AccountData;
use entities\account\AccountDraftData;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class CreateAccount extends AbstractRoute
{
    const USER_ROLE_DEFAULT = 0;
    const USER_ROLE_ADMIN = 1;

    public function getPath(): string { return "/account/create"; }
    public function getMethod(): string { return "POST"; }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $body = $request->bodyStream()->readFully();
        $params = json_decode($body, true);
        if (!is_array($params)) {
            $params = $request->queryParameters();
        }

        $username = trim($params["username"] ?? "");
        $password = (string)($params["password"] ?? "");
        $email    = (string)($params["email"] ?? "");

        $response->header("Content-Type", "application/json");

        if ($username === "" || $password === "") {
            $response->status(400);
            $response->body(json_encode(["status" => "error", "message" => "username and password are required"]));
            return;
        }

        $accountDraft = new AccountDraftData(
            $username,
            PasswordHasher::hash($password),
            $email,
            self::USER_ROLE_DEFAULT
        );

        $account = $this->repository->create($accountDraft);

        if ($account instanceof AccountData) {
            $response->body(json_encode(["status" => "ok", "id" => $account->id]));
        } else {
            $response->body(json_encode(["status" => "error", "message" => "Ошибка создания аккаунта"]));
        }
    }
}