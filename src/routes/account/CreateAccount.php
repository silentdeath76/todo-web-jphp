<?php

namespace routes\account;

use entities\account\AccountData;
use entities\account\AccountDraftData;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class CreateAccount extends AbstractRoute
{
    const USER_ROLE_DEFAULT = 0;
    const USER_ROLE_ADMIN = 1;

    public function getPath(): string
    {
        return "/account/create";
    }

    public function getMethod(): string
    {
        return "POST";
    }


    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $params = $request->queryParameters();
        $accountDraft = new AccountDraftData($params["username"], $params["password"], $params["email"], self::USER_ROLE_DEFAULT);

        $account = $this->repository->create($accountDraft);

        $response->header("Content Type", "application/json");
        if ($account instanceof AccountData) {
            $response->body(json_encode([
                "status" => "ok",
                "session" => md5(time() . $account->username . $account->password)
            ]));
        } else {
            if ($this->repository->get($params["username"]) instanceof AccountData) {
                $response->body(json_encode(["status" => "error", "message" => "Такой пользователь уже существует"]));
            } else {
                $response->body(json_encode(["status" => "error", "message" => "Ошибка создания аккаунта"]));
            }
        }
    }
}